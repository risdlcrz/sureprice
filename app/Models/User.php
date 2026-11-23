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
        'avatar_path',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'failed_login_attempts' => 'integer',
    ];

    // ---------------------------------------------------------------- account lock
    // The lock columns are deliberately not $fillable: only lockBy() / unlock() / the sign-in flow change them.

    /** Consecutive failed sign-ins that lock the account automatically. */
    public const MAX_FAILED_LOGINS = 5;

    public const AUTO_LOCK_REASON = 'Locked automatically after too many failed sign-in attempts.';

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }

    /** Lock the account ($admin null = automatic lock) and end its sessions and remember-me token. */
    public function lockBy(?User $admin, string $reason): void
    {
        $this->forceFill([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $admin?->id,
            'lock_reason' => mb_substr(trim($reason), 0, 255),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        $this->endSessions();
    }

    public function unlock(): void
    {
        $this->forceFill([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
            'lock_reason' => null,
            'failed_login_attempts' => 0,
        ])->save();
    }

    /** Count a failed sign-in; locks the account on the MAX_FAILED_LOGINS-th. Returns true if it just locked. */
    public function registerFailedLogin(): bool
    {
        if ($this->isLocked()) {
            return false;
        }
        $this->forceFill(['failed_login_attempts' => $this->failed_login_attempts + 1])->save();

        if ($this->failed_login_attempts >= self::MAX_FAILED_LOGINS) {
            $this->lockBy(null, self::AUTO_LOCK_REASON);

            return true;
        }

        return false;
    }

    public function clearFailedLogins(): void
    {
        if ($this->failed_login_attempts !== 0) {
            $this->forceFill(['failed_login_attempts' => 0])->save();
        }
    }

    /** Sign the user out everywhere (database sessions); other drivers are caught by EnsureAccountIsNotLocked. */
    private function endSessions(): void
    {
        if (config('session.driver') === 'database') {
            \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))->where('user_id', $this->id)->delete();
        }
    }

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
        // Contractors work alongside procurement and have the same permissions.
        if ($role === 'procurement' && $this->role === 'contractor') {
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
        return Party::where('user_id', $this->id)->where(fn ($q) => $q->where('type', 'client')->orWhere('entity_type', 'client'))->first()
            ?? Party::where('email', $this->email)->where('user_id', $this->id)->first()
            ?? Party::where('email', $this->email)->where('entity_type', 'client')->first()
            ?? ($this->company ? Party::where('company_name', $this->company->company_name)->where('entity_type', 'client')->first() : null)
            ?? Party::where('email', $this->email)->first();
    }

    /** Disk holding profile photos (private; served by AvatarController). */
    public const AVATAR_DISK = 'local';

    /** URL of the profile photo, or null to show initials (<x-avatar>). Versioned so a new photo isn't cached. */
    public function avatarUrl(): ?string
    {
        return $this->avatar_path
            ? route('avatars.show', ['user' => $this->id, 'v' => substr(md5($this->avatar_path), 0, 8)])
            : null;
    }

    /** Name shown next to the avatar: company name for company accounts, else the person's name. */
    public function avatarName(): string
    {
        return ($this->user_type === 'company' ? $this->company?->company_name : null) ?: ($this->name ?: $this->username ?: '?');
    }

    /** Route name a user lands on after login or email verification. */
    public function homeRoute(): string
    {
        $byRole = ['manager' => 'manager.dashboard', 'admin' => 'admin.dashboard', 'finance' => 'finance.dashboard'];
        if (isset($byRole[$this->role ?? ''])) {
            return $byRole[$this->role];
        }
        if ($this->user_type === 'employee') {
            return ['procurement' => 'procurement.dashboard', 'contractor' => 'procurement.dashboard', 'warehousing' => 'warehouse.dashboard'][$this->role ?? ''] ?? 'home';
        }
        $company = $this->user_type === 'company' ? $this->company : null;
        if ($company?->status === 'rejected') {
            return 'account.rejected';
        }
        if ($company?->status === 'approved') {
            return ['client' => 'client.dashboard', 'supplier' => 'supplier.dashboard'][$company->designation] ?? 'pending.approval';
        }
        return 'pending.approval';
    }

    protected static function booted()
    {
        parent::booted();
    }
}

