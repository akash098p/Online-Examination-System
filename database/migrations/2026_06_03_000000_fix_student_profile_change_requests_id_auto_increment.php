<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            'ALTER TABLE student_profile_change_requests MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY'
        );
    }

    public function down(): void
    {
        DB::statement(
            'ALTER TABLE student_profile_change_requests MODIFY id BIGINT UNSIGNED NOT NULL'
        );
    }
};
