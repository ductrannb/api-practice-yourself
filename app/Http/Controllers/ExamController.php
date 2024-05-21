<?php

namespace App\Http\Controllers;

use App\Helpers\MathpixHelper;
use App\Http\Requests\ExamRequest;
use App\Http\Resources\ExamDetailResource;
use App\Http\Resources\ExamResource;
use App\Models\Question;
use App\Repositories\ExamRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    private $mathpixHelper;
    public function __construct(ExamRepository $repository, MathpixHelper $mathpixHelper)
    {
        $this->repository = $repository;
        $this->mathpixHelper = $mathpixHelper;
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
        $exam = $this->repository->create(array_merge($request->validated(), ['user_id' => auth()->id()]));
        $pdfId = '2024_05_21_71213a69eeeabc6dddfeg';
        $questions = $this->mathpixHelper->getPdfLinesData($pdfId);
        collect($questions)->each(function ($question) use ($exam) {
            DB::transaction(function () use ($question, $exam) {
                $q = $exam->questions()->create([
                    'content' => $question->content,
                    'user_id' => auth()->id(),
                    'assignable_type' => Question::TYPE_EXAM,
                    'assignable_id' => $exam->id,
                ]);
                collect($question->choices)->each(function ($choice) use ($q) {
                    $q->choices()->create([
                        'content' => $choice['content'],
                        'is_correct' => $choice['is_correct']
                    ]);
                });
            });
        });
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
