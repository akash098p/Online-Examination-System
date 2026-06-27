<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->boolean('proctoring_enabled')->default(false)->after('negative_marking');
            $table->boolean('require_camera')->default(false)->after('proctoring_enabled');
            $table->boolean('require_microphone')->default(false)->after('require_camera');
            $table->boolean('detect_no_face')->default(false)->after('require_microphone');
            $table->boolean('detect_multiple_faces')->default(false)->after('detect_no_face');
            $table->boolean('detect_talking')->default(false)->after('detect_multiple_faces');
            $table->unsignedTinyInteger('max_warnings')->default(5)->after('detect_talking');
            $table->unsignedSmallInteger('pre_exam_countdown_seconds')->default(10)->after('max_warnings');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'proctoring_enabled',
                'require_camera',
                'require_microphone',
                'detect_no_face',
                'detect_multiple_faces',
                'detect_talking',
                'max_warnings',
                'pre_exam_countdown_seconds',
            ]);
        });
    }
};
