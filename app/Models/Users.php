<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'role', 'status', 'profile_photo_path', 'phone', 'six', 'age'];
    protected $hidden = ['password'];
    protected $casts = ['email_verified_at' => 'datetime'];
}
