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
        Schema::create('unidades_constructivas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('propiedad_id')
                ->constrained('propiedades')
                ->cascadeOnDelete();

            $table->string('tipo_unidad',100)->nullable();
            $table->string('tipologia',150)->nullable();
            $table->decimal('superficie_m2',12,2)->nullable();
            $table->string('localizacion_externa',100)->nullable();

            $table->json('raw_json')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades_constructivas');
    }
};
