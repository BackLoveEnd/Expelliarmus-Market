<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ProductDoesNotExistsInWishlistException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'This product does not exists in your wishlist.'], 409);
    }
}
