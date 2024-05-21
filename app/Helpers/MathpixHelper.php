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

    public function getPdfLinesData(string $pdfId)
    {
        $response = $this->get("{$pdfId}.lines.json");
        $this->processData($response->json()['pages']);
        return $response->json();
    }

    public function processData(array $pages)
    {
        $lines = [];
        Arr::map($pages, function ($page) use (&$lines) {
            $lines = array_merge($lines, $page['lines']);
        });

        $name = $this->getName($lines);
        $questions = [];
        $questionInstance = new QuestionInformation();
        $adding = null;
        for ($i = 0; $i < count($lines); $i++) {
            if (Str::startsWith($lines[$i]['text'], 'Câu')) {
                if ($adding === self::ADDING_CHOICE_FOURTH) {
                    $questions[] = $questionInstance;
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
        dd($questions);
    }

    private function getName(array $lines = [])
    {
        $defaultName = 'Đề thi thử import-' . now()->format('Y-m-d H:i:s');
        if (!count($lines) > 0) {
            return $defaultName;
        }
        for ($i = 0; $i < count($lines); $i++) {
            if ($lines[$i]['column'] === 2) {
                return $lines[$i]['text'] ?? $defaultName;
            }
        }
        return $defaultName;
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
