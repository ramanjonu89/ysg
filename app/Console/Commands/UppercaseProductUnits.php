<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UppercaseProductUnits extends Command
{
    protected $signature = 'products:uppercase-units {--dry-run : Show what would be changed without making changes}';

    protected $description = 'Uppercase all product units and product stock SKUs in the database';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->line('');
        }

        DB::beginTransaction();
        
        try {
            $this->uppercaseProductUnits($isDryRun);
            $this->uppercaseProductStockSkus($isDryRun);
            
            if ($isDryRun) {
                DB::rollBack();
                $this->info('DRY RUN completed - No changes were made');
            } else {
                DB::commit();
                $this->info('All product units and stock SKUs have been uppercased successfully!');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error occurred: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function uppercaseProductUnits($isDryRun)
    {
        $this->info('Processing product units...');
        
        $products = DB::table('products')
            ->whereNotNull('unit')
            ->where('unit', '!=', '')
            ->whereRaw('BINARY unit != BINARY UPPER(unit)')
            ->get(['id', 'unit']);
            
        if ($products->count() === 0) {
            $this->line('No product units need to be updated.');
            return;
        }
        
        $this->line("Found {$products->count()} products with lowercase units:");
        
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();
        
        foreach ($products as $product) {
            $oldUnit = $product->unit;
            $newUnit = strtoupper($oldUnit);
            
            if ($isDryRun) {
                $this->line("Product ID {$product->id}: '{$oldUnit}' → '{$newUnit}'");
            } else {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['unit' => $newUnit]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line('');
        
        if (!$isDryRun) {
            $this->info("Updated {$products->count()} product units.");
        }
    }
    
    private function uppercaseProductStockSkus($isDryRun)
    {
        $this->info('Processing product stock SKUs...');
        
        $stocks = DB::table('product_stocks')
            ->whereNotNull('sku')
            ->where('sku', '!=', '')
            ->whereRaw('BINARY sku != BINARY UPPER(sku)')
            ->get(['id', 'sku', 'product_id']);
            
        if ($stocks->count() === 0) {
            $this->line('No product stock SKUs need to be updated.');
            return;
        }
        
        $this->line("Found {$stocks->count()} product stocks with lowercase SKUs:");
        
        $bar = $this->output->createProgressBar($stocks->count());
        $bar->start();
        
        foreach ($stocks as $stock) {
            $oldSku = $stock->sku;
            $newSku = strtoupper($oldSku);
            
            if ($isDryRun) {
                $this->line("Stock ID {$stock->id} (Product {$stock->product_id}): '{$oldSku}' → '{$newSku}'");
            } else {
                DB::table('product_stocks')
                    ->where('id', $stock->id)
                    ->update(['sku' => $newSku]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line('');
        
        if (!$isDryRun) {
            $this->info("Updated {$stocks->count()} product stock SKUs.");
        }
    }
}
