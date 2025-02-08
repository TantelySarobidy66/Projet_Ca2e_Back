<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Occupation;
use App\Models\Salle;
use App\Models\Solde;
use App\Models\Notif;
use Carbon\Carbon;

class BackStatistique extends Controller
{
    public function Count(){
        $res = Reservation::count();
        $client = Reservation::with('salle')->where('isa', 'tsyao')->count();
        $sal = Salle::count();
        $solde = Solde::select('solde')->where('id', 1)->first();
        $maxSal = Salle::max('tarif');
        $minSal = Salle::min('tarif');
        $maxRes = Reservation::max('tot');
        $direct = Reservation::where('info','Direct')->count();
        $en_ligne = Reservation::where('info','En_ligne')->count();
        if($solde){
            return response()->json(['res' => $res, 'client' => $client, 'sal' => $sal, 'solde' => $solde->solde, 'maxTarif' => $maxSal, 'minTarif' => $minSal, 'maxRes' => $maxRes, 'direct'=> $direct, 'en_ligne' => $en_ligne]);
        }
    }


    public function notif(){
        $notif = Notif::select('miampy')->where('id', 1)->first();
        if($notif){
            return response()->json(['notif' => $notif->miampy]);
        }
    }


    public function notifUpdate(){
        Notif::where('id', 1)->update(['miampy' => 0]);
        return response()->json(['message' => 'succes']);
    }


    public function chart1()
    {
        $reservations = Reservation::selectRaw('
                TO_CHAR("dateEven", \'Mon\') AS mois,
                COUNT(CASE WHEN "info" = \'Direct\' THEN 1 END) AS total_Direct,
                COUNT(CASE WHEN "info" = \'En_ligne\' THEN 1 END) AS total_En_ligne
            ')
            ->groupByRaw('EXTRACT(YEAR FROM "dateEven"), EXTRACT(MONTH FROM "dateEven"), TO_CHAR("dateEven", \'Mon\')')
            ->orderByRaw('EXTRACT(YEAR FROM "dateEven") ASC, EXTRACT(MONTH FROM "dateEven") ASC')
            ->limit(6)
            ->get();

        $totalRes = Reservation::selectRaw('TO_CHAR("dateEven", \'Mon\') AS mois')
        ->selectRaw('COUNT("idreserve") AS Total_reservation')
        ->groupByRaw('EXTRACT(YEAR FROM "dateEven"), EXTRACT(MONTH FROM "dateEven"), TO_CHAR("dateEven", \'Mon\')')
        ->orderByRaw('EXTRACT(YEAR FROM "dateEven") ASC, EXTRACT(MONTH FROM "dateEven") ASC')
        ->limit(6)
        ->get();

        $dernierMois5 = Reservation::selectRaw('
         TO_CHAR("dateEven", \'Mon\') AS mois,
         EXTRACT(YEAR FROM "dateEven") AS annee,
         COUNT("idreserve") AS Total_reservation
        ')
        ->groupByRaw('EXTRACT(YEAR FROM "dateEven"), EXTRACT(MONTH FROM "dateEven"), TO_CHAR("dateEven", \'Mon\')')
        ->orderByRaw('EXTRACT(YEAR FROM "dateEven") ASC, EXTRACT(MONTH FROM "dateEven") ASC')
        ->limit(5) // Limite aux 5 derniers mois
        ->get();

        $MaxRes = Reservation::selectRaw('
        EXTRACT(YEAR FROM "dateEven") AS annee,
        EXTRACT(MONTH FROM "dateEven") AS mois,
        COUNT("idreserve") AS Total_reservation
            ')
            ->groupByRaw('EXTRACT(YEAR FROM "dateEven"), EXTRACT(MONTH FROM "dateEven")')
            ->orderByRaw('Total_reservation ASC') // Trier par total de réservation décroissant
            ->limit(1) // Limiter à la ligne avec le plus grand nombre de réservations
            ->get();

        return response()->json(['liste1' => $reservations , 'liste' => $totalRes, 'dernier' => $dernierMois5, 'max' => $MaxRes]);
    }




    // $reche = $champ->date;
    public function salleLibre(Request $champ) {
        $reche = Salle::whereNotIn('idSalle', Occupation::select('idSalle')->where('dateEntrer', '<=', $champ->date)->where('dateSortie', '>=', $champ->date))->get();
        return response()->json(['re' => $reche]);
    }


}
