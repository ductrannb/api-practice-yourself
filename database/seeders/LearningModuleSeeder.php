<?php

namespace Database\Seeders;

use App\Models\LearningModule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearningModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LearningModule::truncate();
        $data = json_decode(file_get_contents(database_path('learning-modules.json')));
        foreach ($data as $class) {
            $classObj = LearningModule::create([
                'name' => $class->name,
                'type' => LearningModule::TYPE_CLASS,
                'parent_id' => null
            ]);
            foreach ($class->chapters as $chapter) {
                $chapterObj = LearningModule::create([
                    'name' => $chapter->name,
                    'type' => LearningModule::TYPE_CHAPTER,
                    'parent_id' => $classObj->id
                ]);
                foreach ($chapter->units as $unit) {
                    LearningModule::create([
                        'name' => $unit,
                        'type' => LearningModule::TYPE_UNIT,
                        'parent_id' => $chapterObj->id
                    ]);
                }
            }
        }
    }
}
