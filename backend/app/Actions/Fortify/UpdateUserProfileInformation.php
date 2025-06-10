<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Modules\User\Users\Models\User;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make(
            $input,
            [
                'first_name' => ['required', 'string', 'max:50'],
                'last_name' => ['nullable', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'address' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'phone:UA,US', Rule::unique('users', 'phone_number')->ignore($user->id)],
            ],
            [
                'phone.phone' => 'Please enter a valid phone number for Ukraine or the US.',
                'phone.unique' => 'This phone number is already taken.',
            ],
        )->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'] ?? null,
                'email' => $input['email'],
                'phone_number' => $input['phone'] ?? null,
                'address' => $input['address'] ?? null,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone_number' => $input['phone'] ?? null,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
