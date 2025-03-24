<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Product\Http\Shop\Services\ProductsRetrieve\RetrieveProductsService;

class RetrieveProductsController extends Controller
{
    public function __construct(
        private RetrieveProductsService $productsService,
    ) {}

    public function index(Request $request)
    {
        $this->productsService->getProducts();
    }
}