<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class OrderOfSliderContentIsNotUniqueException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Order of slides is not unique. Check it out.']);
    }
}
