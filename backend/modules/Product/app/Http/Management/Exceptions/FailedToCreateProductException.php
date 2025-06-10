<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class FailedToCreateProductException extends Exception
{
    protected ?Throwable $originalException;

    protected ?string $renderMessage;

    public function __construct(string $message, ?Throwable $exception = null, ?string $renderMessage = null)
    {
        parent::__construct($message);
        $this->originalException = $exception;
        $this->renderMessage = $renderMessage;
    }

    public function report(): bool
    {
        return false;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->renderMessage ?? 'Failed to create product. Try again or contact us.',
        ], 500);
    }
}
