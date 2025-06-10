<?php

declare(strict_types=1);

namespace Modules\Order\Order\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\User\Users\Models\Guest;

class UniquePhoneGuestRule implements ValidationRule
{
    public function __construct(
        public $email,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $existingGuestWithEmail = Guest::query()->where('email', $this->email)->exists();

        if ($existingGuestWithEmail) {
            return;
        }

        $duplicatePhone = Guest::query()
            ->where('phone_number', $value)
            ->where('email', '!=', $this->email)
            ->exists();

        if ($duplicatePhone) {
            $fail('This phone number is already taken.');
        }
    }
}
