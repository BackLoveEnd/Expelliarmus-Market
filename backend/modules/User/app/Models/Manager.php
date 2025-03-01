<?php

namespace Modules\User\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Contracts\UserInterface;
use Modules\User\Database\Factories\ManagerFactory;
use Modules\User\Observers\ManagerObserver;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $manager_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property bool $is_super_manager
 * @property Carbon $created_at
 */
#[ObservedBy(ManagerObserver::class)]
class Manager extends Model implements UserInterface
{
    use HasFactory;
    use HasRoles;

    protected $primaryKey = 'manager_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function isSuperManager(): bool
    {
        return $this->is_super_manager;
    }

    protected static function newFactory(): ManagerFactory
    {
        return ManagerFactory::new();
    }
}
