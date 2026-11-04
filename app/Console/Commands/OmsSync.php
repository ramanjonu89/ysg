<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OmsService;

class OmsSync extends Command
{
    
    /**
     * @var OmsService
     *
     */
    protected $omsService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oms:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncing process from OMS to YSG';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OmsService $omsService)
    {
        parent::__construct();
        $this->omsService = $omsService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->omsService->runSync();
    }
}
