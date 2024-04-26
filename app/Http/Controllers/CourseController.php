<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\CourseUser;
use App\Repositories\CourseRepository;
use App\Repositories\CourseUserRepository;
use App\Utils\Constants;
use App\Utils\Messages;
use App\Utils\Uploader;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    use Uploader;

    private $courseUserRepository;

    public function __construct(CourseRepository $repository, CourseUserRepository $courseUserRepository)
    {
        $this->repository = $repository;
        $this->courseUserRepository = $courseUserRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->responsePaginate($this->repository->getList($request->keyword), CourseResource::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request)
    {
        $url = $this->storeFile($request->image, Constants::COURSES_PATH);
        $data = Arr::add(Arr::except($request->all(), ['image', 'teachers']), 'image', $url);
        DB::transaction(function () use ($data, $request) {
            $course = $this->repository->create($data);
            $this->createCourseUser($course, $request->teachers);
        });
        return $this->responseOk(Messages::CREATE_SUCCESS_MESSAGE);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->responseOk(data: new CourseResource($this->repository->find($id)));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request, string $id)
    {
        $data = Arr::except($request->validated(), ['image', 'teachers']);
        if ($request->hasFile('image')) {
            $course = $this->repository->find($id);
            $url = $this->storeFile($request->image, Constants::COURSES_PATH, $course->image);
            $data = array_merge($data, ['image' => $url]);
        }
        DB::transaction(function () use ($id, $data, $request) {
            $course = $this->repository->update($id, $data);
            $course->assigned()->forceDelete();
            $this->createCourseUser($course, $request->teachers);
        });
        return $this->responseOk(Messages::UPDATE_SUCCESS_MESSAGE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = $this->repository->find($id);
        $course->assigned()->delete();
        $course->delete();
        return $this->responseOk(Messages::DELETE_SUCCESS_MESSAGE);
    }

    private function createCourseUser(Course $course, array $teachers) : void
    {
        collect($teachers)->map(function ($teacherId) use ($course) {
            $this->courseUserRepository->create([
                'course_id' => $course->id,
                'user_id' => $teacherId,
                'type' => CourseUser::TYPE_TEACHER
            ]);
        });
    }
}
