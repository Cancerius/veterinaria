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
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medicos');
            $table->unsignedBigInteger('id_pacientes');
            $table->foreign('id_medicos')->references('id')->on('users');
            $table->foreign('id_pacientes')->references('id')->on('pacientes');
            $table->string('tos')->nullable();
            $table->string('decaimiento')->nullable();
            $table->string('apetito')->nullable();
            $table->string('vomito')->nullable();
            $table->string('lagañas')->nullable();
            $table->string('babeo')->nullable();
            $table->string('sed')->nullable();
            $table->string('estado_reproductivo')->nullable();
            $table->integer('numero_crias')->nullable();
            $table->string('orina')->nullable();
            $table->string('heces')->nullable();
            $table->text('dolores_localizados')->nullable();
            $table->text('diagnostico')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
