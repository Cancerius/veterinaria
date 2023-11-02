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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_propietario')->nullable();
            $table->integer('telefono')->nullable();
            $table->string('nombre_mascota')->nullable();
            $table->string('sexo_mascota')->nullable();
            $table->string('raza_mascota')->nullable();
            $table->string('edad')->nullable();
            $table->string('peso')->nullable();
            $table->string('color')->nullable();
            $table->date('fecha')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
