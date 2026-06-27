<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users',
            'exams',
            'questions',
            'options',
            'results',
            'responses',
            'exam_attempts',
            'ai_generated_questions',
            'ai_result_analyses',
            'ai_chat_messages',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'id')) {
                continue;
            }

            $column = DB::selectOne("SHOW COLUMNS FROM `{$table}` LIKE 'id'");

            if (
                !$column
                || $column->Key !== 'PRI'
                || str_contains((string) $column->Extra, 'auto_increment')
            ) {
                continue;
            }

            DB::statement(sprintf(
                'ALTER TABLE `%s` MODIFY `id` %s NOT NULL AUTO_INCREMENT',
                $table,
                $column->Type
            ));
        }
    }

    public function down(): void
    {
        // Intentional no-op. This migration repairs malformed primary key definitions.
    }
};
