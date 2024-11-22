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
        Schema::create('cirugias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mascota');
            $table->foreign('id_mascota')->references('id')->on('mascotas');
            $table->unsignedBigInteger('id_propietario');
            $table->foreign('id_propietario')->references('id')->on('propietarios');
            $table->unsignedBigInteger('id_veterinario');
            $table->foreign('id_veterinario')->references('id')->on('veterinarios');
            $table->date('fecha_cirugia')->nullable();
            $table->date('fecha_cita')->nullable();
            $table->integer('temperatura')->nullable();
            $table->integer('fre_cardiaca')->nullable();
            $table->integer('fre_respiratoria')->nullable();
            $table->integer('peso')->nullable();
            $table->string('tipo_cirugia')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE cirugias ADD CONSTRAINT chk_temperatura CHECK (LENGTH(temperatura) = 2 AND temperatura REGEXP "^[0-9]+$")');
        DB::statement('ALTER TABLE cirugias ADD CONSTRAINT chk_fre_cardiaca CHECK (LENGTH(fre_cardiaca) >= 2 AND LENGTH(fre_cardiaca) <= 3 AND fre_cardiaca REGEXP "^[0-9]+$")');
        DB::statement('ALTER TABLE cirugias ADD CONSTRAINT chk_fre_respiratoria CHECK (LENGTH(fre_respiratoria) >= 2 AND LENGTH(fre_respiratoria) <= 3 AND fre_respiratoria REGEXP "^[0-9]+$")');
        DB::statement('ALTER TABLE cirugias ADD CONSTRAINT chk_peso CHECK (LENGTH(peso) <= 2 AND peso REGEXP "^[0-9]+$")');
        DB::statement('ALTER TABLE cirugias ADD CONSTRAINT chk_tipo_cirugia CHECK (tipo_cirugia REGEXP "^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$")');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirugias');
    }
};
