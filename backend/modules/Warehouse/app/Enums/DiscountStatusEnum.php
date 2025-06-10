<?php

namespace Modules\Warehouse\Enums;

use Modules\Warehouse\Http\Exceptions\InvalidDiscountStatusException;
use Modules\Warehouse\Models\Discount;

enum DiscountStatusEnum: int
{
    case CANCELLED = 1;

    case FINISHED = 2;

    case ACTIVE = 3;

    case PENDING = 4;

    public static function fromString(string $status): DiscountStatusEnum
    {
        return match (strtolower($status)) {
            strtolower(self::CANCELLED->name) => self::CANCELLED,
            strtolower(self::ACTIVE->name) => self::ACTIVE,
            strtolower(self::FINISHED->name) => self::FINISHED,
            strtolower(self::PENDING->name) => self::PENDING,
            default => throw new InvalidDiscountStatusException
        };
    }

    public function is(self $status): bool
    {
        return $this === $status;
    }

    public static function defineStatus(Discount $discount): DiscountStatusEnum
    {
        if ($discount->status->is(self::CANCELLED)) {
            return self::CANCELLED;
        }

        if ($discount->end_date < now()) {
            return self::FINISHED;
        }

        if ($discount->start_date > now()) {
            return self::PENDING;
        }

        if ($discount->start_date <= now() && $discount->end_date >= now()) {
            return self::ACTIVE;
        }

        return $discount->status;
    }

    public function toString(): string
    {
        return match ($this) {
            self::CANCELLED => 'Cancelled',
            self::FINISHED => 'Finished',
            self::ACTIVE => 'Active',
            self::PENDING => 'Pending',
        };
    }
}
