<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (! Schema::hasColumn('questions', 'topic')) {
                $table->string('topic')->nullable()->after('question_text');
            }

            if (! Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('marks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('questions', 'topic')) {
                $drops[] = 'topic';
            }

            if (Schema::hasColumn('questions', 'explanation')) {
                $drops[] = 'explanation';
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
