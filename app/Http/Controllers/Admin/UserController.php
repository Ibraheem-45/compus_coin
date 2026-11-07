<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = User::where('role', 'student');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('university', 'like', '%' . $search . '%');
            });
        }

        $students = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.admin-users', compact('students'));
    }

    public function show(User $user): View
    {
        abort_unless($user->role === 'student', 404);

        $transactionCount = $user->transactions()->count();

        return view('admin.admin-user-details', compact(
            'user',
            'transactionCount'
        ));
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'student', 404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Student password has been reset successfully.');
    }

    public function disable(User $user): RedirectResponse
    {
        abort_unless($user->role === 'student', 404);

        $user->status = 'disabled';
        $user->save();

        return redirect()
            ->route('admin.users')
            ->with('success', 'Student account has been disabled successfully.');
    }

    public function enable(User $user): RedirectResponse
    {
        abort_unless($user->role === 'student', 404);

        $user->status = 'active';
        $user->save();

        return redirect()
            ->route('admin.users')
            ->with('success', 'Student account has been enabled successfully.');
    }
}
