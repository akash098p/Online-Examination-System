<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\ExamReportController;
use App\Http\Controllers\Admin\ExamAnalysisController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AiQuestionController;
use App\Http\Controllers\Admin\ViolationController;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /* ===========================
           DASHBOARD
        ============================ */

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/dashboard/profile-change-requests', [DashboardController::class, 'profileChangeRequests'])
            ->name('profile_requests.index');
        Route::post('/dashboard/profile-change-requests/{profileRequest}/approve', [DashboardController::class, 'approveProfileChangeRequest'])
            ->name('profile_requests.approve');
        Route::post('/dashboard/profile-change-requests/{profileRequest}/reject', [DashboardController::class, 'rejectProfileChangeRequest'])
            ->name('profile_requests.reject');

        Route::get('/analytics', [DashboardController::class, 'analytics'])
            ->name('analytics');

        /* ===========================
           EXAMS
        ============================ */

        Route::post('exams/{exam}/toggle-publish', [ExamController::class, 'togglePublish'])
            ->name('exams.toggle_publish');
        Route::get('exams/department/{department}', [ExamController::class, 'department'])
            ->name('exams.department');

        Route::resource('exams', ExamController::class);
        Route::post('exams/{exam}/ai-questions/generate', [AiQuestionController::class, 'generate'])
            ->middleware('throttle:ai')
            ->name('exams.ai_questions.generate');
        Route::patch('exams/{exam}/ai-questions/{generatedQuestion}', [AiQuestionController::class, 'update'])
            ->name('exams.ai_questions.update');
        Route::post('exams/{exam}/ai-questions/{generatedQuestion}/approve', [AiQuestionController::class, 'approve'])
            ->name('exams.ai_questions.approve');
        Route::post('exams/{exam}/ai-questions/{generatedQuestion}/reject', [AiQuestionController::class, 'reject'])
            ->name('exams.ai_questions.reject');

        /* ===========================
           QUESTIONS
        ============================ */

        Route::get('exams/{exam}/questions', [QuestionController::class, 'index'])
            ->name('questions.index');

        Route::get('exams/{exam}/questions/create', [QuestionController::class, 'create'])
            ->name('questions.create');

        Route::post('exams/{exam}/questions', [QuestionController::class, 'store'])
            ->name('questions.store');

        Route::get('questions/{question}/edit', [QuestionController::class, 'edit'])
            ->name('questions.edit');

        Route::put('questions/{question}', [QuestionController::class, 'update'])
            ->name('questions.update');

        Route::delete('questions/{question}', [QuestionController::class, 'destroy'])
            ->name('questions.destroy');

        /* ===========================
           OPTIONS
        ============================ */

        Route::post('questions/{question}/options', [OptionController::class, 'store'])
            ->name('options.store');

        Route::put('options/{option}', [OptionController::class, 'update'])
            ->name('options.update');

        Route::delete('options/{option}', [OptionController::class, 'destroy'])
            ->name('options.destroy');

        Route::post('questions/{question}/options/reorder', [OptionController::class, 'reorder'])
            ->name('options.reorder');

        /* ===========================
           RESULTS
        ============================ */

        Route::get('/results', [ResultController::class, 'index'])
            ->name('results.index');

        Route::get('/results/{exam}', [ResultController::class, 'show'])
            ->name('results.show');

        Route::get('/results/sheet/{result}', [ResultController::class, 'sheet'])
            ->name('results.sheet');

        Route::get('/violations', [ViolationController::class, 'index'])
            ->name('violations.index');
        Route::get('/violations/exams/{exam}', [ViolationController::class, 'exam'])
            ->name('violations.exam');
        Route::get('/violations/students/{userId}', [ViolationController::class, 'studentOverview'])
            ->name('violations.student_overview');
        Route::get('/violations/exams/{exam}/students/{userId}', [ViolationController::class, 'student'])
            ->name('violations.student');
        Route::get('/violations/{violation}/image', [ViolationController::class, 'image'])
            ->name('violations.image');

        /* ===========================
           EXAM PERFORMANCE SYSTEM
        ============================ */

        Route::get('/exam-performance', [ExamReportController::class, 'index'])
            ->name('performance.index');

        Route::get('/exam-performance/{examId}', [ExamReportController::class, 'show'])
            ->name('performance.show');

        Route::get('/exam-performance/{examId}/student/{userId}', [ExamReportController::class, 'student'])
            ->name('performance.student');

        /* ===========================
           EXAM ANALYSIS
        ============================ */

        Route::get('/analysis/exams', [ExamAnalysisController::class, 'index'])
            ->name('analysis.exams');

        Route::get('/analysis/exams/{exam}', [ExamAnalysisController::class, 'showExam'])
            ->name('analysis.exam.students');

        Route::get('/analysis/attempt/{attempt}', [ExamAnalysisController::class, 'showStudent'])
            ->name('analysis.student.result');

        /* ===========================
           STUDENT MANAGEMENT
        ============================ */

        // List students
        Route::get('/students', [StudentController::class, 'index'])
            ->name('students.index');

        // Create student
        Route::get('/students/create', [StudentController::class, 'create'])
            ->name('students.create');

        Route::post('/students', [StudentController::class, 'store'])
            ->name('students.store');

        // Edit student
        Route::get('/students/{id}/edit', [StudentController::class, 'edit'])
            ->name('students.edit');

        Route::put('/students/{id}', [StudentController::class, 'update'])
            ->name('students.update');

        // View student profile
        Route::get('/students/{id}', [StudentController::class, 'show'])
            ->name('students.show');

        // Block / Unblock
        Route::post('/students/{id}/toggle', [StudentController::class, 'toggleStatus'])
            ->name('students.toggle');

        // Delete student
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])
            ->name('students.destroy');

});
