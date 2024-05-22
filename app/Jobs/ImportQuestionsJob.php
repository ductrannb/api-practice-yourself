<?php

namespace App\Jobs;

use App\Helpers\MathpixHelper;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\MathpixHistory;
use App\Models\Question;
use App\Models\User;
use App\Notifications\ImportQuestionsDone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportQuestionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $record;
    private $type;
    private $pdfId;
    private $authId;
    private $mathpixHelper;
    public $timeout = 0;

    const TYPE_EXAM = 1;
    const TYPE_LESSON = 2;
    /**
     * Create a new job instance.
     */
    public function __construct(Exam|Lesson $record, $pdfId, $type, $authId)
    {
        $this->record = $record;
        $this->pdfId = $pdfId;
        $this->type = $type;
        $this->authId = $authId;
        $this->mathpixHelper = new MathpixHelper();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pdfId = $this->pdfId;
        if (!$pdfId) {
            return;
        }
        $mathpixHistory = MathpixHistory::create([
            'pdf_id' => $pdfId,
            'status' => MathpixHistory::STATUS_PROCESSING,
            'import_status' => MathpixHistory::STATUS_PENDING
        ]);
        info('Upload pdf done: ' . $pdfId);
        while (!$this->mathpixHelper->isProcessingDone($pdfId)) {
            sleep(1);
        }
        $mathpixHistory->update([
            'status' => MathpixHistory::STATUS_DONE,
            'num_pages' => $this->mathpixHelper->getNumPages($pdfId) ?? '0',
        ]);
        info('Processing pdf done: ' . $pdfId);
        $questions = $this->mathpixHelper->getPdfLinesData($pdfId);
        info('Get pdf data done: ' . $pdfId);
        $mathpixHistory->update([
            'import_status' => MathpixHistory::STATUS_PROCESSING,
        ]);
        $this->createQuestions($questions);
        $mathpixHistory->update([
            'import_status' => MathpixHistory::STATUS_DONE,
        ]);
        $user = User::find($this->authId);
        $user->notify(new ImportQuestionsDone($this->record, $this->type));
        info('Create questions done: ' . $pdfId);
    }

    private function createQuestions($questions)
    {
        collect($questions)->each(function ($question) {
            DB::beginTransaction();
            try {
                $q = $this->record->questions()->create([
                    'content' => $question->content,
                    'user_id' => $this->authId,
                    'assignable_type' => $this->type == self::TYPE_EXAM ? Question::TYPE_EXAM : Question::TYPE_LESSON,
                    'assignable_id' => $this->record->id,
                ]);
                collect($question->choices)->each(function ($choice) use ($q) {
                    $q->choices()->create([
                        'content' => $choice['content'],
                        'is_correct' => $choice['is_correct']
                    ]);
                });
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                info('Create question failed: ' . $e->getMessage());
            }
        });
    }
}
