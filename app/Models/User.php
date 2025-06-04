<?php

namespace App\Models;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable,  MustVerifyEmailTrait;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'user_type',
        'force_password_change',
        'email_verified_at',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'force_password_change' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Relationships
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    // Display name logic
    public function getDisplayNameAttribute()
    {
        return match ($this->user_type) {
            'employee' => $this->employee?->first_name . ' ' . $this->employee?->last_name,
            'company' => $this->company?->company_name,
            default => $this->first_name . ' ' . $this->last_name,
        };
    }
}
