<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Salle;

class BackSalle extends Controller
{

    public function selectSalles()
    {
        $salle = Salle::get();
        return response()->json(['liste' => $salle]);
    }


    public function selectSalle1(){
        $salle = Salle::select('idSalle', 'designe')->get();
        return response()->json(['liste' => $salle]);
    }


    public function createSalles(Request $champ)
    {
        $deja = Salle::where('designe', $champ->des)->first();
        if ($deja) {
            $me = 'en cours....';
            return response()->json(['message' => $me], 200);
        } else {
           $photo_name= Str::random(32).".".$champ->sary->getClientOriginalExtension();
            Salle::create([
                'designe' => $champ->des,
                'photo' => $photo_name,
                'espace' => $champ->esp,
                'tarif' => $champ->tarif,
            ]);
            Storage::disk('public')->put($photo_name, file_get_contents($champ->sary));
            $me = 'en cours...';
            return response()->json(['message' => $me], 200);
        }
    }


    public function modifiSalles(Request $champ)
     {
         $deja = Salle::where('designe', $champ->des)->where('idSalle', '!=', $champ->id)->first();
         if ($deja) {
             $me = 'en cours....';
             return response()->json(['message' => $me]);
         } else {
            $res = Salle::find($champ->id); //maka id

            $storage = Storage::disk('public');
            if($storage->exists($res->photo))
               $storage->delete($res->photo); //mamafa ny ami back sy storage

            $photo_name= Str::random(32).".".$champ->sary->getClientOriginalExtension();
            $storage->put($photo_name, file_get_contents($champ->sary));
             Salle::where('idSalle', $champ->id)->update([
                    'designe' => $champ->des,
                    'photo' => $photo_name,
                    'espace' => $champ->esp,
                    'tarif' => $champ->tarif,
             ]);
             $me = 'en cours...';
             return response()->json(['message' => $me]);
         }
     }


     public function suprimSalles(Salle $id)
     {

         $storage = Storage::disk('public');
            if($storage->exists($id->photo))
               $storage->delete($id->photo);
         $id->delete();
         return response()->json([
             'message' => 'supression avec succés',
         ]);
     }

}
