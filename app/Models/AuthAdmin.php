<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthAdmin extends Model
{
    use HasFactory;
    protected $fillable = [
        'nomA',
        'mdpA',
    ];
}
