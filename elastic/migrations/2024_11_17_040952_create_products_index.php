<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateProductsIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        $mapping = [
            'properties' => [
                'id' => [
                    'type' => 'integer'
                ],
                'name' => [
                    'type' => 'text',
                    'analyzer' => 'standard'
                ],
                'added_by' => [
                    'type' => 'keyword'
                ],
                'user' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'name' => [
                            'type' => 'keyword'
                        ],
                        'deleted_at' => [
                            'type' => 'date',
                            'format' => 'strict_date_optional_time||epoch_millis'
                        ],
                        'company_type' => [
                            'type' => 'integer'
                        ]
                    ]
                ],
                'category' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'name' => [
                            'type' => 'keyword'
                        ],
                        'status' => [
                            'type' => 'boolean'
                        ],
                        'is_age_restricted' => [
                            'type' => 'integer'
                        ],
                    ]
                ],
                'subcategory' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'name' => [
                            'type' => 'keyword'
                        ],
                        'status' => [
                            'type' => 'boolean'
                        ]
                    ]
                ],
                'subsubcategory' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'name' => [
                            'type' => 'keyword'
                        ],
                        'status' => [
                            'type' => 'boolean'
                        ]
                    ]
                ],
                'brand' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'name' => [
                            'type' => 'keyword'
                        ],
                        'status' => [
                            'type' => 'boolean'
                        ]
                    ]
                ],
                'tags' => [
                    'type' => 'text',
                    'analyzer' => 'standard'
                ],
                'description' => [
                    'type' => 'text'
                ],
                'unit_price' => [
                    'type' => 'double'
                ],
                'min_price' => [
                    'type' => 'double'
                ],
                'min_final_price' => [
                    'type' => 'double'
                ],
                'max_price' => [
                    'type' => 'double'
                ],
                'max_final_price' => [
                    'type' => 'double'
                ],
                'purchase_price' => [
                    'type' => 'double'
                ],
                'variations' => [
                    'type' => 'text'
                ],
                'todays_deal' => [
                    'type' => 'boolean'
                ],
                'published' => [
                    'type' => 'boolean'
                ],
                'featured' => [
                    'type' => 'boolean'
                ],
                'current_stock' => [
                    'type' => 'integer'
                ],
                'unit' => [
                    'type' => 'keyword'
                ],
                'min_qty' => [
                    'type' => 'integer'
                ],
                'max_qty' => [
                    'type' => 'integer'
                ],
                'discount' => [
                    'type' => 'double'
                ],
                'discount_type' => [
                    'type' => 'keyword'
                ],
                'tax' => [
                    'type' => 'double'
                ],
                'tax_type' => [
                    'type' => 'keyword'
                ],
                'shipping_type' => [
                    'type' => 'keyword'
                ],
                'shipping_cost' => [
                    'type' => 'double'
                ],
                'shipping_cost_above' => [
                    'type' => 'double'
                ],
                'num_of_sale' => [
                    'type' => 'integer'
                ],
                'rating' => [
                    'type' => 'double'
                ],
                'digital' => [
                    'type' => 'integer'
                ],
                'file_name' => [
                    'type' => 'keyword'
                ],
                'file_path' => [
                    'type' => 'keyword'
                ],
                'featured_sorting' => [
                    'type' => 'integer'
                ],
                'frontend_sorting' => [
                    'type' => 'integer'
                ],
                'only_microsite' => [
                    'type' => 'boolean'
                ],
                // Adding product variants from product_stocks table
                'variants' => [
                    'type' => 'nested',
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'variant' => [
                            'type' => 'keyword'
                        ],
                        'sku' => [
                            'type' => 'keyword'
                        ],
                        'price' => [
                            'type' => 'double'
                        ],
                        'final_price' => [
                            'type' => 'double'
                        ],
                        'qty' => [
                            'type' => 'integer'
                        ],
                        'discount' => [
                            'type' => 'double'
                        ],
                        'discount_type' => [
                            'type' => 'keyword'
                        ],
                    ]
                ],
                'reviews' => [
                    'type' => 'nested',
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'rating' => [
                            'type' => 'integer'
                        ],
                        'status' => [
                            'type' => 'boolean'
                        ],
                    ]
                ]
            ]
        ];

        $settings = [
            'analysis' => [
                'normalizer' => [
                    'comma_split' => [
                        'type' => 'custom',
                        'char_filter' => ['comma'],
                        'filter' => ["lowercase", "asciifolding"]
                    ]
                ],
                'char_filter' => [
                    'comma' => [
                        'type' => 'pattern_replace',
                        'pattern' => ',',
                        'replacement' => ','
                    ]
                ]
            ]
        ];

        try {
            Index::createRaw('products', $mapping, $settings);    
        } catch(Exception $e) {
            var_dump($e->getMessage());
        }
        
        
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::drop('products');
    }
}
