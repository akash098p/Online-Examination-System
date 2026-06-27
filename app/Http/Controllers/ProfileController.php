<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\StudentProfileChangeRequest;
use App\Services\CloudinaryProfilePhotoService;
use RuntimeException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
            'pendingAcademicRequest' => $user->pendingProfileChangeRequest()->first(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, CloudinaryProfilePhotoService $profilePhotoService): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Fill normal profile fields (exclude photo controls)
        unset($validated['profile_photo'], $validated['remove_profile_photo']);

        if ($user->role === 'student') {
            $requestedDepartment = $validated['department'] ?? $user->department;
            $requestedSemester = $validated['semester'] ?? $user->semester;

            unset($validated['department'], $validated['semester']);

            if ($requestedDepartment !== $user->department || $requestedSemester !== $user->semester) {
                StudentProfileChangeRequest::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'status' => 'pending',
                    ],
                    [
                        'current_department' => $user->department,
                        'requested_department' => $requestedDepartment,
                        'current_semester' => $user->semester,
                        'requested_semester' => $requestedSemester,
                        'reviewed_by' => null,
                        'reviewed_at' => null,
                    ]
                );
            }
        }

        $user->fill($validated);

        // Remove current profile photo and fall back to default avatar
        if ($request->boolean('remove_profile_photo') && $user->profile_photo) {
            $profilePhotoService->deleteFromUser($user);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            try {
                $profilePhotoService->deleteFromUser($user);

                $upload = $profilePhotoService->upload($request->file('profile_photo'), $user);
                $user->profile_photo = $upload['url'];
                $user->profile_photo_public_id = $upload['public_id'];
            } catch (RuntimeException $exception) {
                return Redirect::route('profile.edit')
                    ->withInput()
                    ->withErrors(['profile_photo' => $exception->getMessage()]);
            }
        }

        // Reset email verification if email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
