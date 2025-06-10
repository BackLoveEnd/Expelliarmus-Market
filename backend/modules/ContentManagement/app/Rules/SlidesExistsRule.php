<?php

namespace Modules\ContentManagement\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Modules\ContentManagement\Models\ContentSlider;

class SlidesExistsRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $slidesWithId = collect($value)->whereNotNull('slide_id')
            ->pluck('slide_id');

        foreach ($slidesWithId as $id) {
            if (! Str::isUuid($id)) {
                $fail('Invalid slide ID.');

                return;
            }
        }

        $existsSlides = ContentSlider::query()->whereIn('slide_id', $slidesWithId->toArray())
            ->get(['slide_id']);

        if ($existsSlides->count() !== $slidesWithId->count()) {
            $fail('Some of "exists" slide is not valid.');
        }
    }
}
