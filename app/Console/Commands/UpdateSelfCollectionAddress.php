<?php

namespace App\Console\Commands;

use App\Models\OrderDetail;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UpdateSelfCollectionAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:updarte_address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orderDetails = OrderDetail::where('shipping_type', 'self_collection')->get();
        foreach($orderDetails as $order) {
            $order->collection_address = $order->seller->shop->address;
            $order->save();
        }

        print ('ok');
    }
}
