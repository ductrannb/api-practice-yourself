<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionChoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonString = file_get_contents(database_path('questions.json'));
        collect(json_decode($jsonString))->map(function ($question) {
            $record = Question::updateOrCreate([
                'lesson_id' => $question->lesson_id,
                'content' => $question->content,
            ], [
                'user_id' => $question->user_id,
                'level' => $question->level,
            ]);
            collect($question->choices)->map(function ($choice) use ($record) {
                QuestionChoice::updateOrCreate([
                    'question_id' => $record->id,
                    'content' => $choice->content,
                ], [
                    'is_correct' => $choice->is_correct,
                ]);
            });
        });
    }
}
