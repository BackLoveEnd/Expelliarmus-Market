<?php

namespace Modules\ContentManagement\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Modules\ContentManagement\Models\NewArrival;

class NewArrivalsExistsRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $arrivalsWithId = collect($value)->whereNotNull('arrival_id')
            ->pluck('arrival_id');

        foreach ($arrivalsWithId as $id) {
            if (! Str::isUuid($id)) {
                $fail('Invalid arrival ID.');

                return;
            }
        }

        $existsArrivals = NewArrival::query()->whereIn('arrival_id', $arrivalsWithId->toArray())
            ->get(['arrival_id']);
        /*if ($existsArrivals->count() !== $arrivalsWithId->count()) {
            $fail("Some of \"exists\" arrivals is not valid.");
        }*/
    }
}
