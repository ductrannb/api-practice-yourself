<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Jobs\ImportQuestionsJob;
use App\Models\Question;
use App\Repositories\ExamRepository;
use App\Repositories\LessonRepository;
use App\Repositories\QuestionChoiceRepository;
use App\Repositories\QuestionRepository;
use App\Utils\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    private $questionChoiceRepository;
    private $lessonRepository;
    private $examRepository;
    private const ASSIGNABLE_TYPE_LESSON = 1;
    private const ASSIGNABLE_TYPE_EXAM = 2;

    public function __construct(
        QuestionRepository $questionRepository,
        QuestionChoiceRepository $questionChoiceRepository,
        LessonRepository $lessonRepository,
        ExamRepository $examRepository
    ) {
        $this->repository = $questionRepository;
        $this->questionChoiceRepository = $questionChoiceRepository;
        $this->lessonRepository = $lessonRepository;
        $this->examRepository = $examRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $questions = $this->repository->getList(
            $request->keyword,
            $request->level,
            $request->learning_module_id,
            $request->learning_module_type,
            $request->paginate == null
        );
        if (!$request->paginate) {
            return $this->responsePaginate($questions, QuestionResource::class);
        }
        if (!$request->assignable_id) {
            return $this->responseOk(data: QuestionResource::collection($questions));
        }
        $selected = [];
        if ($request->assignable_type == self::ASSIGNABLE_TYPE_LESSON) {
            $lesson = $this->lessonRepository->find($request->assignable_id, ['questions']);
            $selected = $lesson->questions->pluck('id')->toArray();
        }
        if ($request->assignable_type == self::ASSIGNABLE_TYPE_EXAM) {
            $exam = $this->examRepository->find($request->assignable_id, ['questions']);
            $selected = $exam->questions->pluck('id')->toArray();
        }
        return $this->responseOk(data: QuestionResource::collection($questions), extra: ['selected' => $selected]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionRequest $request)
    {
        $data = Arr::add($request->validated(), 'user_id', auth()->id());
        DB::transaction(function () use ($data) {
            $question = $this->repository->create(Arr::except($data, ['choices']));
            $data['choices'] = collect($data['choices'])->map(function ($choice) use ($question) {
                return array_merge(
                    $choice,
                    ['question_id' => $question->id, 'created_at' => now(), 'updated_at' => now()]
                );
            })->all();
            $this->questionChoiceRepository->createMany($data['choices']);
        });
        return $this->responseOk(Messages::CREATE_SUCCESS_MESSAGE);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->responseOk(
            data: new QuestionResource($this->repository->find($id, ['choices', 'correctChoices', 'author']))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuestionRequest $request, string $id)
    {
        $data = $request->validated();
        DB::transaction(function () use ($data, $id) {
            $question = $this->repository->find($id, ['choices']);
            $this->repository->update($id, Arr::only($data, ['content', 'level', 'solution', 'learning_module_id']));
            $question->choices->map(function ($choice, $index) use ($data) {
                $this->questionChoiceRepository->update($choice->id, $data['choices'][$index]);
            });
        });
        return $this->responseOk(Messages::UPDATE_SUCCESS_MESSAGE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $question = $this->repository->find($id);
            $question->choices()->delete();
            $question->questionMappings()->forceDelete();
            $this->repository->delete($id);
        });
        return $this->responseOk(Messages::DELETE_SUCCESS_MESSAGE);
    }

    public function quicklyUpdate(Request $request)
    {
        if ($request->mode == Question::QUICKLY_MODE_CHOICE) {
            $choice = $this->questionChoiceRepository->find($request->choice_id, ['question']);
            $choice->question->correctChoices()->update(['is_correct' => false]);
            $choice->update(['is_correct' => true]);
            return $this->responseOk($request->notification ? Messages::UPDATE_SUCCESS_MESSAGE : '');
        } else if ($request->mode == Question::QUICKLY_MODE_LEVEL) {
            $this->repository->update($request->question_id, ['level' => $request->level]);
            return $this->responseOk($request->notification ? Messages::UPDATE_SUCCESS_MESSAGE : '');
        }
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'pdf_id' => 'required|string',
            'learning_module_id' => 'required|integer|exists:learning_modules,id',
        ]);
        dispatch(new ImportQuestionsJob($request->learning_module_id, $request->pdf_id, auth()->id()));
        return $this->responseOk(Messages::CREATE_AND_IMPORT_QUESTION_MESSAGE);
    }
}
