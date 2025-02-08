<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Occupation;
use App\Models\Salle;
use App\Models\Solde;
use App\Models\Notif;
use Carbon\Carbon;

class BackReservation extends Controller
{
    public function selectRes()
    {
        $Reser = Reservation::orderBy('idreserve','desc')->with('salle')->where('client', 'vita')->get();
        return response()->json(['liste' => $Reser]);
    }

    public function selectResee($date){
        $Reser = Reservation::orderBy('idreserve','desc')->with('salle')->where('dateEven', $date)->where('client', 'vita')->get();
        return response()->json(['liste' => $Reser]);
    }

    public function selectResCl($id)
    {
        $Reser = Reservation::orderBy('idreserve','desc')->with('salle')->where('client', 'vita')->where('nomC', $id)->get();
        return response()->json(['liste' => $Reser]);
    }

    public function selectRes1()
    {
        $Reser = Reservation::with('salle')->where('isa', 'tsyao')->get();
        return response()->json(['liste' => $Reser]);
    }

    public function createClient(Request $champ){
        $deja = Reservation::where('nomC', $champ->nomC)->first();
        if($deja){
            return response()->json(['message' => 'En....']);
        }
        else{
            Reservation::create([
                'nomC' => $champ->nomC,
                'email' => $champ->mail,
                'num' => $champ->num,
                'adres' => $champ->adrs,
                'sexe' => $champ->sexe,
                'photo' => $champ->nomInital,
                'mdp' => $champ->mdp,
            ]);
            return response()->json(['message' => 'En..']);
        }
    }

    public function createRes(Request $champ){
        $dateEv = Carbon::parse($champ->dateEv);
        $nbrJour = $champ->nbrJ - 1;
        $dateFin = $dateEv->addDays($nbrJour)->toDateString();
        $compte = Occupation::where('idSalle', $champ->des)
        ->where('dateEntrer', '<=', $champ->dateEv)
        ->where('dateSortie', '>=', $champ->dateEv)
        ->orWhere('dateEntrer', '<=', $dateFin)
        ->where('idSalle', $champ->des)
        ->where('dateSortie', '>=', $dateFin)
        ->orwhere('dateEntrer', '>=', $champ->dateEv)
        ->where('idSalle', $champ->des)
        ->where('dateSortie', '<=', $dateFin)
        ->count();
        if($compte == 0){
            $solde = Solde::select('solde')->where('id', 1)->first();
            if($solde){
                if($champ->stat == 'Payant'){
                    if($champ->info == 'Avance'){
                        $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                        if($prix){
                            $tarif = $prix->tarif * $champ->nbrJ;
                            $reste = $tarif - $champ->avance;
                            $totalSolde = $solde->solde + $champ->avance;
                            $deja = Reservation::where('nomC', $champ->nomC)->first();
                            if($deja){
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => $champ->info,
                                    'info' => 'Direct',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => $champ->avance,
                                    'reste' => $reste,
                                    'isa' => 'ao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }
                            else{
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => $champ->info,
                                    'info' => 'Direct',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => $champ->avance,
                                    'reste' => $reste,
                                    'isa' => 'tsyao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }
                            Solde::where('id', 1)->update(['solde' => $totalSolde]);
                            $makaId = Reservation::select('idreserve')
                            ->where('idSalle', $champ->des)
                            ->where('nomC', $champ->nomC)
                            ->where('email', $champ->mail)
                            ->where('num', $champ->num)
                            ->where('dateEven', $champ->dateEv)
                            ->first();
                            if($makaId){
                                Occupation::create([
                                    'idSalle' => $champ->des,
                                    'res' => $makaId->idreserve,
                                    'dateEntrer' => $champ->dateEv,
                                    'dateSortie' => $dateFin,
                                ]);
                            }
                        }
                    }else{
                        $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                        if($prix){
                            $tarif = $prix->tarif * $champ->nbrJ;
                            $totalSolde = $solde->solde + $tarif;
                            $deja = Reservation::where('nomC', $champ->nomC)->first();
                            if($deja){
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => $champ->info,
                                    'info' => 'Direct',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => $tarif,
                                    'reste' => 0,
                                    'isa' => 'ao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }
                            else{
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => $champ->info,
                                    'info' => 'Direct',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => $tarif,
                                    'reste' => 0,
                                    'isa' => 'tsyao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }

                            Solde::where('id', 1)->update(['solde' => $totalSolde]);
                            $makaId = Reservation::select('idreserve')
                            ->where('idSalle', $champ->des)
                            ->where('nomC', $champ->nomC)
                            ->where('email', $champ->mail)
                            ->where('num', $champ->num)
                            ->where('dateEven', $champ->dateEv)
                            ->first();
                            if($makaId){
                                Occupation::create([
                                    'idSalle' => $champ->des,
                                    'res' => $makaId->idreserve,
                                    'dateEntrer' => $champ->dateEv,
                                    'dateSortie' => $dateFin,
                                ]);
                            }
                        }
                    }
                }
                else{
                    $deja = Reservation::where('nomC', $champ->nomC)->first();
                    if($deja){
                        Reservation::create([
                            'nomEve' => $champ->eve,
                            'mode' => $champ->stat,
                            'idSalle' => $champ->des,
                            'nomC' => $champ->nomC,
                            'email' => $champ->mail,
                            'num' => $champ->num,
                            'adres' => $champ->adrs,
                            'sexe' => $champ->sexe,
                            'status' => 'Gratuit',
                            'info' => 'Direct',
                            'photo' => $champ->nomInital,
                            'dateRes' => $champ->datRes,
                            'dateEven' => $champ->dateEv,
                            'dateFin' => $dateFin,
                            'nbrJ' => $champ->nbrJ,
                            'tot' => 0,
                            'reste' => 0,
                            'isa' => 'ao',
                            'confirmation' => 'Valider',
                            'client' => 'vita',
                        ]);
                    }
                    else{
                        Reservation::create([
                            'nomEve' => $champ->eve,
                            'mode' => $champ->stat,
                            'idSalle' => $champ->des,
                            'nomC' => $champ->nomC,
                            'email' => $champ->mail,
                            'num' => $champ->num,
                            'adres' => $champ->adrs,
                            'sexe' => $champ->sexe,
                            'status' => 'Gratuit',
                            'info' => 'Direct',
                            'photo' => $champ->nomInital,
                            'dateRes' => $champ->datRes,
                            'dateEven' => $champ->dateEv,
                            'dateFin' => $dateFin,
                            'nbrJ' => $champ->nbrJ,
                            'tot' => 0,
                            'reste' => 0,
                            'isa' => 'tsyao',
                            'confirmation' => 'Valider',
                            'client' => 'vita',
                        ]);
                    }

                    $makaId = Reservation::select('idreserve')
                    ->where('idSalle', $champ->des)
                    ->where('nomC', $champ->nomC)
                    ->where('email', $champ->mail)
                    ->where('num', $champ->num)
                    ->where('dateEven', $champ->dateEv)
                    ->first();
                    if($makaId){
                        Occupation::create([
                            'idSalle' => $champ->des,
                            'res' => $makaId->idreserve,
                            'dateEntrer' => $champ->dateEv,
                            'dateSortie' => $dateFin,
                        ]);
                    }
                }
                return response()->json(['message'=> 'En..']);
            }
        }
        else{
            return response()->json(['message'=>'En...']);
        }
    }






    public function createResEnligne(Request $champ){
        $dateEv = Carbon::parse($champ->dateEv);
        $nbrJour = $champ->nbrJ - 1;
        $dateFin = $dateEv->addDays($nbrJour)->toDateString();
        $compte = Occupation::where('idSalle', $champ->des)
        ->where('dateEntrer', '<=', $champ->dateEv)
        ->where('dateSortie', '>=', $champ->dateEv)
        ->orWhere('dateEntrer', '<=', $dateFin)
        ->where('idSalle', $champ->des)
        ->where('dateSortie', '>=', $dateFin)
        ->orwhere('dateEntrer', '>=', $champ->dateEv)
        ->where('idSalle', $champ->des)
        ->where('dateSortie', '<=', $dateFin)
        ->count();
        if($compte == 0){
            $noti = Notif::select('miampy')->where('id', 1)->first();
            if($noti){
                $miampy = $noti->miampy + 1;
                Notif::where('id', 1)->update(['miampy' => $miampy]);
            }
            $solde = Solde::select('solde')->where('id', 1)->first();
            if($solde){
                if($champ->stat == 'Payant'){
                    if($champ->info == 'Avance'){
                        $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                        if($prix){
                            $tarif = $prix->tarif * $champ->nbrJ;
                            $reste = $tarif - $champ->avance;
                            $totalSolde = $solde->solde + $champ->avance;
                            $deja = Reservation::where('nomC', $champ->nomC)->first();
                            if($deja){
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => $champ->info,
                                    'info' => 'En_ligne',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => $champ->avance,
                                    'reste' => $reste,
                                    'isa' => 'ao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }
                            else{
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => $champ->info,
                                    'info' => 'En_ligne',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => $champ->avance,
                                    'reste' => $reste,
                                    'isa' => 'tsyao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }
                            Solde::where('id', 1)->update(['solde' => $totalSolde]);
                            $makaId = Reservation::select('idreserve')
                            ->where('idSalle', $champ->des)
                            ->where('nomC', $champ->nomC)
                            ->where('email', $champ->mail)
                            ->where('num', $champ->num)
                            ->where('dateEven', $champ->dateEv)
                            ->first();
                            if($makaId){
                                Occupation::create([
                                    'idSalle' => $champ->des,
                                    'res' => $makaId->idreserve,
                                    'dateEntrer' => $champ->dateEv,
                                    'dateSortie' => $dateFin,
                                ]);
                            }
                        }
                    }else{
                        $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                        if($prix){
                            $tarif = $prix->tarif * $champ->nbrJ;
                            $totalSolde = $solde->solde + $tarif;
                            $deja = Reservation::where('nomC', $champ->nomC)->first();
                            if($deja){
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => $champ->info,
                                    'info' => 'En_ligne',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => $tarif,
                                    'reste' => 0,
                                    'isa' => 'ao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }
                            else{
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => $champ->info,
                                    'info' => 'En_ligne',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => $tarif,
                                    'reste' => 0,
                                    'isa' => 'tsyao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }

                            Solde::where('id', 1)->update(['solde' => $totalSolde]);
                            $makaId = Reservation::select('idreserve')
                            ->where('idSalle', $champ->des)
                            ->where('nomC', $champ->nomC)
                            ->where('email', $champ->mail)
                            ->where('num', $champ->num)
                            ->where('dateEven', $champ->dateEv)
                            ->first();
                            if($makaId){
                                Occupation::create([
                                    'idSalle' => $champ->des,
                                    'res' => $makaId->idreserve,
                                    'dateEntrer' => $champ->dateEv,
                                    'dateSortie' => $dateFin,
                                ]);
                            }
                        }
                    }
                }
                else{
                    $deja = Reservation::where('nomC', $champ->nomC)->first();
                    if($deja){
                        Reservation::create([
                            'nomEve' => $champ->eve,
                            'mode' => $champ->stat,
                            'idSalle' => $champ->des,
                            'nomC' => $champ->nomC,
                            'email' => $champ->mail,
                            'num' => $champ->num,
                            'adres' => $champ->adrs,
                            'sexe' => $champ->sexe,
                            'status' => 'Gratuit',
                            'info' => 'En_ligne',
                            'photo' => $champ->nomInital,
                            'dateRes' => $champ->datRes,
                            'dateEven' => $champ->dateEv,
                            'dateFin' => $dateFin,
                            'nbrJ' => $champ->nbrJ,
                            'tot' => 0,
                            'reste' => 0,
                            'isa' => 'ao',
                            'confirmation' => 'Valider',
                            'client' => 'vita',
                        ]);
                    }
                    else{
                        Reservation::create([
                            'nomEve' => $champ->eve,
                            'mode' => $champ->stat,
                            'idSalle' => $champ->des,
                            'nomC' => $champ->nomC,
                            'email' => $champ->mail,
                            'num' => $champ->num,
                            'adres' => $champ->adrs,
                            'sexe' => $champ->sexe,
                            'status' => 'Gratuit',
                            'info' => 'En_ligne',
                            'photo' => $champ->nomInital,
                            'dateRes' => $champ->datRes,
                            'dateEven' => $champ->dateEv,
                            'dateFin' => $dateFin,
                            'nbrJ' => $champ->nbrJ,
                            'tot' => 0,
                            'reste' => 0,
                            'isa' => 'tsyao',
                            'confirmation' => 'Valider',
                            'client' => 'vita',
                        ]);
                    }

                    $makaId = Reservation::select('idreserve')
                    ->where('idSalle', $champ->des)
                    ->where('nomC', $champ->nomC)
                    ->where('email', $champ->mail)
                    ->where('num', $champ->num)
                    ->where('dateEven', $champ->dateEv)
                    ->first();
                    if($makaId){
                        Occupation::create([
                            'idSalle' => $champ->des,
                            'res' => $makaId->idreserve,
                            'dateEntrer' => $champ->dateEv,
                            'dateSortie' => $dateFin,
                        ]);
                    }
                }
                return response()->json(['message'=> 'En..']);
            }
        }
        else{
            return response()->json(['message'=>'En...']);
        }
    }








    public function createResClient(Request $champ){
        $dateEv = Carbon::parse($champ->dateEv);
        $nbrJour = $champ->nbrJ - 1;
        $dateFin = $dateEv->addDays($nbrJour)->toDateString();
        $compte = Occupation::where('idSalle', $champ->des)
        ->where('dateEntrer', '<=', $champ->dateEv)
        ->where('dateSortie', '>=', $champ->dateEv)
        ->orWhere('dateEntrer', '<=', $dateFin)
        ->where('idSalle', $champ->des)
        ->where('dateSortie', '>=', $dateFin)
        ->orwhere('dateEntrer', '>=', $champ->dateEv)
        ->where('idSalle', $champ->des)
        ->where('dateSortie', '<=', $dateFin)
        ->count();
        if($compte == 0){
            $deja = Reservation::select('client')->where('idreserve', $champ->idreserve)->first();
            if($deja){
                if($deja->client == 'vita'){
                    $noti = Notif::select('miampy')->where('id', 1)->first();
                    if($noti){
                        $miampy = $noti->miampy + 1;
                        Notif::where('id', 1)->update(['miampy' => $miampy]);
                    }
                    $solde = Solde::select('solde')->where('id', 1)->first();
                    if($solde){
                        if($champ->stat == 'Payant'){
                            if($champ->info == 'Avance'){
                                $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                                if($prix){
                                    $tarif = $prix->tarif * $champ->nbrJ;
                                    $reste = $tarif - $champ->avance;
                                    $totalSolde = $solde->solde + $champ->avance;
                                    $deja = Reservation::where('nomC', $champ->nomC)->first();
                                    if($deja){
                                        Reservation::create([
                                            'nomEve' => $champ->eve,
                                            'mode' => $champ->stat,
                                            'idSalle' => $champ->des,
                                            'nomC' => $champ->nomC,
                                            'email' => $champ->mail,
                                            'num' => $champ->num,
                                            'adres' => $champ->adrs,
                                            'sexe' => $champ->sexe,
                                            'status' => $champ->info,
                                            'info' => 'En_ligne',
                                            'photo' => $champ->nomInital,
                                            'dateRes' => $champ->datRes,
                                            'dateEven' => $champ->dateEv,
                                            'dateFin' => $dateFin,
                                            'nbrJ' => $champ->nbrJ,
                                            'tot' => $champ->avance,
                                            'reste' => $reste,
                                            'isa' => 'ao',
                                            'confirmation' => 'Valider',
                                            'client' => 'vita',
                                        ]);
                                    }
                                    else{
                                        Reservation::create([
                                            'nomEve' => $champ->eve,
                                            'mode' => $champ->stat,
                                            'idSalle' => $champ->des,
                                            'nomC' => $champ->nomC,
                                            'email' => $champ->mail,
                                            'num' => $champ->num,
                                            'adres' => $champ->adrs,
                                            'sexe' => $champ->sexe,
                                            'status' => $champ->info,
                                            'info' => 'En_ligne',
                                            'photo' => $champ->nomInital,
                                            'dateRes' => $champ->datRes,
                                            'dateEven' => $champ->dateEv,
                                            'dateFin' => $dateFin,
                                            'nbrJ' => $champ->nbrJ,
                                            'tot' => $champ->avance,
                                            'reste' => $reste,
                                            'isa' => 'tsyao',
                                            'confirmation' => 'Valider',
                                            'client' => 'vita',
                                        ]);
                                    }
                                    Solde::where('id', 1)->update(['solde' => $totalSolde]);
                                    $makaId = Reservation::select('idreserve')
                                    ->where('idSalle', $champ->des)
                                    ->where('nomC', $champ->nomC)
                                    ->where('email', $champ->mail)
                                    ->where('num', $champ->num)
                                    ->where('dateEven', $champ->dateEv)
                                    ->first();
                                    if($makaId){
                                        Occupation::create([
                                            'idSalle' => $champ->des,
                                            'res' => $makaId->idreserve,
                                            'dateEntrer' => $champ->dateEv,
                                            'dateSortie' => $dateFin,
                                        ]);
                                    }
                                }
                            }else{
                                $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                                if($prix){
                                    $tarif = $prix->tarif * $champ->nbrJ;
                                    $totalSolde = $solde->solde + $tarif;
                                    $deja = Reservation::where('nomC', $champ->nomC)->first();
                                    if($deja){
                                        Reservation::create([
                                            'nomEve' => $champ->eve,
                                            'mode' => $champ->stat,
                                            'idSalle' => $champ->des,
                                            'nomC' => $champ->nomC,
                                            'email' => $champ->mail,
                                            'num' => $champ->num,
                                            'adres' => $champ->adrs,
                                            'sexe' => $champ->sexe,
                                            'status' => $champ->info,
                                            'info' => 'En_ligne',
                                            'photo' => $champ->nomInital,
                                            'dateRes' => $champ->datRes,
                                            'dateEven' => $champ->dateEv,
                                            'dateFin' => $dateFin,
                                            'nbrJ' => $champ->nbrJ,
                                            'tot' => $tarif,
                                            'reste' => 0,
                                            'isa' => 'ao',
                                            'confirmation' => 'Valider',
                                            'client' => 'vita',
                                        ]);
                                    }
                                    else{
                                        Reservation::create([
                                            'nomEve' => $champ->eve,
                                            'mode' => $champ->stat,
                                            'idSalle' => $champ->des,
                                            'nomC' => $champ->nomC,
                                            'email' => $champ->mail,
                                            'num' => $champ->num,
                                            'adres' => $champ->adrs,
                                            'sexe' => $champ->sexe,
                                            'status' => $champ->info,
                                            'info' => 'Direct',
                                            'photo' => $champ->nomInital,
                                            'dateRes' => $champ->datRes,
                                            'dateEven' => $champ->dateEv,
                                            'dateFin' => $dateFin,
                                            'nbrJ' => $champ->nbrJ,
                                            'tot' => $tarif,
                                            'reste' => 0,
                                            'isa' => 'tsyao',
                                            'confirmation' => 'Valider',
                                            'client' => 'vita',
                                        ]);
                                    }

                                    Solde::where('id', 1)->update(['solde' => $totalSolde]);
                                    $makaId = Reservation::select('idreserve')
                                    ->where('idSalle', $champ->des)
                                    ->where('nomC', $champ->nomC)
                                    ->where('email', $champ->mail)
                                    ->where('num', $champ->num)
                                    ->where('dateEven', $champ->dateEv)
                                    ->first();
                                    if($makaId){
                                        Occupation::create([
                                            'idSalle' => $champ->des,
                                            'res' => $makaId->idreserve,
                                            'dateEntrer' => $champ->dateEv,
                                            'dateSortie' => $dateFin,
                                        ]);
                                    }
                                }
                            }
                        }
                        else{
                            $deja = Reservation::where('nomC', $champ->nomC)->first();
                            if($deja){
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => 'Gratuit',
                                    'info' => 'En_ligne',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => 0,
                                    'reste' => 0,
                                    'isa' => 'ao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }
                            else{
                                Reservation::create([
                                    'nomEve' => $champ->eve,
                                    'mode' => $champ->stat,
                                    'idSalle' => $champ->des,
                                    'nomC' => $champ->nomC,
                                    'email' => $champ->mail,
                                    'num' => $champ->num,
                                    'adres' => $champ->adrs,
                                    'sexe' => $champ->sexe,
                                    'status' => 'Gratuit',
                                    'info' => 'En_ligne',
                                    'photo' => $champ->nomInital,
                                    'dateRes' => $champ->datRes,
                                    'dateEven' => $champ->dateEv,
                                    'dateFin' => $dateFin,
                                    'nbrJ' => $champ->nbrJ,
                                    'tot' => 0,
                                    'reste' => 0,
                                    'isa' => 'tsyao',
                                    'confirmation' => 'Valider',
                                    'client' => 'vita',
                                ]);
                            }

                            $makaId = Reservation::select('idreserve')
                            ->where('idSalle', $champ->des)
                            ->where('nomC', $champ->nomC)
                            ->where('email', $champ->mail)
                            ->where('num', $champ->num)
                            ->where('dateEven', $champ->dateEv)
                            ->first();
                            if($makaId){
                                Occupation::create([
                                    'idSalle' => $champ->des,
                                    'res' => $makaId->idreserve,
                                    'dateEntrer' => $champ->dateEv,
                                    'dateSortie' => $dateFin,
                                ]);
                            }
                        }
                        return response()->json(['message'=> 'En..']);
                    }
                }
                else{
                    $noti = Notif::select('miampy')->where('id', 1)->first();
                    if($noti){
                        $miampy = $noti->miampy + 1;
                        Notif::where('id', 1)->update(['miampy' => $miampy]);
                    }
                    $solde = Solde::select('solde')->where('id', 1)->first();
                    if($solde){
                        if($champ->stat == 'Payant'){
                            if($champ->info == 'Avance'){
                                $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                                $tot = Reservation::select('tot')->where('idreserve', $champ->idy)->first();
                                if($tot){
                                 if($prix){
                                    $tarif = $prix->tarif * $champ->nbrJ;
                                    $reste = $tarif - $champ->avance;
                                    $soldeee = $solde->solde - $tot->tot;
                                    $totalSolde = $soldeee + $champ->avance;
                                    Reservation::where('idreserve', $champ->idreserve)->update([
                                        'nomEve' => $champ->eve,
                                        'mode' => $champ->stat,
                                        'idSalle' => $champ->des,
                                        'nomC' => $champ->nomC,
                                        'email' => $champ->mail,
                                        'num' => $champ->num,
                                        'adres' => $champ->adrs,
                                        'sexe' => $champ->sexe,
                                        'status' => $champ->info,
                                        'info' => 'En_ligne',
                                        'photo' => $champ->nomInital,
                                        'dateRes' => $champ->datRes,
                                        'dateEven' => $champ->dateEv,
                                        'dateFin' => $dateFin,
                                        'nbrJ' => $champ->nbrJ,
                                        'tot' => $champ->avance,
                                        'reste' => $reste,
                                        'isa' => 'tsyao',
                                        'confirmation' => 'Valider',
                                        'client' => 'vita',
                                    ]);
                                    Solde::where('id', 1)->update(['solde' => $totalSolde]);
                                    $makaId = Reservation::select('idreserve')
                                    ->where('idreserve', $champ->idreserve)
                                    ->first();
                                    if($makaId){
                                        Occupation::create([
                                            'idSalle' => $champ->des,
                                            'res' => $makaId->idreserve,
                                            'dateEntrer' => $champ->dateEv,
                                            'dateSortie' => $dateFin,
                                        ]);
                                    }
                                    }
                                }
                            }else{
                                $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                                $tot = Reservation::select('tot')->where('idreserve', $champ->idreserve)->first();
                                if($tot){
                                   if($prix){
                                    $tarif = $prix->tarif * $champ->nbrJ;
                                    $soldeee = $solde->solde - $tot->tot;
                                    $totalSolde = $soldeee + $tarif;
                                    Reservation::where('idreserve', $champ->idreserve)->update([
                                        'nomEve' => $champ->eve,
                                        'mode' => $champ->stat,
                                        'idSalle' => $champ->des,
                                        'nomC' => $champ->nomC,
                                        'email' => $champ->mail,
                                        'num' => $champ->num,
                                        'adres' => $champ->adrs,
                                        'sexe' => $champ->sexe,
                                        'status' => $champ->info,
                                        'info' => 'En_ligne',
                                        'photo' => $champ->nomInital,
                                        'dateRes' => $champ->datRes,
                                        'dateEven' => $champ->dateEv,
                                        'dateFin' => $dateFin,
                                        'nbrJ' => $champ->nbrJ,
                                        'tot' => $tarif,
                                        'reste' => 0,
                                        'isa' => 'tsyao',
                                        'confirmation' => 'Valider',
                                        'client' => 'vita',
                                    ]);
                                    Solde::where('id', 1)->update(['solde' => $totalSolde]);
                                    $makaId = Reservation::select('idreserve')
                                    ->where('idreserve', $champ->idreserve)
                                    ->first();
                                    if($makaId){
                                        Occupation::create([
                                            'idSalle' => $champ->des,
                                            'res' => $makaId->idreserve,
                                            'dateEntrer' => $champ->dateEv,
                                            'dateSortie' => $dateFin,
                                        ]);
                                    }
                                    }
                                }
                            }
                        }
                        else{
                            Reservation::where('idreserve', $champ->idreserve)->update([
                                'nomEve' => $champ->eve,
                                'mode' => $champ->stat,
                                'idSalle' => $champ->des,
                                'nomC' => $champ->nomC,
                                'email' => $champ->mail,
                                'num' => $champ->num,
                                'adres' => $champ->adrs,
                                'sexe' => $champ->sexe,
                                'status' => 'Gratuit',
                                'info' => 'En_ligne',
                                'photo' => $champ->nomInital,
                                'dateRes' => $champ->datRes,
                                'dateEven' => $champ->dateEv,
                                'dateFin' => $dateFin,
                                'nbrJ' => $champ->nbrJ,
                                'tot' => 0,
                                'reste' => 0,
                                'isa' => 'tsyao',
                                'confirmation' => 'Valider',
                                'client' => 'vita',
                            ]);
                            $makaId = Reservation::select('idreserve')
                                    ->where('idreserve', $champ->idreserve)
                                    ->first();
                                    if($makaId){
                                        Occupation::create([
                                            'idSalle' => $champ->des,
                                            'res' => $makaId->idreserve,
                                            'dateEntrer' => $champ->dateEv,
                                            'dateSortie' => $dateFin,
                                        ]);
                                    }
                        }
                        return response()->json(['message'=> 'En..']);
                    }
                }
            }
        }
        else{
            return response()->json(['message'=>'En...']);
        }
    }






    public function modifRes(Request $champ){
        $dateEv = Carbon::parse($champ->dateEv);
        $nbrJour = $champ->nbrJ - 1;
        $dateFin = $dateEv->addDays($nbrJour)->toDateString();
        $compte = Occupation::where('idSalle', $champ->des)
        ->where('dateEntrer', '<=', $champ->dateEv)
        ->where('dateSortie', '>=', $champ->dateEv)
        ->where('res', '!=', $champ->idy)
        ->orWhere('dateEntrer', '<=', $dateFin)
        ->where('idSalle', $champ->des)
        ->where('dateSortie', '>=', $dateFin)
        ->where('res', '!=', $champ->idy)
        ->orwhere('dateEntrer', '>=', $champ->dateEv)
        ->where('idSalle', $champ->des)
        ->where('dateSortie', '<=', $dateFin)
        ->where('res', '!=', $champ->idy)
        ->count();
        if($compte == 0){
            $solde = Solde::select('solde')->where('id', 1)->first();
            if($solde){
                if($champ->stat == 'Payant'){
                    if($champ->info == 'Avance'){
                        $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                        $tot = Reservation::select('tot')->where('idreserve', $champ->idy)->first();
                        if($tot){
                         if($prix){
                            $tarif = $prix->tarif * $champ->nbrJ;
                            $reste = $tarif - $champ->avance;
                            $soldeee = $solde->solde - $tot->tot;
                            $totalSolde = $soldeee + $champ->avance;
                            Reservation::where('idreserve', $champ->idy)->update([
                                'nomEve' => $champ->eve,
                                'mode' => $champ->stat,
                                'idSalle' => $champ->des,
                                'nomC' => $champ->nomC,
                                'email' => $champ->mail,
                                'num' => $champ->num,
                                'adres' => $champ->adrs,
                                'sexe' => $champ->sexe,
                                'status' => $champ->info,
                                'photo' => $champ->nomInital,
                                'dateEven' => $champ->dateEv,
                                'dateFin' => $dateFin,
                                'nbrJ' => $champ->nbrJ,
                                'tot' => $champ->avance,
                                'reste' => $reste,
                            ]);
                            Solde::where('id', 1)->update(['solde' => $totalSolde]);
                            Occupation::where('res', $champ->idy)->update([
                                    'idSalle' => $champ->des,
                                    'dateEntrer' => $champ->dateEv,
                                    'dateSortie' => $dateFin,
                                ]);
                            }
                        }
                    }else{
                        $prix = Salle::select('tarif')->where('idSalle', $champ->des)->first();
                        $tot = Reservation::select('tot')->where('idreserve', $champ->idy)->first();
                        if($tot){
                           if($prix){
                            $tarif = $prix->tarif * $champ->nbrJ;
                            $soldeee = $solde->solde - $tot->tot;
                            $totalSolde = $soldeee + $tarif;
                            Reservation::where('idreserve', $champ->idy)->update([
                                'nomEve' => $champ->eve,
                                'mode' => $champ->stat,
                                'idSalle' => $champ->des,
                                'nomC' => $champ->nomC,
                                'email' => $champ->mail,
                                'num' => $champ->num,
                                'adres' => $champ->adrs,
                                'sexe' => $champ->sexe,
                                'status' => $champ->info,
                                'photo' => $champ->nomInital,
                                'dateEven' => $champ->dateEv,
                                'dateFin' => $dateFin,
                                'nbrJ' => $champ->nbrJ,
                                'tot' => $tarif,
                                'reste' => 0
                            ]);
                            Solde::where('id', 1)->update(['solde' => $totalSolde]);
                            Occupation::where('res', $champ->idy)->update([
                                    'idSalle' => $champ->des,
                                    'dateEntrer' => $champ->dateEv,
                                    'dateSortie' => $dateFin,
                                ]);
                            }
                        }
                    }
                }
                else{
                    Reservation::where('idreserve', $champ->idy)->update([
                        'nomEve' => $champ->eve,
                        'mode' => $champ->stat,
                        'idSalle' => $champ->des,
                        'nomC' => $champ->nomC,
                        'email' => $champ->mail,
                        'num' => $champ->num,
                        'adres' => $champ->adrs,
                        'sexe' => $champ->sexe,
                        'status' => 'Gratuit',
                        'photo' => $champ->nomInital,
                        'dateEven' => $champ->dateEv,
                        'dateFin' => $dateFin,
                        'nbrJ' => $champ->nbrJ,
                        'tot' => 0,
                        'reste' => 0,
                    ]);
                    Occupation::where('res', $champ->idy)->update([
                        'idSalle' => $champ->des,
                        'dateEntrer' => $champ->dateEv,
                        'dateSortie' => $dateFin,
                    ]);
                }
                return response()->json(['message'=> 'En..']);
            }
        }
        else{
            return response()->json(['message'=>'En...']);
        }
    }





    public function suprimRes(Request $champ){
        $prix = Reservation::select('tot')->where('idreserve', $champ->iden)->first();
        $solde = Solde::select('solde')->where('id', 1)->first();
        if($prix){
            if($solde){
                $totalSolde = $solde->solde - $prix->tot;
                Solde::where('id', 1)->update(['solde' => $totalSolde]);
                Reservation::where('idreserve', $champ->iden)->delete();
                Occupation::where('res', $champ->iden)->delete();
                return response()->json(['message'=>'Suppression avec succés']);
            }
        }
    }

    public function suprimResMultiple(Request $request)
        {
            $ids = $request->ids;
            $prixTotal = 0;
            $prix = Reservation::whereIn('idreserve', $ids)->get(['tot']);
            foreach ($prix as $p) {
                $prixTotal += $p->tot;
            }
            $solde = Solde::select('solde')->where('id', 1)->first();
            if ($solde) {
                $totalSolde = $solde->solde - $prixTotal;
                Solde::where('id', 1)->update(['solde' => $totalSolde]);
                Reservation::whereIn('idreserve', $ids)->delete();
                Occupation::whereIn('res', $ids)->delete();
                return response()->json(['message' => 'Suppression multiple effectuée avec succès']);
            }
        }


}
