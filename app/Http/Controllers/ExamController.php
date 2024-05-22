<?php

namespace App\Http\Controllers;

use App\Helpers\MathpixHelper;
use App\Http\Requests\ExamRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\ExamDetailResource;
use App\Http\Resources\ExamResource;
use App\Jobs\ImportQuestionsJob;
use App\Models\Question;
use App\Repositories\ExamRepository;
use App\Utils\Messages;
use App\Utils\Uploader;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    use Uploader;

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
        if ($request->has('pdf_id')) {
            dispatch(new ImportQuestionsJob($exam, $request->pdf_id, ImportQuestionsJob::TYPE_EXAM, auth()->id()));
            return $this->responseOk(Messages::CREATE_AND_IMPORT_QUESTION_MESSAGE);
        }
        return $this->createdSuccess();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->responseOk(data: new ExamDetailResource($this->repository->find($id, ['questions.correctChoices', 'questions.choices', 'questions.author', 'author'])));
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
