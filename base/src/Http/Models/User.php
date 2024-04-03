<?php

namespace Polirium\Core\Base\Http\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Avatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Polirium\Core\Base\Http\Models\Traits\HasUuid;
use Polirium\Modules\Sale\Http\Model\Branch;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use LogsActivity;
    use HasUuid;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'super_admin',
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

        return $logOptions;
    }

    public function getAvatarAttribute()
    {
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
        ->withPivot(["id", "active"]);
    }
}
