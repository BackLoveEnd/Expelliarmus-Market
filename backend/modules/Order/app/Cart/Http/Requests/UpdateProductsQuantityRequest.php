<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Modules\Order\Cart\Rules\CartExistsInDbRule;
use Modules\Order\Cart\Rules\CartExistsInSessionRule;

class UpdateProductsQuantityRequest extends JsonApiFormRequest
{
    public function jsonApiAttributeRules(): array
    {
        return [
            'items' => [
                'required',
                'array',
                $this->user() ? new CartExistsInDbRule() : new CartExistsInSessionRule($this->session()),
            ],
            'items.*.cart_id' => ['required', 'uuid'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.variation_id' => ['required', '']
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'items' => 'items',
            'items.*.cart_id' => 'cart',
            'items.*.quantity' => 'quantity',
        ];
    }
}