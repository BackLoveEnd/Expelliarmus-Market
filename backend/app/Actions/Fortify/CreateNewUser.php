<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Modules\User\Users\Enums\RolesEnum;
use Modules\User\Users\Events\GuestRegistered;
use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $this->validate($input);

        $user = $this->createFromGuestOrUser($input);

        $user->assignRole(RolesEnum::REGULAR_USER);

        return $user;
    }

    private function createFromGuestOrUser(array $input): User
    {
        $guest = Guest::query()->where('email', $input['email'])->first();

        if ($guest) {
            $user = User::query()->create([
                'first_name' => $input['first_name'],
                'email' => $guest->email,
                'last_name' => $guest->last_name,
                'phone_country_code' => $guest->phone_country_code,
                'phone_number' => $guest->phone_number,
                'address' => $guest->address,
                'password' => Hash::make($input['password']),
            ]);

            event(new GuestRegistered($user, $guest));

            return $user;
        }

        return $this->defaultCreate($input);
    }

    private function defaultCreate(array $input)
    {
        return User::query()->create([
            'first_name' => $input['first_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }

    private function validate(array $input): void
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();
    }
}
