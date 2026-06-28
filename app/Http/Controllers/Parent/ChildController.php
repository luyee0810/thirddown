<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChildRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChildController extends Controller
{
    /**
     * The parent's dashboard — a list of their children.
     */
    public function index(): View
    {
        $children = Auth::user()->students()
            ->withCount('classes')
            ->orderBy('first_name')
            ->get();

        return view('parent.dashboard', compact('children'));
    }

    /**
     * Show the add-a-child form.
     */
    public function create(): View
    {
        return view('parent.children.create');
    }

    /**
     * Store a new child for the signed-in parent.
     */
    public function store(StoreChildRequest $request): RedirectResponse
    {
        $parent = Auth::user();

        $child = $parent->students()->create($this->childData($request) + [
            'parent_name' => $parent->name,
            'parent_email' => $parent->email,
            'parent_phone' => $parent->phone,
            'is_active' => true,
        ]);

        return redirect()
            ->route('parent.dashboard')
            ->with('status', "{$child->full_name} added.");
    }

    /**
     * Show the edit form for one of the parent's children.
     */
    public function edit(Student $child): View
    {
        $this->authorizeChild($child);

        return view('parent.children.edit', compact('child'));
    }

    /**
     * Update one of the parent's children.
     */
    public function update(StoreChildRequest $request, Student $child): RedirectResponse
    {
        $this->authorizeChild($child);

        $child->update($this->childData($request, $child));

        return redirect()
            ->route('parent.dashboard')
            ->with('status', "{$child->full_name} updated.");
    }

    /**
     * Validated student fields plus an optional uploaded photo path.
     *
     * @return array<string, mixed>
     */
    private function childData(StoreChildRequest $request, ?Student $child = null): array
    {
        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            if ($child?->photo_path && ! str_starts_with($child->photo_path, 'http')) {
                Storage::disk('public')->delete($child->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('students', 'public');
        }

        return $data;
    }

    /**
     * Ensure the child belongs to the signed-in parent.
     */
    private function authorizeChild(Student $child): void
    {
        abort_unless($child->parent_id === Auth::id(), 403);
    }
}
