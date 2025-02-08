<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'mail',
        'mdp',
    ];
}
