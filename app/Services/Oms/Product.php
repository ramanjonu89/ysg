<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 22/05/2022
 * Time: 15:40
 */

namespace App\Services\Oms;

use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Services\ProductExport\Export;
use App\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class Product
 * @package App\Services\Oms
 * @property
 */
class Product extends AbstractService
{
    /**
     * @property UserRepository
     */
    protected $userRepo;

    /**
     * @property ProductRepository
     */
    protected $productRepo;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $time;

    /**
     * @var User
     */
    protected $currentUser;

    /**
     * @var Client
     */
    protected $client;

    protected $header;
    protected $body;
    protected $filePath;

    public function __construct(UserRepository $userRepo, ProductRepository $productRepo)
    {
        $this->userRepo = $userRepo;
        $this->productRepo = $productRepo;

        $this->client = new Client([
            'base_uri' => config('api.oms'),
            'http_errors' => false,
        ]);

        //$crescoAuthToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjQ3MzI1Njc4ODYsImlzcyI6Ik9NUyIsImF1ZCI6Ik9NUy1BdWRpZW5jZSJ9.T4nEOot8vLBq8mjELX05HL78kXfXGWkg6J_TWXPjwUM';

        parent::__construct();
    }

    private function setApiKey() {
        $this->header = [
            'Authorization' => 'Bearer '.$this->currentUser->api_key,
        ];
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    private function prepareData($product): array
    {
        $expiryDays = 365;

        $tags = str_replace(' ','_', $product->tags);
        $temp = [
            'Product Type' => 'Simple Product',
            'SKU' => $product->unit,
            'Name' => $product->name,
            'Brand' => '',
            'Category' => '',
            'Inventory Tracking' => 'YES',
            'Price' => $product->unit_price,
            'Publish Datetime' => $product->created_at->format('d-M-Y'),
            'Unpublish Datetime' => $product->created_at->addDays($expiryDays)->format('d-M-Y'),
            'Product Tag' => $tags,
            'Description' => $product->description,
            'Channels' => $product->user->seller->oms_channel_name,
            'Groups Products' => '',
            'Bundled Products' => '',
            'Is Digital Goods' => '0',
            'Is Track Inventory' => '0',
            'Is Use OMSVoucher' => '0',
            'Days To Expiry' => $expiryDays,
            'Status' => 'Active',
        ];

        if ($product->variant_product) {
            $data = [];
            $variants = $product->stocks;
            foreach ($variants as $variant) {
                $temp['SKU'] = $variant->sku;
                $temp['Price'] = $variant->price;

                $data[] = $temp;
            }

            return $data;
        } else {
            return [$temp];
        }
    }

    private function chunkRows(array $rows, int $chunkSize = 190): array
    {
        return array_chunk($rows, $chunkSize);
    }

    private function generateXlsl(array $productIds): array
    {
        $params = [
            [
                'column' => 'ID',
                'value' => $productIds,
            ]
        ];

        $products = $this->productRepo->getAllBy($params);

        $rows = [];
        $sellerId = null;
        foreach ($products as $product) {
            if ($sellerId === null) {
                $sellerId = $product->user_id ?? 'unknown';
            }
            $data = $this->prepareData($product);
            if (count($data)) {
                $rows = array_merge($rows, $data);
            }
        }

        $chunks = $this->chunkRows($rows);
        $filePaths = [];
        $folderPath = config('constant.export_oms_path');

        foreach ($chunks as $index => $chunk) {
            $filePath = $folderPath.'/'.time().rand(11111,99999).'_seller_'.$sellerId.'_sync_products_chunk_'.($index + 1).'.xlsx';
            Excel::store(new Export($chunk), $filePath);
            $filePaths[] = $filePath;
        }

        return $filePaths;
    }

    private function isUserValid(): bool
    {
        if ($this->currentUser->api_key) {
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    private function validate()
    {
        if (!$this->isUserValid()) {
            throw new Exception('User not valid');
        }
    }

    public function runSync($userId, array $productIds): array
    {
        $result = [
            'status' => false,
            'responseData' => [],
            'message' => '',
        ];

        try {
            $user = $this->userRepo->find($userId);
            $this->setCurrentUser($user);
            $this->validate();

            $responseData = $this->handleSync($productIds);

            $result = [
                'status' => true,
                'responseData' => $responseData,
                'message' => '',
            ];
        } catch (Exception $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function makeParams(string $filePath): array
    {
        if ($filePath == '') {
            throw new Exception('Can not generate Xlsl file');
        }

        $file = File::get(public_path($filePath));

        $this->setApiKey();

        return [
            'headers' => $this->header,
            'multipart' => [
                [
                    'name'     => 'requestid',
                    'contents' => 1,
                ],
                [
                    'name'     => 'feedtype',
                    'contents' => 'product',
                ],
                [
                    'name'     => 'postfile',
                    'contents' => $file,
                    'filename' => 'invoices.xlsx'
                ],
            ],
        ];
    }

    /**
     * @throws Exception
     */
    public function handleSync($productIds)
    {
        $filePaths = $this->generateXlsl($productIds);
        $allResults = [];
        $uri = '/v1/product/ysg-update';

        foreach ($filePaths as $index => $filePath) {
            try {
                $params = $this->makeParams($filePath);
                $res = $this->client->post($uri, $params);

                $responseJson = $res->getBody()->getContents();
                $responseData = json_decode($responseJson, true);

                if (is_array($responseData)) {
                    if ($responseData['messages'] == 'Success') {
                        $allResults[] = [
                            'chunk' => $index + 1,
                            'status' => 'success',
                            'results' => $responseData['results']
                        ];
                    } else {
                        $allResults[] = [
                            'chunk' => $index + 1,
                            'status' => 'failed',
                            'error' => $responseData['messages'] ?? 'Unknown error'
                        ];
                    }
                } else {
                    $allResults[] = [
                        'chunk' => $index + 1,
                        'status' => 'failed',
                        'error' => 'Invalid response format'
                    ];
                }
            } catch (Exception $e) {
                $allResults[] = [
                    'chunk' => $index + 1,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            } finally {
                File::delete($filePath);
            }
        }

        $successCount = count(array_filter($allResults, function($result) {
            return $result['status'] === 'success';
        }));

        if ($successCount === 0) {
            throw new Exception('All chunks failed to sync: ' . json_encode($allResults));
        }

        return [
            'total_chunks' => count($filePaths),
            'successful_chunks' => $successCount,
            'failed_chunks' => count($filePaths) - $successCount,
            'results' => $allResults
        ];
    }
}