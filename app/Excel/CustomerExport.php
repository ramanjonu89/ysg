<?php

namespace App\Excel;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CustomerExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting
{
    const MAX_RECORD = 3000;
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function collection()
    {
        $query = $this->buildQuery($this->params);

        $grouped = collect($query)->groupBy('user_id');

        $userIds = $grouped->keys();

        $orders = DB::table('orders')
            ->whereIn('user_id', $userIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        $orderIds = $orders->flatten()->pluck('id')->unique();
        $orderDetails = DB::table('order_details')
            ->whereIn('order_id', $orderIds)
            ->get()
            ->groupBy('order_id');

        $result = collect();

        foreach ($grouped as $userId => $items) {
            $userOrders = $orders[$userId] ?? collect();
            $latestOrder = $userOrders->first();

            foreach ($items as $index => $row) {

                $rowCopy = (object)[
                    'id' => $row->id,
                    'user_id' => $row->user_id,
                    'name' => $row->name,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'delivery_address' => $row->delivery_address,
                    'delivery_phone' => $row->delivery_phone,
                    'introduced_by' => $row->introduced_by,
                    'referral_code_register' => $row->referral_code_register,
                    'receive_special_offers' => $row->receive_special_offers,
                    'banned' => $row->banned,
                    'total_orders' => $row->total_orders,
                    'created_at' => $row->created_at,
                    'last_login' => $row->last_login,
                    'last_order_date' => optional($latestOrder)->created_at,
                    'last_payment_status' => optional($latestOrder)->payment_status,
                    'total_successful_orders' => $userOrders
                        ->filter(function ($order) use ($orderDetails) {
                            if ($order->payment_status !== Order::PAYMENT_STATUS_PAID) {
                                return false;
                            }

                            if (!isset($orderDetails[$order->id])) {
                                return false;
                            }

                            return $orderDetails[$order->id]
                                ->where('delivery_status', '!=', 'cancelled')
                                ->isNotEmpty();
                            })
                        ->count(),
                ];

                if ($index > 0) {
                    $rowCopy->id = null;
                    $rowCopy->user_id = null;
                    $rowCopy->name = null;
                    $rowCopy->email = null;
                    $rowCopy->phone = null;
                    $rowCopy->introduced_by = null;
                    $rowCopy->referral_code_register = null;
                    $rowCopy->receive_special_offers = null;
                    $rowCopy->banned = null;
                    $rowCopy->total_orders = null;
                    $rowCopy->created_at = null;
                    $rowCopy->last_login = null;
                    $rowCopy->last_order_date = null;
                    $rowCopy->last_payment_status = null;
                    $rowCopy->total_successful_orders = null;
                }

                $result->push($rowCopy);
            }
        }

        return $result->values();
    }

    private function buildQuery($params)
    {
        $query = DB::table('customers')
            ->select(
                'customers.id',
                'customers.user_id',
                'users.name',
                'users.email',
                'users.phone',
                'addresses.address as delivery_address',
                'addresses.phone as delivery_phone',
                'users.introduced_by',
                'users.referral_code_register',
                'users.receive_special_offers',
                'users.banned',
                'users.created_at',
                'users.last_login',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->leftJoin('addresses', 'users.id', '=', 'addresses.user_id')
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->whereNull('users.deleted_at');

        if (!empty($params['filters'])) {
            foreach ($params['filters'] as $filter) {
                if (empty($filter['keyword'])) continue;

                $column = $filter['column'] ?? 'name';
                $condition = $filter['condition'] ?? 'contains';
                $keyword = $filter['keyword'];

                // Filter user columns
                if (in_array($column, ['name','email','phone'])) {
                    $query->where(function($q) use ($column, $keyword, $condition) {
                        $col = "users.$column";
                        $this->applySearchCondition($q, $col, $keyword, $condition);
                    });
                }

                // Filter address columns
                if (in_array($column, ['delivery_address','delivery_contact'])) {
                    $query->where(function($q) use ($column, $keyword, $condition) {
                        if ($column === 'delivery_address') {
                            $this->applySearchCondition($q, 'addresses.address', $keyword, $condition);
                        } elseif ($column === 'delivery_contact') {
                            $this->applySearchCondition($q, 'addresses.phone', $keyword, $condition);
                        }
                    });
                }
            }
        }

        return $query
            ->groupBy(
                'customers.id',
                'customers.user_id',
                'users.name',
                'users.email',
                'users.phone',
                'addresses.address',
                'addresses.phone',
                'users.introduced_by',
                'users.referral_code_register',
                'users.receive_special_offers',
                'users.banned',
                'users.created_at'
            )
            ->orderBy('customers.user_id')
            ->orderBy('addresses.id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Customer ID',
            'User ID',
            'Name',
            'Email',
            'Phone',
            'Delivery Address',
            'Delivery Contact',
            'Introduced By',
            'Referral Code Register',
            'Receive Special Offers',
            'Banned',
            'Total Orders',
            'Created At',
            'Last Login At',
            'Last Order Date',
            'Payment Status',
            'Total Successful Orders',
        ];
    }

    public function map($entity): array
    {
        return [
            $entity->id,
            $entity->user_id,
            $entity->name,
            $entity->email,
            $entity->phone,
            $entity->delivery_address,
            '="' . $entity->delivery_phone . '"',
            $entity->introduced_by,
            $entity->referral_code_register,
            $this->getReceiveSpecialOffersStatus($entity->receive_special_offers),
            $this->getBannedStatus($entity->banned),
            $entity->total_orders,
            $entity->created_at,
            $entity->last_login,
            $entity->last_order_date,
            $entity->last_payment_status,
            $entity->total_successful_orders,
        ];
    }

    private function getReceiveSpecialOffersStatus($receiveSpecialOffers)
    {
        if ($receiveSpecialOffers === null) {
            return null;
        }

        return $receiveSpecialOffers ? 'Yes' : 'No';
    }

    private function getBannedStatus($banned)
    {
        if ($banned === null) {
            return null;
        }

        return $banned ? 'Yes' : 'No';
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_TEXT
        ];
    }

    private function applySearchCondition($query, $column, $value, $condition)
    {
        switch ($condition) {
            case 'contains':
                $query->where($column, 'like', "%{$value}%");
                break;
            case 'not_contains':
                $query->where($column, 'not like', "%{$value}%");
                break;
            case 'starts_with':
                $query->where($column, 'like', "{$value}%");
                break;
            case 'ends_with':
                $query->where($column, 'like', "%{$value}");
                break;
            case 'equals':
                $query->where($column, '=', $value);
                break;
            case 'not_equals':
                $query->where($column, '!=', $value);
                break;
        }
    }
}
