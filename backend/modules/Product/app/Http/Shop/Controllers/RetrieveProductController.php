<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\Product\Http\Shop\Actions\GetPublicProductInfoAction;
use Modules\Product\Http\Shop\Resources\ProductPublicInfoResource;

class RetrieveProductController extends Controller
{
    public function __construct(private GetPublicProductInfoAction $action) {}

    public function __invoke(ProductSlug $productBind)
    {
        $product = $this->action->handle($productBind->getProductId());

        return ProductPublicInfoResource::make($product);
    }
}