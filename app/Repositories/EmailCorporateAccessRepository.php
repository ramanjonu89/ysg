<?php
namespace App\Repositories;

use App\Models\EmailCorporateAccess;

class EmailCorporateAccessRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return EmailCorporateAccess::class;
    }

    /**
     * get list of Seller Corporate email
     * @param array $params
     * @return mixed
     */
    public function getList(array $params)
    {
        $sellerId = $params['sellerId'] ?? null;
        $emails = $params['emails'] ?? null;

        $query = $this->model::query();

        if ($sellerId != null) {
            $query->where('seller_id', $sellerId);
        }

        if ($emails) {
            $query->whereIn('email', $emails);
        }

        return $query->get();
    }

    public function getByEmail(string $email) {
        $query = $this->model::query();
        $query->where('email', '=', $email);
        return $query->get();
    }

    public function removeEmailAccess($sellerIds, $emails)
    {
        $this->model->whereIn('seller_id', $sellerIds)->whereIn('email', $emails)->delete();
    }

    public function addEmailAccess($sellerIds, $emails)
    {
        foreach ($sellerIds as $id) {
            foreach ($emails as $email) {
                $this->create([
                    'seller_id' => $id,
                    'email' => $email
                ]);
            }
        }
    }

    public function deleteBySellerId(int $sellerId): void
    {
        $this->model->where('seller_id', $sellerId)->delete();
    }
}
