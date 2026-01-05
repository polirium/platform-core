<?php

namespace Polirium\Core\Base\Http\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Avatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Polirium\Core\Base\Http\Models\Branch\Branch;
use Polirium\Core\Base\Http\Models\Traits\HasUuid;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use LogsActivity;
    use HasUuid;
    use HasRoles;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'phone',
        'status',
        'super_admin',
        'avatar',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'super_admin' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->name = $user->first_name . ' ' . $user->last_name;
            if (!$user->status) {
                $user->status = 'active';
            }
        });

        static::updating(function ($user) {
            $user->name = $user->first_name . ' ' . $user->last_name;
        });
    }

    public function isSuperAdmin(): bool
    {
        return $this->super_admin;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = new LogOptions();
        $logOptions->logAll();
        $logOptions->logOnlyDirty();
        // Never log password for security reasons
        $logOptions->logExcept(['password']);

        return $logOptions;
    }

    public function getAvatarAttribute()
    {
        if ($this->attributes['avatar']) {
            return $this->attributes['avatar'];
        }
        return Avatar::create($this->name)->setShape('square')->toBase64();
    }

    /**
     * The users that belong to the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'user_branches', 'user_id', 'branch_id')
        ->withTimestamps()
        ->withPivot(['id', 'active']);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}
