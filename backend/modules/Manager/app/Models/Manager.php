<?php

namespace Modules\Manager\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Manager\Observers\ManagerObserver;
use Modules\User\Database\Factories\ManagerFactory;
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
class Manager extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

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

    public function makeSuperManager(): void
    {
        $this->is_super_manager = true;
        $this->save();
    }

    public function isSuperManager(): bool
    {
        return $this->is_super_manager;
    }

    public function fullName(): string
    {
        return $this->last_name.' '.$this->first_name;
    }

    protected static function newFactory(): ManagerFactory
    {
        return ManagerFactory::new();
    }
}
