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
        Schema::create('municipios', function (Blueprint $table) {
            $table->string('codigo',8)->primary();
            $table->string('nombre',150);

            $table->string('provincia_codigo',4)->nullable();

            $table->index('provincia_codigo');

            $table->foreign('provincia_codigo')
            ->references('codigo')
            ->on('provincias')
            ->nullOnDelete();
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
