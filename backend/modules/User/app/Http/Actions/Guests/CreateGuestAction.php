<?php

declare(strict_types=1);

namespace Modules\User\Http\Actions\Guests;

use Modules\User\Http\Dto\CreateGuestDto;
use Modules\User\Models\Guest;

class CreateGuestAction
{
    public function handle(CreateGuestDto $dto): Guest
    {
        $guest = Guest::query()->where('email', $dto->email)->first();

        if ($guest) {
            return $this->overrideExistGuest($guest, $dto);
        }

        return Guest::query()->create([
            'email' => $dto->email,
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'phone' => $dto->phone,
            'address' => $dto->address,
        ]);
    }

    private function overrideExistGuest(Guest $guest, CreateGuestDto $dto): Guest
    {
        $guest->last_name = $dto->lastName;
        $guest->first_name = $dto->firstName;
        $guest->phone = $dto->phone;
        $guest->address = $dto->address;
        $guest->save();

        return $guest;
    }
}