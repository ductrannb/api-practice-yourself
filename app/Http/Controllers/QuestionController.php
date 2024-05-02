<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Repositories\QuestionRepository;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(QuestionRepository $questionRepository)
    {
        $this->repository = $questionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
//        $this->repository->where([''])
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionRequest $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
