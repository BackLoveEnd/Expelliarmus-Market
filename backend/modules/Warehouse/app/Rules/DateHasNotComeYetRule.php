<?php

namespace Modules\Warehouse\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DateHasNotComeYetRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = Carbon::parse($value)->setTimezone(config('app.timezone'));

        if ($date < now()) {
            $fail('The date has already passed.');
        }
    }
}
