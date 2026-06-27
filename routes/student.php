<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ExamController;
use App\Http\Controllers\Student\ResultController;
use App\Http\Controllers\Student\AiResultController;

Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware(['auth'])
            ->name('dashboard');

        Route::get('/exams', [ExamController::class, 'index'])
            ->name('exams.index');

        Route::get('/exams/{examId}/start', [ExamController::class, 'start'])
            ->name('exams.start');

        Route::post('/exams/{examId}/submit', [ExamController::class, 'submit'])
            ->name('exams.submit');

        Route::post('/exams/{examId}/autosave', [ExamController::class, 'autosave'])
            ->name('exams.autosave');

        Route::get('/exams/{examId}/load-saved', [ExamController::class, 'loadSaved'])
            ->name('exams.load_saved');

        Route::get('/exams/{examId}/result', [ExamController::class, 'result'])
            ->name('exams.result');

        Route::get('/results', [ResultController::class, 'index'])
            ->name('results.index');

        Route::get('/results/{result}', [ResultController::class, 'show'])
            ->name('results.show');
        Route::post('/results/{result}/ai-analysis', [AiResultController::class, 'analyze'])
            ->middleware('throttle:ai')
            ->name('results.ai_analysis');
        Route::post('/results/{result}/ai-chat', [AiResultController::class, 'chat'])
            ->middleware('throttle:ai')
            ->name('results.ai_chat');
    });
