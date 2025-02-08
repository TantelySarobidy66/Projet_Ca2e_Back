<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Occupation;
use App\Models\Salle;
use App\Models\Solde;
use Carbon\Carbon;

class BackOccupation extends Controller
{
    public function getDatesOccupées($idSalle)
    {
        $today = Carbon::now()->toDateString();
        $datesOccupées = Occupation::select('dateEntrer', 'dateSortie')->where('idSalle', $idSalle)->where('dateEntrer', '>=', $today)->get();
        return response()->json(['datesOccupées' => $datesOccupées]);
    }
}
