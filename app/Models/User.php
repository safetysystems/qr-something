<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_super_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workplaces(): BelongsToMany
    {
        return $this->belongsToMany(Workplace::class, 'workplace_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function createdEquipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'created_by');
    }

    public function createdInspectionTemplates(): HasMany
    {
        return $this->hasMany(InspectionTemplate::class, 'created_by');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function currentWorkplace(): ?Workplace
    {
        return $this->workplaces()
            ->whereNull('workplaces.archived_at')
            ->orderBy('workplaces.name')
            ->first();
    }

    public function canAccessWorkplace(Workplace $workplace): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->workplaces()
            ->whereKey($workplace->getKey())
            ->exists();
    }
}
