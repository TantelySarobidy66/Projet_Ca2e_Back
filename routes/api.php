<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackSalle;
use App\Http\Controllers\BackReservation;
use App\Http\Controllers\BackStatistique;
use App\Http\Controllers\BackOccupation;
use App\Http\Controllers\BackAuthentification;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/selectSalle', [BackSalle::class , 'selectSalles']); //Route api pour le place
Route::get('/selectSalle1', [BackSalle::class , 'selectSalle1']);
Route::post('/createSalle', [BackSalle::class , 'createSalles']);
Route::post('/modifiSalle', [BackSalle::class , 'modifiSalles']);
Route::get('/suprimSalle/{id}', [BackSalle::class , 'suprimSalles']);



Route::get('/selectRes', [BackReservation::class , 'selectRes']); //Route api pour la Reservation
Route::get('/selectRes1', [BackReservation::class , 'selectRes1']);
Route::get('/selectResee/{date}', [BackReservation::class , 'selectResee']);
Route::post('/createRes', [BackReservation::class , 'createRes']);
Route::get('/selectResCl/{id}', [BackReservation::class , 'selectResCl']);
Route::post('/createClient', [BackReservation::class , 'createClient']);
Route::post('/createResClient', [BackReservation::class , 'createResClient']);
Route::post('/createResEnligne', [BackReservation::class , 'createResEnligne']);
Route::post('/modifRes', [BackReservation::class , 'modifRes']);
Route::post('/suprimRes', [BackReservation::class , 'suprimRes']);
Route::post('/suprimResMultiple', [BackReservation::class, 'suprimResMultiple']);


Route::post('/salleLibre', [BackStatistique::class , 'salleLibre']); //Route api pour les statistiques
Route::get('/cout', [BackStatistique::class , 'Count']);
Route::get('/chart1', [BackStatistique::class , 'chart1']);
Route::get('/notif', [BackStatistique::class , 'notif']);
Route::get('/notifUpdate', [BackStatistique::class , 'notifUpdate']);



Route::post('/valider', [BackAuthentification::class , 'valider']);  //Route api pour les authentificaion


Route::get('/getDatesOccupées/{idSalle}', [BackOccupation::class , 'getDatesOccupées']); //Route api por les occupations
