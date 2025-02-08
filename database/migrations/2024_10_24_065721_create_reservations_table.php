<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->bigIncrements('idreserve');
            $table->string('nomEve')->nullable();
            $table->string('mode')->nullable();
            $table->foreignId('idSalle')->nullable()->constrained('salles', 'idSalle')->onDelete('cascade');
            $table->string('nomC');
            $table->string('email');
            $table->string('num');
            $table->string('adres');
            $table->string('sexe');
            $table->string('status')->nullable();
            $table->string('info')->nullable();
            $table->string('photo')->nullable();
            $table->date('dateRes')->nullable();
            $table->date('dateEven')->nullable();
            $table->date('dateFin')->nullable();
            $table->integer('nbrJ')->nullable();
            $table->integer('tot')->nullable();
            $table->integer('reste')->nullable();
            $table->string('isa')->nullable();
            $table->string('confirmation')->nullable();
            $table->string('client')->nullable();
            $table->string('mdp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
