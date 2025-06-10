<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotAddNotPublishedProductToWishlistException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Cannot add not published product to wishlist.'], 400);
    }
}
