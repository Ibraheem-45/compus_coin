<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL]+(?: +[\pL]+)*$/u'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->letters()->numbers()->symbols(),
                'regex:/[^\pL\pN\s]/u',
            ],
            'academic_year' => ['nullable', 'string', 'max:245'],
            'university' => ['nullable', 'string', 'max:255'],
            'monthly_allowance_baseline' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'monthly_saving_goal' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],

        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'academic_year' => $request->academic_year,
            'university' => $request->university ?? 'FAST-NUCES',
            'monthly_allowance_baseline' => $request->monthly_allowance_baseline ?? 0,
            'monthly_saving_goal' => $request->monthly_saving_goal ?? 0,
            'role' => 'student',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
