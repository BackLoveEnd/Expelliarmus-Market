<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Modules\Order\Cart\Rules\ProductCartRule;

class AddToCartRequest extends JsonApiFormRequest
{
    public function jsonApiAttributeRules(): array
    {
        return [
            'product_id' => ['required', 'integer'],
            'variation_id' => [
                'nullable',
                'integer',
                new ProductCartRule($this->product_id),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /*public function withValidator($validator)
    {
        $data = $validator->validate();

        $validator->after(function (Validator $validator) use ($data) {

        });
    }*/

    public function jsonApiCustomAttributes(): array
    {
        return [
            'product_id' => 'product',
            'variation_id' => 'variation',
            'quantity' => 'quantity',
        ];
    }
}
