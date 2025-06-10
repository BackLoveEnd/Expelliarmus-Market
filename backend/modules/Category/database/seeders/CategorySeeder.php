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
                'name' => 'Electronics',
                'slug' => 'electronics',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Computers',
                        'slug' => 'computers',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Laptops',
                                'slug' => 'laptops',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Desktops',
                                'slug' => 'desktops',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Phones',
                        'slug' => 'phones',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Smartphones',
                                'slug' => 'smartphones',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Accessories',
                                'slug' => 'phone-accessories',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Home & Furniture',
                'slug' => 'home-furniture',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Living Room',
                        'slug' => 'living-room',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Sofas',
                                'slug' => 'sofas',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Coffee Tables',
                                'slug' => 'coffee-tables',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Bedroom',
                        'slug' => 'bedroom',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Beds',
                                'slug' => 'beds',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Wardrobes',
                                'slug' => 'wardrobes',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Cycling',
                        'slug' => 'cycling',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Bicycles',
                                'slug' => 'bicycles',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Accessories',
                                'slug' => 'cycling-accessories',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Camping',
                        'slug' => 'camping',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Tents',
                                'slug' => 'tents',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Sleeping Bags',
                                'slug' => 'sleeping-bags',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Books & Media',
                'slug' => 'books-media',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Books',
                        'slug' => 'books',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Fiction',
                                'slug' => 'fiction',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Non-Fiction',
                                'slug' => 'non-fiction',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Music',
                        'slug' => 'music',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Vinyl',
                                'slug' => 'vinyl',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'CDs',
                                'slug' => 'cds',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Beauty & Health',
                'slug' => 'beauty-health',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Makeup',
                        'slug' => 'makeup',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                    ],
                    [
                        'name' => 'Skincare',
                        'slug' => 'skincare',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                    ],
                ],
            ],
            [
                'name' => 'Toys & Games',
                'slug' => 'toys-games',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Board Games',
                        'slug' => 'board-games',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                    ],
                    [
                        'name' => 'Action Figures',
                        'slug' => 'action-figures',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                    ],
                ],
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Car Accessories',
                        'slug' => 'car-accessories',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                    ],
                    [
                        'name' => 'Motorcycles',
                        'slug' => 'motorcycles',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                    ],
                ],
            ],
            [
                'name' => 'Office Supplies',
                'slug' => 'office-supplies',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Notebooks',
                        'slug' => 'notebooks',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                    ],
                    [
                        'name' => 'Printers',
                        'slug' => 'printers',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                    ],
                ],
            ],
            [
                'name' => 'Food & Drinks',
                'slug' => 'food-and-drinks',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Drinks',
                        'slug' => 'drinks',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Alcohol',
                                'slug' => 'alcohol',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Soda',
                                'slug' => 'soda',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Juice',
                                'slug' => 'juices',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                ],
            ],
            [

                'name' => 'Clothes',
                'slug' => 'clothes',
                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                'children' => [
                    [
                        'name' => 'Man\'s Clothes',
                        'slug' => 'clothes',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Sweatshirts',
                                'slug' => 'sweatshirts',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Shoes',
                                'slug' => 'shoes',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                                'children' => [
                                    [
                                        'name' => 'Sneakers',
                                        'slug' => 'sneakers',
                                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                                    ],
                                    [
                                        'name' => 'Boots',
                                        'slug' => 'boots',
                                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Jeans',
                                'slug' => 'jeans',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Women\'s Clothes',
                        'slug' => 'womens-clothes',
                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                        'children' => [
                            [
                                'name' => 'Sweatshirts',
                                'slug' => 'womens-sweatshirts',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                            ],
                            [
                                'name' => 'Shoes',
                                'slug' => 'womens-shoes',
                                'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                                'children' => [
                                    [
                                        'name' => 'Sneakers',
                                        'slug' => 'womens-sneakers',
                                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                                    ],
                                    [
                                        'name' => 'Boots',
                                        'slug' => 'womens-boots',
                                        'icon_url' => 'https://dummyimage.com/100x100/000/fff',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
