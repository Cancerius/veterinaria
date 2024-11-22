<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vacunacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mascota');
            $table->foreign('id_mascota')->references('id')->on('mascotas');
            $table->unsignedBigInteger('id_veterinario');
            $table->foreign('id_veterinario')->references('id')->on('veterinarios');
            $table->unsignedBigInteger('id_propietario');
            $table->foreign('id_propietario')->references('id')->on('propietarios');
            $table->unsignedBigInteger('id_producto');
            $table->foreign('id_producto')->references('id')->on('productos');
            $table->date('fecha')->nullable();
            $table->date('fecha_cita')->nullable();
            $table->integer('costo')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE vacunacion ADD CONSTRAINT chk_costo CHECK (LENGTH(costo) >= 2 AND LENGTH(costo) <= 3 AND costo REGEXP "^[0-9]+$")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacunacion');
    }
};
