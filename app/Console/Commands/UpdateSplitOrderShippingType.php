<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSplitOrderShippingType extends Command
{
    protected $signature = 'split-order:update-shipping-type {--dry-run : Show what would be updated without making changes}';
    protected $description = 'Update shipping_type in split_orders table from order_details';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Starting to update shipping_type in split_orders...');
        
        $query = "
            SELECT 
                so.id as split_order_id,
                so.shipping_type as current_shipping_type,
                od.shipping_type as new_shipping_type,
                COUNT(od.id) as order_detail_count
            FROM split_orders so
            LEFT JOIN order_details od ON od.split_order_id = so.id
            WHERE (so.shipping_type IS NULL OR so.shipping_type = '') 
            AND od.shipping_type IS NOT NULL
            GROUP BY so.id, so.shipping_type, od.shipping_type
        ";
        
        $results = DB::select($query);
        
        if (empty($results)) {
            $this->info('No split orders found that need shipping_type updates.');
            return;
        }
        
        $this->info("Found " . count($results) . " split orders to update:");
        
        $updateQuery = "
            UPDATE split_orders 
            SET shipping_type = (
                SELECT DISTINCT od.shipping_type 
                FROM order_details od 
                WHERE od.split_order_id = split_orders.id 
                AND od.shipping_type IS NOT NULL 
                LIMIT 1
            )
            WHERE (shipping_type IS NULL OR shipping_type = '')
            AND id IN (
                SELECT DISTINCT split_order_id 
                FROM order_details 
                WHERE split_order_id IS NOT NULL 
                AND shipping_type IS NOT NULL
            )
        ";
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            foreach ($results as $result) {
                $this->line("Split Order ID: {$result->split_order_id} | Current: '{$result->current_shipping_type}' | New: '{$result->new_shipping_type}' | Order Details: {$result->order_detail_count}");
            }
            $this->info('Would update ' . count($results) . ' split orders.');
        } else {
            $affectedRows = DB::update($updateQuery);
            $this->info("Successfully updated {$affectedRows} split orders.");
            
            $this->info('Verification - checking updated records:');
            $verificationQuery = "
                SELECT 
                    so.id,
                    so.shipping_type,
                    COUNT(od.id) as order_detail_count
                FROM split_orders so
                LEFT JOIN order_details od ON od.split_order_id = so.id
                WHERE so.shipping_type IS NOT NULL AND so.shipping_type != ''
                GROUP BY so.id, so.shipping_type
                ORDER BY so.id
                LIMIT 10
            ";
            
            $verificationResults = DB::select($verificationQuery);
            foreach ($verificationResults as $result) {
                $this->line("Split Order ID: {$result->id} | Shipping Type: '{$result->shipping_type}' | Order Details: {$result->order_detail_count}");
            }
        }
    }
}
