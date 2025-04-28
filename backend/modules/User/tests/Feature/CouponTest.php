<?php

declare(strict_types=1);

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Manager\Models\Manager;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Database\Seeders\UserPermissionSeeder;
use Modules\User\Users\Enums\RolesEnum;
use Modules\User\Users\Models\User;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([UserPermissionSeeder::class]);
    }

    public function test_can_create_global_coupon(): void
    {
        $manager = Manager::factory()->create();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson('api/management/users/coupons', [
                'data' => [
                    'type' => 'coupons',
                    'attributes' => [
                        'coupon_code' => 'TEST123',
                        'expires_at' => now()->addDays(30)->toDateTimeString(),
                        'discount' => 10,
                        'type' => CouponTypeEnum::GLOBAL->toString(),
                        'email' => null,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('coupons', [
            'coupon_id' => 'TEST123',
            'type' => CouponTypeEnum::GLOBAL->value,
        ]);
    }

    public function test_can_create_personal_coupon(): void
    {
        $manager = Manager::factory()->create();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson('api/management/users/coupons', [
                'data' => [
                    'type' => 'coupons',
                    'attributes' => [
                        'coupon_code' => 'TEST12345',
                        'expires_at' => now()->addDays(30)->toDateTimeString(),
                        'discount' => 15,
                        'type' => CouponTypeEnum::PERSONAL->toString(),
                        'email' => $user->email,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('coupons', [
            'coupon_id' => 'TEST12345',
            'type' => CouponTypeEnum::PERSONAL->value,
            'user_id' => $user->id,
            'email' => null,
        ]);
    }

    public function test_can_update_global_coupon(): void
    {
        $manager = Manager::factory()->create();

        $coupon = Coupon::factory()->create();

        $date = now()->addDays(30)->toDateTimeString();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->putJson('api/management/users/coupons/'.$coupon->id, [
                'data' => [
                    'type' => 'coupons',
                    'attributes' => [
                        'expires_at' => $date,
                        'discount' => 20,
                        'email' => null,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'discount' => 20,
            'expires_at' => $date,
        ]);
    }

    public function test_can_update_coupon_for_exist_user(): void
    {
        $manager = Manager::factory()->create();

        $user = User::factory()->create();

        $coupon = Coupon::factory()->user($user)->create();

        $date = now()->addDays(30)->toDateTimeString();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->putJson('api/management/users/coupons/'.$coupon->id, [
                'data' => [
                    'type' => 'coupons',
                    'attributes' => [
                        'expires_at' => $date,
                        'discount' => 10,
                        'email' => $user->email,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'user_id' => $user->id,
            'email' => null,
            'discount' => 10,
            'expires_at' => $date,
        ]);
    }

    public function test_can_update_coupon_for_email(): void
    {
        $manager = Manager::factory()->create();

        $coupon = Coupon::factory()->user('example@gmail.com')->create();

        $date = now()->addDays(30)->toDateTimeString();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->putJson('api/management/users/coupons/'.$coupon->id, [
                'data' => [
                    'type' => 'coupons',
                    'attributes' => [
                        'expires_at' => $date,
                        'discount' => 10,
                        'email' => 'example@gmail.com',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'user_id' => null,
            'discount' => 10,
            'email' => 'example@gmail.com',
            'expires_at' => $date,
        ]);
    }

    public function test_can_override_coupon_owner(): void
    {
        $manager = Manager::factory()->create();

        $user = User::factory()->create();

        $coupon = Coupon::factory()->user($user)->create();

        $date = now()->addDays(30)->toDateTimeString();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->putJson('api/management/users/coupons/'.$coupon->id, [
                'data' => [
                    'type' => 'coupons',
                    'attributes' => [
                        'expires_at' => $date,
                        'discount' => 10,
                        'email' => 'example@gmail.com', // New coupon owner
                    ],
                ],
            ]);

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'user_id' => null,
            'discount' => 10,
            'email' => 'example@gmail.com',
            'expires_at' => $date,
        ]);

        $this->assertDatabaseMissing('coupons', [
            'id' => $coupon->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_can_delete_coupon(): void
    {
        $manager = Manager::factory()->create();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson('api/management/users/coupons', [
                'data' => [
                    'type' => 'coupons',
                    'attributes' => [
                        'coupon_code' => 'TEST1235678',
                        'expires_at' => now()->addDays(30)->toDateTimeString(),
                        'discount' => 10,
                        'type' => CouponTypeEnum::GLOBAL->toString(),
                        'email' => null,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('coupons', [
            'coupon_id' => 'TEST1235678',
            'type' => CouponTypeEnum::GLOBAL->value,
        ]);

        $coupon = Coupon::query()->where('coupon_id', 'TEST1235678')->first(['id', 'coupon_id']);

        $deleteResponse = $this
            ->actingAs($manager)
            ->deleteJson('api/management/users/coupons/'.$coupon->coupon_id);

        $this->assertDatabaseMissing('coupons', [
            'id' => $coupon->id,
        ]);
    }

    public function test_cannot_modify_coupons_if_not_manager(): void
    {
        $user = User::factory()->create();

        $coupon = Coupon::factory()->create();

        $response = $this
            ->actingAs($user, 'web')
            ->putJson('api/management/users/coupons/'.$coupon->id, [
                'data' => [
                    'type' => 'coupons',
                    'attributes' => [
                        'expires_at' => now()->addDays(30)->toDateTimeString(),
                        'discount' => 10,
                        'email' => null,
                    ],
                ],
            ]);

        $response->assertExactJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}