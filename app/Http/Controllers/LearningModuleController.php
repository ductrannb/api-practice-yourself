<?php

namespace App\Http\Controllers;

use App\Http\Resources\LearningModuleResource;
use App\Models\LearningModule;
use Illuminate\Http\Request;

class LearningModuleController extends Controller
{
    public function index(Request $request)
    {
        return $this->responseOk(data: [
            'classes' => LearningModuleResource::collection(LearningModule::where('type', LearningModule::TYPE_CLASS)->get()),
            'chapters' => LearningModuleResource::collection(LearningModule::where('type', LearningModule::TYPE_CHAPTER)->get()),
            'units' => LearningModuleResource::collection(LearningModule::where('type', LearningModule::TYPE_UNIT)->get()),
        ]);
    }
}
