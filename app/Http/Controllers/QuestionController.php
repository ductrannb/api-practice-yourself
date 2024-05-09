<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Repositories\QuestionChoiceRepository;
use App\Repositories\QuestionRepository;
use App\Utils\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    private $questionChoiceRepository;

    public function __construct(QuestionRepository $questionRepository, QuestionChoiceRepository $questionChoiceRepository)
    {
        $this->repository = $questionRepository;
        $this->questionChoiceRepository = $questionChoiceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $questions = $this->repository->getList(
            $request->assignable_id,
            $request->keyword,
            $request->level,
            $request->assignable_type
        );
        return $this->responsePaginate($questions, QuestionResource::class);
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
            $this->repository->update($id, Arr::only($data, ['content', 'level', 'solution']));
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
            $this->repository->delete($id);
        });
        return $this->responseOk(Messages::DELETE_SUCCESS_MESSAGE);
    }
}
