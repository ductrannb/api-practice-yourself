<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Http\Resources\ExamDetailResource;
use App\Http\Resources\ExamResource;
use App\Repositories\ExamRepository;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(ExamRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->responsePaginate(
            $this->repository->getList($request->keyword, $request->author_id),
            ExamResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExamRequest $request)
    {
        $this->repository->create(array_merge($request->validated(), ['user_id' => auth()->id()]));
        return $this->createdSuccess();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->responseOk(data: new ExamDetailResource($this->repository->find($id, ['questions', 'author'])));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExamRequest $request, string $id)
    {
        $this->repository->update($id, $request->validated());
        return $this->updatedSuccess();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->repository->delete($id);
        return $this->deletedSuccess();
    }

    public function getName($id)
    {
        $record = $this->repository->find($id);
        return $this->responseOk(data: ['exam_name' => $record->name]);
    }
}
