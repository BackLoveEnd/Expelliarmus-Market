<?php

return [
    'name' => 'Product',
    'cache' => [
        'root-category-children' => 'root:category:%s:children',
        'product-image-preview' => 'product:image:preview:%s',
        'product-images' => 'product:%s:images',
        'product-preview' => 'product:%s:preview',
        'products-explore' => 'products:explore',
        'products-top-sellers' => 'products:top-sellers',
        'product-public' => 'products:%s:public',
        'product-related-by-category' => 'products:categories:%s:related',
        'products-suggestions' => 'products:suggestions',
    ],
    'retrieve' => [
        'by_category' => 6,
        'trashed' => 16,
        'discounted' => 16,
        'flash-sales' => 5,
        'explore' => 10,
        'top-sellers' => 5,
        'suggestions' => 5,
        'shop' => [
            'default' => 20,
        ],
    ],
    'image' => [
        'preview' => [
            'size' => [
                'width' => 272,
                'height' => 262,
            ],
        ],
        'default' => 'product_preview.png',
        'default_preview' => 'preview_product_preview.png',
        'default_resized_preview' => '272_262_preview_product_preview.png',
    ],
];
