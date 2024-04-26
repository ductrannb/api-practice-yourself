<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\CourseUser;
use App\Repositories\LessonRepository;
use App\Utils\Messages;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct(LessonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->responsePaginate($this->repository->getList($request->keyword), LessonResource::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request)
    {
        if (!$this->isAssigned($request->course_id)) {
            throw new RecordsNotFoundException();
        }
        $this->repository->create(array_merge($request->validated(), ['user_id' => auth()->id()]));
        return $this->responseOk(Messages::CREATE_SUCCESS_MESSAGE);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lesson = $this->repository->find($id);
        if (!$this->isAssigned($lesson->course_id)) {
            throw new RecordsNotFoundException();
        }
        return $this->responseOk(data: new LessonResource($lesson));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LessonRequest $request, string $id)
    {
        $lesson = $this->repository->find($id);
        if (!$this->isAssigned($lesson->course_id)) {
            throw new RecordsNotFoundException();
        }
        $this->repository->update($id, $request->validated());
        return $this->responseOk(Messages::UPDATE_SUCCESS_MESSAGE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lesson = $this->repository->find($id);
        if (!$this->isAssigned($lesson->course_id)) {
            throw new RecordsNotFoundException();
        }
        $this->repository->delete($id);
        return $this->responseOk(Messages::DELETE_SUCCESS_MESSAGE);
    }

    private function isAssigned($course_id) : bool
    {
        return (bool)CourseUser::where(['user_id' => auth()->id(), 'course_id' => $course_id])->first();
    }
}
