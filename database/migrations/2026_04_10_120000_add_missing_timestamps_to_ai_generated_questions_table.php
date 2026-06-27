<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ai_generated_questions')) {
            return;
        }

        Schema::table('ai_generated_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_generated_questions', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('ai_generated_questions', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        DB::table('ai_generated_questions')
            ->whereNull('created_at')
            ->update([
                'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                'updated_at' => DB::raw('COALESCE(updated_at, CURRENT_TIMESTAMP)'),
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('ai_generated_questions')) {
            return;
        }

        Schema::table('ai_generated_questions', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('ai_generated_questions', 'updated_at')) {
                $columnsToDrop[] = 'updated_at';
            }

            if (Schema::hasColumn('ai_generated_questions', 'created_at')) {
                $columnsToDrop[] = 'created_at';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
