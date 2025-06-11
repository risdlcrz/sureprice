<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'first_name',
        'last_name',
        'email',
        'role',
        'company_name',
        'street',
        'barangay',
        'city',
        'state',
        'postal',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
