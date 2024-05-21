<?php

namespace App\Helpers;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MathpixHelper
{
    private Http $http;
    private const MATHPIX_API_URL = 'https://api.mathpix.com/v3/pdf/';
    private const ADDING_QUESTION = 1;
    private const ADDING_CHOICE_FIRST = 2;
    private const ADDING_CHOICE_SECOND = 3;
    private const ADDING_CHOICE_THIRD = 4;
    private const ADDING_CHOICE_FOURTH = 5;

    public function getPdfLinesData(string $pdfId): array
    {
        $response = $this->get("{$pdfId}.lines.json");
        return $this->processData($response->json()['pages']);
    }

    public function processData(array $pages): array
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
            if (Str::startsWith($lines[$i]['text'], 'Câu')) {
                if ($adding === self::ADDING_CHOICE_FOURTH) { // Kết thúc câu hỏi
                    $this->addQuestion($questionInstance, $questions);
                }
                $questionInstance = new QuestionInformation();
                $adding = self::ADDING_QUESTION;
                $questionInstance->content = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'A.')) {
                $adding = self::ADDING_CHOICE_FIRST;
                $questionInstance->choices[0]['content'] = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'B.')) {
                $adding = self::ADDING_CHOICE_SECOND;
                $questionInstance->choices[1]['content'] = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'C.')) {
                $adding = self::ADDING_CHOICE_THIRD;
                $questionInstance->choices[2]['content'] = $lines[$i]['text'];
            } else if (Str::startsWith($lines[$i]['text'], 'D.')) {
                $adding = self::ADDING_CHOICE_FOURTH;
                $questionInstance->choices[3]['content'] = $lines[$i]['text'];
            } else {
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
                }
            }
        }
        $this->addQuestion($questionInstance, $questions);
        return $questions;
    }

    private function addQuestion(QuestionInformation $questionInstance, &$questions)
    {
        $questionInstance->content = $this->handleData($questionInstance->content);
        $questionInstance->choices[0]['content'] = $this->handleData($questionInstance->choices[0]['content']);
        $questionInstance->choices[1]['content'] = $this->handleData($questionInstance->choices[1]['content']);
        $questionInstance->choices[2]['content'] = $this->handleData($questionInstance->choices[2]['content']);
        $questionInstance->choices[3]['content'] = $this->handleData($questionInstance->choices[3]['content']);
        $questions[] = $questionInstance;
    }

    private function handleData($content)
    {
        // remove question | choice number
        $patterns = [
            '/Câu \d+: /',
            '/A\./',
            '/B\./',
            '/C\./',
            '/D\./',
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
        return self::MATHPIX_API_URL . trim($endpoint, '/');
    }

    private function get(string $endpoint, array|null|string $query = null): PromiseInterface|Response
    {
        $url = $this->getUrl($endpoint);
        return $this->getHttp()->get($url, $query);
    }

    private function post(string $endpoint, array $data = []): PromiseInterface|Response
    {
        $url = $this->getUrl($endpoint);
        return $this->getHttp()->post($url, $data);
    }

    private function getHttp(): PendingRequest
    {
        return Http::withHeaders([
            'app_id' => config('services.mathpix.app_id'),
            'app_key' => config('services.mathpix.app_key'),
            'Content-type' => 'application/json',
        ]);
    }
}


class QuestionInformation
{
    public $content;
    public $choices = [
        ['content' => '', 'is_correct' => false],
        ['content' => '', 'is_correct' => false],
        ['content' => '', 'is_correct' => false],
        ['content' => '', 'is_correct' => false]
    ];
}
