<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $primaryKey = 'idreserve';
    protected $fillable = [
        'nomEve',
        'mode',
        'idSalle',
        'nomC',
        'email',
        'num',
        'adres',
        'sexe',
        'status',
        'info',
        'photo',
        'dateRes',
        'dateEven',
        'dateFin',
        'nbrJ',
        'tot',
        'reste',
        'isa',
        'confirmation',
        'client',
        'mdp',
    ];

    public function salle()
    {
        return $this->belongsTo(Salle::class, 'idSalle');
    }
}
