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
        'email', // Add this
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
