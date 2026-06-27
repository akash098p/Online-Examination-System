<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_result_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('model')->nullable();
            $table->string('prompt_hash')->nullable()->index();
            $table->json('analysis');
            $table->timestamps();

            $table->unique(['result_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_result_analyses');
    }
};
