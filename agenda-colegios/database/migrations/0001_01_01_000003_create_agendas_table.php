<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('colegio_id');
            $table->unsignedBigInteger('taller_id')->nullable();
            $table->date('fecha');
            $table->time('hora');
            $table->timestamps();

            $table->engine = 'InnoDB';

            // Foreign keys
            $table->foreign('colegio_id')->references('id')->on('colegios')->onDelete('cascade');
            $table->foreign('taller_id')->references('id')->on('talleres')->onDelete('set null');
        });
    }
    public function down(): void {
        Schema::dropIfExists('agendas');
    }
};
