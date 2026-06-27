<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Violation;
use App\Services\CloudinaryViolationImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ViolationController extends Controller
{
    public function store(Request $request, CloudinaryViolationImageService $cloudinaryViolationImageService)
    {
        $data = $request->validate([
            'exam_id' => 'required|integer|exists:exams,id',
            'reason' => 'required|string|max:120',
            'image' => 'required|string',
        ]);

        $exam = Exam::findOrFail($data['exam_id']);
        $user = $request->user();

        abort_unless($user && $user->role === 'student', 403);
        abort_unless($this->studentCanAttemptExam($exam, $user), 403);

        $storedImage = $this->storeViolationImage($data['image'], $exam, $user, $cloudinaryViolationImageService);

        $violation = Violation::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'reason' => $data['reason'],
            'image_path' => $storedImage['path'],
            'image_public_id' => $storedImage['public_id'],
        ]);

        return response()->json([
            'message' => 'Violation stored.',
            'id' => $violation->id,
        ]);
    }

    public function terminated()
    {
        return view('student.exams.terminated');
    }

    protected function storeViolationImage(
        string $base64Image,
        Exam $exam,
        $user,
        CloudinaryViolationImageService $cloudinaryViolationImageService
    ): array
    {
        if ($this->hasCloudinaryConfig()) {
            try {
                $upload = $cloudinaryViolationImageService->uploadBase64($base64Image, $exam, $user);

                return [
                    'path' => $upload['url'],
                    'public_id' => $upload['public_id'],
                ];
            } catch (\RuntimeException $exception) {
                // Fall back to local storage if the upload fails so the violation is still preserved.
            }
        }

        if (! preg_match('/^data:image\/(?P<extension>png|jpeg|jpg);base64,(?P<data>.+)$/', $base64Image, $matches)) {
            abort(422, 'Invalid image payload.');
        }

        $decoded = base64_decode($matches['data'], true);

        if ($decoded === false) {
            abort(422, 'Corrupted image payload.');
        }

        $extension = $matches['extension'] === 'jpeg' ? 'jpg' : $matches['extension'];
        $fileName = sprintf(
            'violations/exam-%d/user-%d/%s.%s',
            $exam->id,
            $user->id,
            Str::uuid(),
            $extension
        );

        Storage::disk('public')->put($fileName, $decoded);

        return [
            'path' => $fileName,
            'public_id' => null,
        ];
    }

    protected function hasCloudinaryConfig(): bool
    {
        return (string) config('services.cloudinary.cloud_name') !== ''
            && (string) config('services.cloudinary.api_key') !== ''
            && (string) config('services.cloudinary.api_secret') !== '';
    }

    protected function studentCanAttemptExam(Exam $exam, $user): bool
    {
        $semesterAllowed = empty($exam->semester) || in_array($user->semester, (array) $exam->semester, true);
        $departmentAllowed = empty($exam->department) || in_array($user->department, (array) $exam->department, true);

        return $semesterAllowed && $departmentAllowed;
    }
}
