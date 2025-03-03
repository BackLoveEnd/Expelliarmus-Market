<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\Manager;

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
        ];
    }

    public function superManager(): ManagerFactory
    {
        return $this->state(fn()
            => [
            'is_super_manager' => true,
        ]);
    }
}

