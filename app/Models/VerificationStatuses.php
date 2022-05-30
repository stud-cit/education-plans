<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationStatuses extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['title'];
}
