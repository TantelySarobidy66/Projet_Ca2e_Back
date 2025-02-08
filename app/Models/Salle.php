<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    protected $primaryKey = 'idSalle';
    protected $fillable = [
        'designe',
        'photo',
        'espace',
        'tarif'
    ];
}
