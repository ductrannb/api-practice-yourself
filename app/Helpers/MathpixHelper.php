<?php

namespace App\Helpers;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MathpixHelper
{
    private const MATHPIX_API_URL = 'https://api.mathpix.com/v3/pdf/';
    private const ADDING_QUESTION = 1;
    private const ADDING_CHOICE_FIRST = 2;
    private const ADDING_CHOICE_SECOND = 3;
    private const ADDING_CHOICE_THIRD = 4;
    private const ADDING_CHOICE_FOURTH = 5;
    private const ADDING_LEVEL = 6;
    private const ADDING_CORRECT_CHOICE = 7;
    private const ADDING_SOLUTION = 8;

    public function getPdfLinesData(string $pdfId): array
    {
        $response = $this->get("{$pdfId}.lines.json");
        return $this->processData($response->json()['pages']);
    }

    public function isProcessingDone($pdfId): bool
    {
        $response = $this->get("{$pdfId}");
        return ($response->json()['percent_done'] ?? 0) == 100;
    }

    public function getNumPages($pdfId): int
    {
        $response = $this->get("{$pdfId}");
        return $response->json()['num_pages'] ?? 0;
    }

    private function processData(array $pages): array
    {
        // Gộp các dòng văn bản từ các trang thành một mảng
        $lines = [];
        Arr::map($pages, function ($page) use (&$lines) {
            $lines = array_merge($lines, $page['lines']);
        });

        $questions = [];
        $questionInstance = new QuestionInformation();
        $adding = null;
        for ($i = 0; $i < count($lines); $i++) {
            if (Str::startsWith($lines[$i]['text'], 'Câu')) { // Start new question
                if ($adding === self::ADDING_SOLUTION) { // Kết thúc câu hỏi
                    $this->addQuestion($questionInstance, $questions);
                }
                $questionInstance = new QuestionInformation();
                $adding = self::ADDING_QUESTION;
                $questionInstance->content = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'A.')) { // Start new choice A
                $adding = self::ADDING_CHOICE_FIRST;
                $questionInstance->choices[0]['content'] = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'B.')) { // Start new choice B
                $adding = self::ADDING_CHOICE_SECOND;
                $questionInstance->choices[1]['content'] = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'C.')) { // Start new choice C
                $adding = self::ADDING_CHOICE_THIRD;
                $questionInstance->choices[2]['content'] = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'D.')) { // Start new choice D
                $adding = self::ADDING_CHOICE_FOURTH;
                $questionInstance->choices[3]['content'] = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'Mức độ:') || Str::startsWith($lines[$i]['text'], 'Mức đô:')) { // Level
                $adding = self::ADDING_LEVEL;
                $level = trim(explode(':', $lines[$i]['text'])[1]) ?? 1;
                $questionInstance->level = $level;
            } else if (Str::startsWith($lines[$i]['text'], 'Đáp án:')) { // Correct choice
                $adding = self::ADDING_CORRECT_CHOICE;
                $correctChoice = trim(explode(':', $lines[$i]['text'])[1]) ?? 'A';
                $questionInstance->choices[0]['is_correct'] = $correctChoice === 'A';
                $questionInstance->choices[1]['is_correct'] = $correctChoice === 'B';
                $questionInstance->choices[2]['is_correct'] = $correctChoice === 'C';
                $questionInstance->choices[3]['is_correct'] = $correctChoice === 'D';
            } else if (Str::startsWith($lines[$i]['text'], 'Lời giải:')) { // Solution
                $adding = self::ADDING_SOLUTION;
                $questionInstance->solution = $lines[$i]['text'];
            }
            else {
                // Nếu không phải là đầu nội dung câu hỏi hoặc đáp án thì thêm vào nội dung câu hỏi hoặc đáp án
                switch ($adding) {
                    case self::ADDING_QUESTION:
                        $questionInstance->content .= ' ' . $lines[$i]['text'];
                        break;
                    case self::ADDING_CHOICE_FIRST:
                        $questionInstance->choices[0]['content'] .= $lines[$i]['text'];
                        break;
                    case self::ADDING_CHOICE_SECOND:
                        $questionInstance->choices[1]['content'] .= $lines[$i]['text'];
                        break;
                    case self::ADDING_CHOICE_THIRD:
                        $questionInstance->choices[2]['content'] .= $lines[$i]['text'];
                        break;
                    case self::ADDING_CHOICE_FOURTH:
                        $questionInstance->choices[3]['content'] .= $lines[$i]['text'];
                        break;
                    case self::ADDING_CORRECT_CHOICE:
                    case self::ADDING_LEVEL:
                        break;
                    case self::ADDING_SOLUTION:
                        $questionInstance->solution .= ' ' . $lines[$i]['text'];
                        break;
                }
            }
        }
        $this->addQuestion($questionInstance, $questions);
        return $questions;
    }

    private function addQuestion(QuestionInformation $questionInstance, &$questions)
    {
        $questionInstance->content = $this->processContent($questionInstance->content);
        $questionInstance->solution = $this->processContent($questionInstance->solution);
        $questionInstance->choices[0]['content'] = $this->processContent($questionInstance->choices[0]['content']);
        $questionInstance->choices[1]['content'] = $this->processContent($questionInstance->choices[1]['content']);
        $questionInstance->choices[2]['content'] = $this->processContent($questionInstance->choices[2]['content']);
        $questionInstance->choices[3]['content'] = $this->processContent($questionInstance->choices[3]['content']);
        $questions[] = $questionInstance;
    }

    private function processContent($content)
    {
        // remove question | choice number
        $patterns = [
            '/Câu \d+: /',
            '/Câu \d+. /',
            '/A\./',
            '/B\./',
            '/C\./',
            '/D\./',
            '/Lời giải:/'
        ];
        $content = preg_replace($patterns, '', $content);

        if (Str::contains($content, '$')) {
            $matches = [];
            preg_match_all('/\$(.*?)\$/', $content, $matches);
            if (count($matches) > 0) {
                for ($i = 0; $i < count($matches[0]); $i++) {
                    $res = Http::post(env('PRACTICE_SERVER_URL'), ['tex' => $matches[1][$i]]);
                    $content = str_replace($matches[0][$i], str_replace('display="block"', '', $res->json()['result']), $content);
                }
            }
        }
        $content = trim(trim($content), '.');
        return "<h1>&nbsp;</h1><p>{$content}</p>";
    }

    private function getUrl(string $endpoint): string
    {
        return trim(self::MATHPIX_API_URL . trim($endpoint, '/'), '/');
    }

    private function get(string $endpoint, array|null|string $query = null): PromiseInterface|Response
    {
        $url = $this->getUrl($endpoint);
        return $this->getHttp()->get($url, $query);
    }

    private function post(string $endpoint, array $data = [], $isFormData = false): PromiseInterface|Response
    {
        $url = $this->getUrl($endpoint);
        return $this->getHttp($isFormData)->post($url, $data);
    }

    private function getHttp($isFormData = false): PendingRequest
    {
        return Http::withHeaders([
            'app_id' => config('services.mathpix.app_id'),
            'app_key' => config('services.mathpix.app_key'),
            'Content-type' => $isFormData ? 'multipart/form-data' : 'application/json',
        ]);
    }
}


class QuestionInformation
{
    public $content;
    public $level;
    public $solution;
    public $choices = [
        ['content' => '', 'is_correct' => false],
        ['content' => '', 'is_correct' => false],
        ['content' => '', 'is_correct' => false],
        ['content' => '', 'is_correct' => false]
    ];
}
