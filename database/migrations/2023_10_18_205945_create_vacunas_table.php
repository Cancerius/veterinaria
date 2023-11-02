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
        Schema::create('vacunas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medicos');
            $table->foreign('id_medicos')->references('id')->on('users');
            $table->date('fecha')->nullable();
            $table->string('nombre_propietario')->nullable();
            $table->string('nombre_mascota')->nullable();
            $table->string('vacuna')->nullable();
            $table->string('edad')->nullable();
            $table->string('peso')->nullable();
            $table->integer('celular')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacunas');
    }
};
