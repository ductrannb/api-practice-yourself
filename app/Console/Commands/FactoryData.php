<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FactoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:factory-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::transaction(function () {
            User::factory()->count(9)->create(['role_id' => User::ROLE_TEACHER]);
            Course::factory()->count(30)->create();
            Lesson::factory()->count(24)->create(['course_id' => 1]);
            Exam::factory()->count(20)->create();
        });
    }
}
