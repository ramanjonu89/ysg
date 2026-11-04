<?php

namespace App\Console\Commands;

use App\Models\Product; // Replace with your model
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportToElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:elasticsearch {--chunk=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data to Elasticsearch using chunkById';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chunkSize = $this->option('chunk');

        $this->info('Starting import to Elasticsearch...');
        $this->info("Using chunk size: {$chunkSize}");

        // Process data in chunks
        Product::chunkById($chunkSize, function ($products) {
            foreach ($products as $product) {
                try {
                    $product->searchable(); // Laravel Scout method to send data to Elasticsearch
                } catch (\Exception $e) {
                    Log::error("Failed to index product ID {$product->id}: " . $e->getMessage());
                }
            }

            // Force garbage collection after processing each chunk
            gc_collect_cycles();
            $this->info('Processed chunk successfully.');
        });

        $this->info('Import completed.');
        return Command::SUCCESS;
    }
}
