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
        Schema::create('propiedades', function (Blueprint $table) {
            $table->id();

            $table->string('referecia_catastral', 32)->unique();
            $table->string('clase', 4)->nullable();

            $table->string('provincia_codigo', 4)->nullable();
            $table->string('municipio_codigo', 8)->nullable();

            $table->string('provincia', 100)->nullable();
            $table->string('municipio', 150)->nullable();

            $table->string('direccion_text')->nullable();

            $table->string('tipo:Via', 10)->nullable();
            $table->string('nombre_via', 200)->nullable();
            $table->string('numero', 50)->nullable();
            $table->string('bloque', 50)->nullable();
            $table->string('escalera', 50)->nullable();
            $table->string('planta', 50)->nullable();
            $table->string('puerta', 50)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->string('distrito_municipal', 50)->nullable();

            $table->string('uso', 50)->nullable();
            $table->decimal('superficie_m2', 12, 2)->nullable();
            $table->decimal('coef_participacion', 10, 4)->nullable();
            $table->integer('antiguedad_anios')->nullable();

            $table->text('domicilio_tributario')->nullable();

            $table->json('raw_json')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('provincia_codigo');
            $table->index('municipio_codigo');
            $table->index('uso');

            //Foreign Keys
            $table->foreign('provincia_codigo')
                ->references('codigo')
                ->on('provincias')
                ->nullOnDelete();

            $table->foreign('municipio_codigo')
                ->references('codigo')
                ->on('municipios')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propiedades');
    }
};
