<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Http\Resources\ClassResource;
use App\Models\TrainingClass;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClassController extends Controller
{
    /**
     * List the authenticated coach's classes.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $classes = TrainingClass::query()
            ->where('coach_id', $request->user()->id)
            ->withCount(['sessions', 'students'])
            ->latest()
            ->get();

        return ClassResource::collection($classes);
    }

    /**
     * Create a class and generate its session(s).
     */
    public function store(StoreClassRequest $request, CreateClass $createClass): ClassResource
    {
        $class = $createClass->execute($request->validated(), $request->user()->id);

        return (new ClassResource($class->load('sessions')))
            ->additional(['message' => 'Class created.']);
    }

    /**
     * Show a class with sessions and enrolled students.
     */
    public function show(Request $request, TrainingClass $class): ClassResource
    {
        abort_unless($class->coach_id === $request->user()->id, 403);

        $class->load(['sessions' => fn ($q) => $q->orderBy('session_date'), 'students'])
            ->loadCount(['sessions', 'students']);

        return new ClassResource($class);
    }
}
