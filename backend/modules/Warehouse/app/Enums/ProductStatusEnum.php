<?php

namespace Modules\Warehouse\Enums;

use Exception;
use Modules\Product\Http\Management\Exceptions\CannotPublishTrashedProductException;
use Modules\Product\Http\Management\Exceptions\CannotUnpublishTrashedProductException;
use Modules\Product\Http\Management\Exceptions\ProductHasAlreadyPublishedException;
use Modules\Product\Http\Management\Exceptions\ProductHasAlreadyUnpublishedException;
use Modules\Product\Http\Management\Exceptions\ProductIsAlreadyTrashedException;

enum ProductStatusEnum: int
{
    case PUBLISHED = 0;

    case NOT_PUBLISHED = 1;

    case TRASHED = 2;

    public function toString(): string
    {
        return match ($this) {
            self::PUBLISHED => 'Published',
            self::NOT_PUBLISHED => 'Not Published',
            self::TRASHED => 'Trashed',
        };
    }

    public function toColorType(): string
    {
        return match ($this) {
            self::PUBLISHED => 'success',
            self::NOT_PUBLISHED => 'warning',
            self::TRASHED => 'danger',
        };
    }

    public function is(self $status): bool
    {
        return $this === $status;
    }

    /**
     * Check if `from` status can be changed to `to` status.
     *
     * @throws Exception
     */
    public static function checkConsistency(self $from, self $to): true
    {
        $invalidTransitions = [
            self::PUBLISHED->value.'_'.self::PUBLISHED->value => ProductHasAlreadyPublishedException::class,
            self::TRASHED->value.'_'.self::PUBLISHED->value => CannotPublishTrashedProductException::class,
            self::NOT_PUBLISHED->value.'_'.self::NOT_PUBLISHED->value => ProductHasAlreadyUnpublishedException::class,
            self::TRASHED->value.'_'.self::NOT_PUBLISHED->value => CannotUnpublishTrashedProductException::class,
            self::TRASHED->value.'_'.self::TRASHED->value => ProductIsAlreadyTrashedException::class,
        ];

        $transitionKey = $from->value.'_'.$to->value;

        if (isset($invalidTransitions[$transitionKey])) {
            throw new $invalidTransitions[$transitionKey];
        }

        return true;
    }
}
