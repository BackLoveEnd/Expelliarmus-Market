<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Actions\Guests;

use Modules\Order\Order\Exceptions\FailedToCreateOrderException;
use Modules\User\Users\Http\Dto\CreateGuestDto;
use Modules\User\Users\Models\Guest;
use Throwable;

class CreateGuestAction
{
    public function handle(CreateGuestDto $dto): Guest
    {
        try {
            $guest = Guest::query()->where('email', $dto->email)->first();

            if ($guest) {
                return $this->overrideExistGuest($guest, $dto);
            }

            return Guest::query()->create([
                'email' => $dto->email,
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'phone_number' => $dto->phone,
                'phone_country_code' => 'UA',
                'address' => $dto->address,
            ]);
        } catch (Throwable $e) {
            throw new FailedToCreateOrderException(message: $e->getMessage(), previous: $e);
        }
    }

    private function overrideExistGuest(Guest $guest, CreateGuestDto $dto): Guest
    {
        $guest->last_name = $dto->lastName;
        $guest->first_name = $dto->firstName;
        $guest->phone_number = $dto->phone;
        $guest->address = $dto->address;
        $guest->save();

        return $guest;
    }
}
