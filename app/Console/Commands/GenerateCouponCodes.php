<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Services\CouponService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GenerateCouponCodes extends Command
{
    protected $couponService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:generate-coupon-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate coupon codes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CouponService $couponService) {
        $this->couponService = $couponService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $listCouponCodes = [
                    'STAR34958',
                    'STAR67129',
                    'STAR53247',
                    'STAR81693',
                    'STAR25734',
                    'STAR92601',
                    'STAR40372',
                    'STAR18536',
                    'STAR79045',
                    'STAR62817',
                    'STAR93245',
                    'STAR41769',
                    'STAR58420',
                    'STAR10654',
                    'STAR72983',
                    'STAR31572',
                    'STAR89761',
                    'STAR24368',
                    'STAR98125',
                    'STAR43760',
                    'STAR61547',
                    'STAR93267',
                    'STAR47216',
                    'STAR15874',
                    'STAR80236',
                    'STAR53981',
                    'STAR92684',
                    'STAR68015',
                    'STAR26470',
                    'STAR73819',
                    'STAR34682',
                    'STAR81029',
                    'STAR52716',
                    'STAR96723',
                    'STAR41327',
                    'STAR58279',
                    'STAR39451',
                    'STAR17592',
                    'STAR63918',
                    'STAR27186',
                    'STAR92415',
                    'STAR31649',
                    'STAR78412',
                    'STAR50287',
                    'STAR65729',
                    'STAR84937',
                    'STAR23541',
                    'STAR87691',
                    'STAR42963',
                    'STAR71254',
                    'STAR35897',
                    'STAR91236',
                    'STAR57691',
                    'STAR69421',
                    'STAR12345',
                    'STAR75923',
                    'STAR32478',
                    'STAR89025',
                    'STAR46217',
                    'STAR63859',
                    'STAR15234',
                    'STAR80279',
                    'STAR53847',
                    'STAR92617',
                    'STAR67583',
                    'STAR21437',
                    'STAR96347',
                    'STAR41365',
                    'STAR59713',
                    'STAR35896',
                    'STAR72104',
                    'STAR31657',
                    'STAR81027',
                    'STAR43829',
                    'STAR57604',
                    'STAR98173',
                    'STAR42735',
                    'STAR67519',
                    'STAR83924',
                    'STAR12467',
                    'STAR75892',
                    'STAR32975',
                    'STAR86492',
                    'STAR51246',
                    'STAR70683',
                    'STAR29417',
                    'STAR94357',
                    'STAR63147',
                    'STAR28543',
                    'STAR91834',
                    'STAR48375',
                    'STAR65924',
                    'STAR17693',
                    'STAR72469',
                    'STAR32941',
                    'STAR89746',
                    'STAR52134',
                    'STAR96384',
                    'STAR41589',
                    'STAR75824',
                ];

        foreach ($listCouponCodes as $key => $couponCode) {
            try {
                $existedCouponCode = Coupon::where('code', $couponCode)->first();

                if (!$existedCouponCode) {
                    $couponInputs = $this->formatDataInputs($couponCode, $key);
                    $couponCreate = $this->couponService->create($couponInputs);
                    if ($couponCreate) {
                        $this->info('Coupon code ' . $couponCode. ' has been saved successfully');
                    } else {
                        $this->warning('Coupon code ' . $couponCode . ' generate failed');
                    }
                } else {
                    $this->error('Coupon code ' . $couponCode . ' already exists');
                }
            } catch (Exception $e) {
                $this->error('Key ' . $key . ' something went wrong');
            }
        }
    }

    private function formatDataInputs($couponCode, $key)
    {
        $start = Carbon::now()->format('Y-m-d');
        $end = '2024-02-29';
        $details = [
            'min_buy' => 0,
            'max_coupon' => 1,
            'max_discount' => 50,
            'sellerIds' => [],
        ];  

        $inputs['title']         = 'CAPITASTAR voucher 100% max $50 ' . $key;
        $inputs['published']     = 1;
        $inputs['type']          = Coupon::TYPE_CART_BASE;
        $inputs['code']          = $couponCode;
        $inputs['owner']         = Coupon::OWNER_YSG;
        $inputs['free_shipping'] = 0;
        $inputs['seller_id']     = 0;
        $inputs['details']       = $details;
        $inputs['discount']      = 100;
        $inputs['discount_type'] = Coupon::TYPE_PERCENTAGE;
        $inputs['start_date']    = $start;
        $inputs['end_date']      = $end;
        $inputs['is_active']     = 1;
        $inputs['only_once']     = 1;

        return $inputs;
    }
}
