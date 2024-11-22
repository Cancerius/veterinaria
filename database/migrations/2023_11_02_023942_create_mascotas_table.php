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
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_propietario');
            $table->foreign('id_propietario')->references('id')->on('propietarios');
            $table->unsignedBigInteger('id_veterinario');
            $table->foreign('id_veterinario')->references('id')->on('veterinarios');
            $table->string('nombre_mascota')->nullable();
            $table->string('raza')->nullable();
            $table->string('sexo')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('peso')->nullable();
            $table->string('color')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
        
        DB::statement('ALTER TABLE mascotas ADD CONSTRAINT chk_nombre_mascota CHECK (nombre_mascota REGEXP "^[A-Za-z ]+$")');
        DB::statement('ALTER TABLE mascotas ADD CONSTRAINT chk_raza CHECK (raza REGEXP "^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$")');
        DB::statement('ALTER TABLE mascotas ADD CONSTRAINT chk_sexo CHECK (sexo REGEXP "^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$")');
       /* DB::statement('ALTER TABLE mascotas ADD CONSTRAINT chk_fecha_nacimiento CHECK (YEAR(fecha_nacimiento) >= 2005 AND LENGTH(YEAR(fecha_nacimiento)) = 4)');*/
        DB::statement('ALTER TABLE mascotas ADD CONSTRAINT chk_peso CHECK (LENGTH(peso) <= 2 AND peso REGEXP "^[0-9]+$")');
        DB::statement('ALTER TABLE mascotas ADD CONSTRAINT chk_color CHECK (sexo REGEXP "^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$")');
    }


    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
