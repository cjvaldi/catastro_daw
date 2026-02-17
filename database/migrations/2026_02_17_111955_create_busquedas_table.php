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
        Schema::create('busquedas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('query_text', 255);
            $table->string('referencia_busqueda', 32)->nullable();
            $table->json('params_json')->nullable();
            $table->integer('result_count')->default(0);
            $table->timestamps();

            $table->index('usuario_id');
            $table->index('referencia_busqueda');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('busquedas');
    }
};
