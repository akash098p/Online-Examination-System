<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Result;
use App\Services\CloudinaryProfilePhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class StudentController extends Controller
{
    // 📘 All students list
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $selectedDepartments = $this->normalizeMultiSelectFilter(
            (array) $request->query('department', []),
            (array) config('academix.departments', [])
        );
        $selectedSemesters = $this->normalizeMultiSelectFilter(
            (array) $request->query('semester', []),
            (array) config('academix.semesters', [])
        );

        $query = User::withTrashed()->where('role', 'student');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('registration_no', 'like', "%{$search}%");
            });
        }

        if (count($selectedDepartments) > 0) {
            $query->whereIn('department', $selectedDepartments);
        }

        if (count($selectedSemesters) > 0) {
            $query->whereIn('semester', $selectedSemesters);
        }

        $students = $query->latest()->paginate(10)->appends($request->only('search', 'semester', 'department'));

        return view('admin.students.index', compact('students', 'search', 'selectedDepartments', 'selectedSemesters'));
    }

    protected function normalizeMultiSelectFilter(array $selectedValues, array $allOptions): array
    {
        $selectedValues = array_values(array_unique(array_filter(array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $selectedValues), fn ($value) => $value !== '' && $value !== null)));
        $allOptions = array_values(array_unique(array_filter(array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $allOptions), fn ($value) => $value !== '' && $value !== null)));

        if (count($selectedValues) === 0) {
            return [];
        }

        sort($selectedValues);
        sort($allOptions);

        if ($selectedValues === $allOptions) {
            return [];
        }

        return $selectedValues;
    }

    // ➕ Show create student form
    public function create()
    {
        return view('admin.students.create');
    }

    // 💾 Store new student (FULL REGISTRATION STYLE)
    public function store(Request $request, CloudinaryProfilePhotoService $profilePhotoService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'college_name' => 'required|string|max:255',
            'department' => 'required|in:'.implode(',', config('academix.departments', [])),
            'registration_no' => 'required|unique:users,registration_no',
            'semester' => 'required|in:'.implode(',', config('academix.semesters', [])),
            'phone' => 'required|string|max:20',
            'sex' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'password' => 'required|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'college_name' => $request->college_name,
            'department' => $request->department,
            'registration_no' => $request->registration_no,
            'semester' => $request->semester,
            'phone' => $request->phone,
            'sex' => strtolower($request->sex),
            'date_of_birth' => $request->date_of_birth,
            'role' => 'student',
            'password' => Hash::make($request->password),
        ];

        $student = User::create($data);

        if ($request->hasFile('profile_photo')) {
            try {
                $upload = $profilePhotoService->upload($request->file('profile_photo'), $student);
                $student->profile_photo = $upload['url'];
                $student->profile_photo_public_id = $upload['public_id'];
                $student->save();
            } catch (RuntimeException $exception) {
                $student->delete();

                return back()
                    ->withInput()
                    ->withErrors(['profile_photo' => $exception->getMessage()]);
            }
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student added successfully.');
    }

    // 👤 Student full profile
    public function show($id)
    {
        $student = User::withTrashed()->where('role', 'student')->findOrFail($id);

        $results = Result::where('user_id', $id)
            ->with('exam')
            ->latest()
            ->get();

        return view('admin.students.show', compact('student', 'results'));
    }

    // ✏ Edit student form
    public function edit($id)
    {
        $student = User::withTrashed()->where('role', 'student')->findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    // 🔄 Update student (INCLUDING PASSWORD RESET)
    public function update(Request $request, $id, CloudinaryProfilePhotoService $profilePhotoService)
    {
        $student = User::withTrashed()->where('role', 'student')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'college_name' => 'required|string|max:255',
            'department' => 'required|in:'.implode(',', config('academix.departments', [])),
            'registration_no' => 'required|unique:users,registration_no,' . $id,
            'semester' => 'required|in:'.implode(',', config('academix.semesters', [])),
            'phone' => 'required|string|max:20',
            'sex' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'password' => 'nullable|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'remove_profile_photo' => 'nullable|boolean',
        ]);

        $student->name = $request->name;
        $student->email = $request->email;
        $student->college_name = $request->college_name;
        $student->department = $request->department;
        $student->registration_no = $request->registration_no;
        $student->semester = $request->semester;
        $student->phone = $request->phone;
        $student->sex = strtolower($request->sex);
        $student->date_of_birth = $request->date_of_birth;

        if ($request->boolean('remove_profile_photo') && $student->profile_photo) {
            $profilePhotoService->deleteFromUser($student);
        }

        if ($request->hasFile('profile_photo')) {
            try {
                $profilePhotoService->deleteFromUser($student);

                $upload = $profilePhotoService->upload($request->file('profile_photo'), $student);
                $student->profile_photo = $upload['url'];
                $student->profile_photo_public_id = $upload['public_id'];
            } catch (RuntimeException $exception) {
                return back()
                    ->withInput()
                    ->withErrors(['profile_photo' => $exception->getMessage()]);
            }
        }

        // ✅ Only update password if admin entered new one
        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }

        $student->save();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    // 🚫 Block / unblock student
    public function toggleStatus($id)
    {
        $student = User::withTrashed()->findOrFail($id);

        if ($student->trashed()) {
            return back()->with('success', 'Restore the student account before changing blocked status.');
        }

        $student->is_blocked = !$student->is_blocked;
        $student->save();

        return back()->with('success', 'Student status updated.');
    }

    // 🗑 Delete student
    public function destroy($id)
    {
        $student = User::withTrashed()->findOrFail($id);

        if ($student->trashed()) {
            $student->restore();

            return back()->with('success', 'Student account restored.');
        }

        $student->delete();

        return back()->with('success', 'Student removed from app access.');
    }
}
