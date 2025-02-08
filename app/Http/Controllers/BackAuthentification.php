<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class BackAuthentification extends Controller
{
    public function valider(Request $champ){
        $Reservation = Reservation::where('email', $champ->email)->where('mdp', $champ->mdp)->first();
        if(!$Reservation){
            return response()->json(['message' => 'incorrect']);
        }
        else{
            $getId = Reservation::select('idreserve')->where('email', $champ->email)->where('mdp', $champ->mdp)->first();
            $nom = Reservation::select('nomC')->where('email', $champ->email)->where('mdp', $champ->mdp)->first();
            $adrs = Reservation::select('adres')->where('email', $champ->email)->where('mdp', $champ->mdp)->first();
            $sexe = Reservation::select('sexe')->where('email', $champ->email)->where('mdp', $champ->mdp)->first();
            $num = Reservation::select('num')->where('email', $champ->email)->where('mdp', $champ->mdp)->first();
            $photo = Reservation::select('photo')->where('email', $champ->email)->where('mdp', $champ->mdp)->first();
            if($getId){
                if($nom){
                    if($adrs){
                        if($sexe){
                            if($num){
                                if($photo){
                                        return response()->json(['message' => 'correct', 'idreserve' => $getId->idreserve, 'email' => $champ->email, 'mdp' => $champ->mdp
                                        , 'num' => $num->num , 'sexe' => $sexe->sexe , 'adres' => $adrs->adres , 'nomC' => $nom->nomC, 'photo' => $photo->photo
                                    ]);
                                }

                            }
                        }
                    }
                }
            }
        }
    }

}
