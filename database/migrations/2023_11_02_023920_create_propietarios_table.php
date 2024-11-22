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
        Schema::create('propietarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_veterinario');
            $table->foreign('id_veterinario')->references('id')->on('veterinarios');
            $table->string('nombre_completo')->nullable()->regex('/^[A-Za-z]+$/');
            $table->string('celular', 8)->nullable();
            $table->string('direccion')->nullable();
            $table->timestamps();
        }); 

        DB::statement('ALTER TABLE propietarios ADD CONSTRAINT chk_nombre_completo CHECK (nombre_completo REGEXP "^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$")');
        DB::statement('ALTER TABLE propietarios ADD CONSTRAINT chk_celular CHECK (LENGTH(celular) = 8 AND celular REGEXP "^[0-9]+$")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propietarios');
    }
};
