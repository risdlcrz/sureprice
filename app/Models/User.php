<?php

namespace App\Models;
// use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable // implements MustVerifyEmail
{
    use Notifiable; // ,  MustVerifyEmailTrait;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'user_type',
        'email_verified_at',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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

    public function party()
    {
        return $this->hasOne(Party::class);
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

    // Add a name accessor for backward compatibility
    public function getNameAttribute($value)
    {
        return $value ?: $this->getDisplayNameAttribute();
    }

    // Scope for filtering by role
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Check if user has a specific role
    public function hasRole($role)
    {
        // Check role in users table first
        if ($this->role === $role) {
            return true;
        }
        
        // If not found in users table, check in employees table
        if ($this->employee && $this->employee->role === $role) {
            return true;
        }
        
        return false;
    }
}
