<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Console\Command;

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
        User::factory()->count(9)->create(['role_id' => User::ROLE_TEACHER]);
        Course::factory()->create();
        Lesson::factory()->count(24)->create(['course_id' => 1]);
    }
}
