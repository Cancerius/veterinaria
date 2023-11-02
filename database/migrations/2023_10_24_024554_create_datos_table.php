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
        Schema::create('datos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medicos');
            $table->unsignedBigInteger('id_pacientes');
            $table->foreign('id_medicos')->references('id')->on('users');
            $table->foreign('id_pacientes')->references('id')->on('pacientes');
            $table->string('temperatura')->nullable();
            $table->string('pulso')->nullable();
            $table->string('respiracion')->nullable();
            $table->string('desidratacion')->nullable();
            $table->string('pupilas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos');
    }
};
