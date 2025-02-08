<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    use HasFactory;
    protected $primaryKey = 'idOccupation';
    protected $fillable = [
        'idSalle',
        'res',
        'dateEntrer',
        'dateSortie',
    ];
    public function salle()
    {
        return $this->belongsTo(Salle::class, 'idSalle');
    }
}
