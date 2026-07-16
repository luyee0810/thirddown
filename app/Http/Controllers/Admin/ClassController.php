<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $classes = TrainingClass::query()
            ->with('coach')
            ->withCount(['sessions', 'students'])
            ->orderBy('name')
            ->get();

        $coaches = User::where('role', 'coach')->orderBy('name')->get();

        return view('admin.classes.index', compact('classes', 'coaches'));
    }

    public function updateCoach(Request $request, TrainingClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'coach_id' => ['required', 'exists:users,id'],
        ]);

        $coach = User::where('role', 'coach')->findOrFail($validated['coach_id']);

        $class->update(['coach_id' => $coach->id]);

        return back()->with('status', "{$class->name} reassigned to {$coach->name}.");
    }

    public function destroy(TrainingClass $class): RedirectResponse
    {
        $name = $class->name;

        // Sessions, enrollments and attendance cascade on delete at the DB level.
        $class->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('status', "{$name} deleted.");
    }
}
