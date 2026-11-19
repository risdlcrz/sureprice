<?php

namespace App\Models;
// use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable // implements MustVerifyEmail
{
    use HasFactory, Notifiable;

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
        return $this->hasOne(Party::class, 'user_id');
    }

    public function supplier()
    {
        return $this->hasOne(\App\Models\Supplier::class, 'user_id');
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
    /** User::role('admin') or User::role('admin', 'manager') */
    public function scopeRole($query, string ...$roles)
    {
        return count($roles) === 1 ? $query->where('role', $roles[0]) : $query->whereIn('role', $roles);
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

    // New: Check if user is a manager (operational role)
    public function isManager()
    {
        return ($this->role ?? $this->user_type ?? null) === 'manager';
    }

    // Updated: Check if user is an admin (approval/oversight only)
    public function isAdmin()
    {
        return ($this->role ?? $this->user_type ?? null) === 'admin';
    }

    public function isSupplier()
    {
        return ($this->role ?? $this->user_type ?? null) === 'supplier';
    }

    public function isClientCompany(): bool
    {
        return $this->user_type === 'company' && $this->company?->designation === 'client';
    }

    public function isSupplierCompany(): bool
    {
        return $this->user_type === 'company' && $this->company?->designation === 'supplier';
    }

    /**
     * The client Party record for this user: linked by user_id first, then by email, then by company name,
     * finally any party sharing the email (legacy records created before parties were linked to users).
     */
    public function clientParty(): ?Party
    {
        return Party::where('user_id', $this->id)->where('entity_type', 'client')->first()
            ?? Party::where('email', $this->email)->where('user_id', $this->id)->first()
            ?? Party::where('email', $this->email)->where('entity_type', 'client')->first()
            ?? ($this->company ? Party::where('company_name', $this->company->company_name)->where('entity_type', 'client')->first() : null)
            ?? Party::where('email', $this->email)->first();
    }

    /** Route name a user lands on after login or email verification. */
    public function homeRoute(): string
    {
        $byRole = ['manager' => 'manager.dashboard', 'admin' => 'admin.dashboard', 'finance' => 'finance.dashboard'];
        if (isset($byRole[$this->role ?? ''])) {
            return $byRole[$this->role];
        }
        if ($this->user_type === 'employee') {
            return ['procurement' => 'procurement.dashboard', 'warehousing' => 'warehouse.dashboard'][$this->role ?? ''] ?? 'home';
        }
        $company = $this->user_type === 'company' ? $this->company : null;
        if ($company?->status === 'rejected') {
            return 'account.rejected';
        }
        if ($company?->status === 'approved') {
            return ['client' => 'landing.catalogue', 'supplier' => 'supplier.dashboard'][$company->designation] ?? 'pending.approval';
        }
        return 'pending.approval';
    }

    protected static function booted()
    {
        parent::booted();
    }
}

