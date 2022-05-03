<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'user_name', 'user_role', 'operation', 'ip',  
    ];
}
