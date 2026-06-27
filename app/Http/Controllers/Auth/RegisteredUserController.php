<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\WelcomeStudentNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'college_name' => ['required','string','max:255'],
            'department' => ['required', 'in:'.implode(',', config('academix.departments', []))],
            'sex' => ['required', 'in:male,female'],
            'registration_no' => ['required','string','max:100'],
            'semester' => ['required', 'in:'.implode(',', config('academix.semesters', []))],
            'phone' => ['required','string','max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'college_name' => $request->college_name,
            'department' => $request->department,
            'sex' => strtolower($request->sex),
            'registration_no' => $request->registration_no,
            'semester' => $request->semester,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        event(new Registered($user));

        $user->notify(new WelcomeStudentNotification());

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
