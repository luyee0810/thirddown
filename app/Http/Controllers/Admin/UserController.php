<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = ['coach', 'parent', 'admin'];

    /**
     * List users, optionally filtered by role.
     */
    public function index(Request $request): View
    {
        $role = $request->query('role');

        $users = User::query()
            ->when(in_array($role, self::ROLES, true), fn ($q) => $q->where('role', $role))
            ->withCount(['classes', 'students'])
            ->orderBy('name')
            ->get();

        $counts = [
            'all' => User::count(),
            'coach' => User::where('role', 'coach')->count(),
            'parent' => User::where('role', 'parent')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'role', 'counts'));
    }

    public function create(Request $request): View
    {
        $role = in_array($request->query('role'), self::ROLES, true) ? $request->query('role') : 'coach';

        return view('admin.users.create', compact('role'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('password_confirmation');
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        return redirect()
            ->route('admin.users.index', ['role' => $user->role])
            ->with('status', "{$user->name} created.");
    }

    public function edit(User $user): View
    {
        $user->loadCount(['classes', 'students']);

        // Other coaches a class can be reassigned to.
        $coaches = User::where('role', 'coach')
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        $classes = $user->classes()->withCount(['sessions', 'students'])->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'coaches', 'classes'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except(['password', 'password_confirmation']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index', ['role' => $user->role])
            ->with('status', "{$user->name} updated.");
    }

    /**
     * Move all of a coach's classes to another coach, so the coach can be deleted.
     */
    public function reassignClasses(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'new_coach_id' => ['required', 'exists:users,id'],
        ]);

        $newCoach = User::where('role', 'coach')->findOrFail($validated['new_coach_id']);

        $user->classes()->update(['coach_id' => $newCoach->id]);

        return back()->with('status', "Classes reassigned to {$newCoach->name}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->classes()->exists()) {
            return back()->withErrors([
                'user' => 'This coach still owns classes. Reassign them to another coach before deleting.',
            ]);
        }

        $role = $user->role;
        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index', ['role' => $role])
            ->with('status', "{$name} deleted.");
    }
}
