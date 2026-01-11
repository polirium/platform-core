<?php

namespace Polirium\Core\Base\Http\Models;

use Avatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Polirium\Core\Base\Http\Models\Branch\Branch;
use Polirium\Core\Base\Http\Models\Traits\HasUuid;
use Polirium\Impersonate\Models\Impersonate;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use LogsActivity;
    use HasUuid;
    use HasRoles;
    use Impersonate;

    /**
     * @return bool
     */
    public function canImpersonate()
    {
        return $this->can('users.impersonate');
    }

    /**
     * @return bool
     */
    public function canBeImpersonated()
    {
        return ! $this->super_admin;
    }

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'phone',
        'super_admin',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->name = $user->first_name . ' ' . $user->last_name;
        });

        static::updating(function ($user) {
            $user->name = $user->first_name . ' ' . $user->last_name;
        });

    }

    public function isSuperAdmin(): bool
    {
        return $this->super_admin;
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

    /**
     * Get avatar URL - prioritize uploaded avatar, fallback to generated
     */
    public function getAvatarAttribute($value)
    {
        // If there's an uploaded avatar path stored in DB
        if (!empty($value) && str_starts_with($value, 'avatars/')) {
            return asset('storage/' . $value);
        }

        // Fallback to generated avatar
        return Avatar::create($this->name)->setShape('square')->toBase64();
    }

    /**
     * Get raw avatar path (for checking if uploaded avatar exists)
     */
    public function getAvatarPathAttribute()
    {
        return $this->attributes['avatar'] ?? null;
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
}
