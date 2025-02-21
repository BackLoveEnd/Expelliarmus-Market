<?php

declare(strict_types=1);

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Food & Drinks',
                'slug' => 'food-and-drinks',
                'children' => [
                    [
                        'name' => 'Drinks',
                        'slug' => 'drinks',
                        'children' => [
                            [
                                'name' => 'Alcohol',
                                'slug' => 'alcohol'
                            ],
                            [
                                'name' => 'Soda',
                                'slug' => 'soda'
                            ],
                            [
                                'name' => 'Juice',
                                'slug' => 'juices'
                            ]
                        ]
                    ]
                ]
            ],
            [

                'name' => 'Clothes',
                'slug' => 'clothes',
                'children' => [
                    [
                        'name' => 'Man\'s Clothes',
                        'slug' => 'clothes',
                        'children' => [
                            [
                                'name' => 'Sweatshirts',
                                'slug' => 'sweatshirts',
                            ],
                            [
                                'name' => 'Shoes',
                                'slug' => 'shoes',
                                'children' => [
                                    [
                                        'name' => 'Sneakers',
                                        'slug' => 'sneakers'
                                    ],
                                    [
                                        'name' => 'Boots',
                                        'slug' => 'boots'
                                    ]
                                ]
                            ],
                            [
                                'name' => 'Jeans',
                                'slug' => 'jeans'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Women\'s Clothes',
                        'slug' => 'womens-clothes',
                        'children' => [
                            [
                                'name' => 'Sweatshirts',
                                'slug' => 'womens-sweatshirts'
                            ],
                            [
                                'name' => 'Shoes',
                                'slug' => 'womens-shoes',
                                'children' => [
                                    [
                                        'name' => 'Sneakers',
                                        'slug' => 'womens-sneakers'
                                    ],
                                    [
                                        'name' => 'Boots',
                                        'slug' => 'womens-boots'
                                    ]
                                ]
                            ],
                        ]
                    ],
                ],
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}