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
    Schema::create('logs_api', function (Blueprint $table) {
        $table->id();
        $table->foreignId('usuario_id')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();
        $table->string('endpoint', 255);
        $table->json('params_json')->nullable();
        $table->smallInteger('response_code')->nullable();
        $table->integer('duration_ms')->nullable();
        $table->longText('response_json')->nullable();
        $table->string('error_code', 50)->nullable();
        $table->text('error_desc')->nullable();
        $table->timestamps();

        $table->index('usuario_id');
        $table->index('response_code');
    });
}

public function down(): void
{
    Schema::dropIfExists('logs_api');
}
};
