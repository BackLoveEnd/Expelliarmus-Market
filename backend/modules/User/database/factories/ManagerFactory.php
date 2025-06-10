<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\Manager\Models\Manager;
use Modules\User\Users\Enums\RolesEnum;

class ManagerFactory extends Factory
{
    protected $model = Manager::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->email(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'password' => Hash::make('manager123'),
            'is_super_manager' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Manager $manager) {
            $manager->assignRole(RolesEnum::MANAGER->toString());
        });
    }

    public function superManager(): ManagerFactory
    {
        return $this->state(fn () => [
            'is_super_manager' => true,
        ]);
    }
}
