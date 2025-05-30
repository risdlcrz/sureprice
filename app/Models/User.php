<?php

namespace App\Models;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,  MustVerifyEmailTrait;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'user_type',
        'email_verified_at',
        'last_login_at',
        'role',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
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
            default => $this->name,
        };
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
