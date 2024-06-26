<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardAdminResource;
use App\Http\Resources\DashboardTeacherResource;
use App\Http\Resources\DashboardUserResource;
use App\Models\CourseUser;
use App\Models\Exam;
use App\Models\ExamUser;
use App\Models\Question;
use App\Models\QuestionChoiceSelected;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private const TYPE_DAY = 1;
    private const TYPE_WEEK = 2;
    private const TYPE_MONTH = 3;
    private const DAY_LIMIT = 30;
    private const WEEK_LIMIT = 12;
    private const MONTH_LIMIT = 12;

    public function dashboardAdmin(Request $request)
    {
        $type = $request->type ?: self::TYPE_WEEK;
        $userList = User::where('created_at', '>=', Carbon::today()->subYears()->startOfYear()->format('Y-m-d H:i:s'))->get();
        $questionList = Question::where('created_at', '>=', Carbon::today()->subYears()->startOfYear()->format('Y-m-d H:i:s'))->get();
        $examList = Exam::where('created_at', '>=', Carbon::today()->subYears()->startOfYear()->format('Y-m-d H:i:s'))->get();
        $courseUserList = CourseUser::where('type', CourseUser::TYPE_USER)
            ->when($type == self::TYPE_DAY, function ($query) {
                return $query->where('created_at', '>=', Carbon::today()->subDays(self::DAY_LIMIT)->startOfYear()->format('Y-m-d H:i:s'));
            })
            ->when($type == self::TYPE_WEEK, function ($query) {
                return $query->where('created_at', '>=', Carbon::today()->subWeeks(self::WEEK_LIMIT)->startOfYear()->format('Y-m-d H:i:s'));
            })
            ->when($type == self::TYPE_MONTH, function ($query) {
                return $query->where('created_at', '>=', Carbon::today()->subMonths(self::MONTH_LIMIT)->startOfYear()->format('Y-m-d H:i:s'));
            })
            ->get();

        $userChart = $this->getMiniChartData($userList, User::ROLE_USER);
        $teacherChart = $this->getMiniChartData($userList, User::ROLE_TEACHER);
        $questionChart = $this->getMiniChartData($questionList);
        $examChart = $this->getMiniChartData($examList);
        $mainChart = (object) [
            'labels' => $this->getLabels($type),
            'revenue_data' => array_reverse(array_map(
                fn($i) => rand(100, 1000),
                range(0, ($type == self::TYPE_DAY ? self::DAY_LIMIT : ($type == self::TYPE_WEEK ? self::WEEK_LIMIT : self::MONTH_LIMIT) - 1))
            )),
            'course_data' => $this->getMainChartData($courseUserList, $type),
        ];
        return $this->responseOk(data: new DashboardAdminResource((object) [
            'userChart' => $userChart,
            'teacherChart' => $teacherChart,
            'questionChart' => $questionChart,
            'examChart' => $examChart,
            'mainChart' => $mainChart
        ]));
    }

    public function dashboardTeacher(Request $request)
    {
        $type = $request->type ?: self::TYPE_WEEK;
        $questionList = Question::where('created_at', '>=', Carbon::today()->subYears()->startOfYear()->format('Y-m-d H:i:s'))
            ->where('user_id', auth()->id())
            ->get();
        $examList = Exam::where('created_at', '>=', Carbon::today()->subYears()->startOfYear()->format('Y-m-d H:i:s'))
            ->where('user_id', auth()->id())->get();
        $examUserList = ExamUser::where('created_at', '>=', Carbon::today()->subYears()->startOfYear()->format('Y-m-d H:i:s'))
            ->whereHas('exam', function ($query) {
                return $query->where('user_id', auth()->id());
            })->get();

        $mainChart = (object) [
            'labels' => $this->getLabels($type),
            'joiner_data' => $this->getMainChartData($examUserList->unique('user_id'), $type),
            'submit_time_data' => $this->getMainChartData($examUserList, $type),
        ];
        return $this->responseOk(data: new DashboardTeacherResource((object) [
            'questionChart' => $this->getMiniChartData($questionList),
            'examChart' => $this->getMiniChartData($examList),
            'mainChart' => $mainChart
        ]));
    }

    public function dashboardUser(Request $request)
    {
        $type = $request->type ?: self::TYPE_WEEK;
        $courseUserList = CourseUser::where('created_at', '>=', Carbon::today()->subYears()->startOfYear()->format('Y-m-d H:i:s'))
            ->where('type', CourseUser::TYPE_USER)
            ->where('user_id', auth()->id())
            ->get();
        $examUserList = ExamUser::where('created_at', '>=', Carbon::today()->subYears()->startOfYear()->format('Y-m-d H:i:s'))
            ->where('user_id', auth()->id())
            ->get();
        $questionList = QuestionChoiceSelected::where('user_id', auth()->id())
            ->when($type == self::TYPE_DAY, function ($query) {
                return $query->where('created_at', '>=', Carbon::today()->subDays(self::DAY_LIMIT)->startOfYear()->format('Y-m-d H:i:s'));
            })
            ->when($type == self::TYPE_WEEK, function ($query) {
                return $query->where('created_at', '>=', Carbon::today()->subWeeks(self::WEEK_LIMIT)->startOfYear()->format('Y-m-d H:i:s'));
            })
            ->when($type == self::TYPE_MONTH, function ($query) {
                return $query->where('created_at', '>=', Carbon::today()->subMonths(self::MONTH_LIMIT)->startOfYear()->format('Y-m-d H:i:s'));
            })
            ->get();

        $mainChart = (object) [
            'labels' => $this->getLabels($type),
            'question_data' => $this->getMainChartData($questionList, $type),
            'correct_rate_data' => $this->getMainChartCorrectRateData($questionList, $type)
        ];
        return $this->responseOk(data: new DashboardUserResource((object) [
            'courseChart' => $this->getMiniChartData($courseUserList),
            'examChart' => $this->getMiniChartData($examUserList),
            'mainChart' => $mainChart
        ]));
    }

    public function getLabels($type = self::TYPE_WEEK)
    {
        $labels = [];
        switch ($type) {
            case self::TYPE_DAY:
                $labels = array_map(fn($i) => Carbon::today()->subDays($i)->format('d/m'), range(0, self::DAY_LIMIT - 1));
                break;
            case self::TYPE_WEEK:
                $labels = array_map(fn($i) =>
                    Carbon::today()->subWeeks($i)->startOfWeek()->format('d/m')
                    . ' - ' .
                    Carbon::today()->subWeeks($i)->endOfWeek()->format('d/m')
                    , range(0, self::WEEK_LIMIT - 1));
                break;
            case self::TYPE_MONTH:
                $labels = array_map(fn($i) => Carbon::today()->subMonths($i)->startOfMonth()->format('m/Y'), range(0, self::MONTH_LIMIT - 1));
                break;
        }
        return array_reverse($labels);
    }

    private function percentage($thisValue, $lastValue)
    {
        if ($lastValue == 0) {
            return $thisValue;
        }
        return round(($thisValue - $lastValue) / $lastValue * 100, 1) . '%';
    }

    public function getMainChartData($list, $type = self::TYPE_WEEK)
    {
        $data = [];
        if ($type == self::TYPE_DAY) {
            $data = array_map(
                fn($i) => $list
                    ->where('created_at', '>=', Carbon::today()->subDays($i)->startOfDay()->format('Y-m-d H:i:s H:i:s'))
                    ->where('created_at', '<=', Carbon::today()->subDays($i)->endOfDay()->format('Y-m-d H:i:s H:i:s'))
                    ->count(),
                range(0, self::DAY_LIMIT - 1)
            );
        }
        if ($type == self::TYPE_WEEK) {
            $data = array_map(
                fn($i) => $list
                    ->where('created_at', '>=', Carbon::today()->subWeeks($i)->startOfWeek()->format('Y-m-d H:i:s'))
                    ->where('created_at', '<=', Carbon::today()->subWeeks($i)->endOfWeek()->format('Y-m-d H:i:s'))
                    ->count(),
                range(0, self::WEEK_LIMIT - 1)
            );
        }
        if ($type == self::TYPE_MONTH) {
            $data = array_map(
                fn($i) => $list
                    ->where('created_at', '>=', Carbon::today()->subMonths($i)->startOfMonth()->format('Y-m-d H:i:s'))
                    ->where('created_at', '<=', Carbon::today()->subMonths($i)->endOfMonth()->format('Y-m-d H:i:s'))
                    ->count(),
                range(0, self::MONTH_LIMIT - 1)
            );
        }
        return array_reverse($data);
    }

    private function getMainChartCorrectRateData($list, $type = self::TYPE_WEEK): array
    {
        $data = [];
        if ($type == self::TYPE_DAY) {
            $data = array_map(
                fn($i) => $this->getCorrectRate(
                    $list->where('created_at', '>=', Carbon::today()->subDays($i)->startOfDay()->format('Y-m-d H:i:s H:i:s'))
                        ->where('created_at', '<=', Carbon::today()->subDays($i)->endOfDay()->format('Y-m-d H:i:s H:i:s'))
                ),
                range(0, self::DAY_LIMIT - 1)
            );
        }
        if ($type == self::TYPE_WEEK) {
            $data = array_map(
                fn($i) => $this->getCorrectRate(
                    $list->where('created_at', '>=', Carbon::today()->subWeeks($i)->startOfWeek()->format('Y-m-d H:i:s'))
                        ->where('created_at', '<=', Carbon::today()->subWeeks($i)->endOfWeek()->format('Y-m-d H:i:s'))
                ),
                range(0, self::WEEK_LIMIT - 1)
            );
        }
        if ($type == self::TYPE_MONTH) {
            $data = array_map(
                fn($i) => $this->getCorrectRate(
                    $list->where('created_at', '>=', Carbon::today()->subMonths($i)->startOfMonth()->format('Y-m-d H:i:s'))
                        ->where('created_at', '<=', Carbon::today()->subMonths($i)->endOfMonth()->format('Y-m-d H:i:s'))
                ),
                range(0, self::MONTH_LIMIT - 1)
            );
        }
        return array_reverse($data);
    }

    private function getCorrectRate($list)
    {
        $total = $list->count();
        $correct = $list->where('is_correct', true)->count();
        return $total == 0 ? 0 : round($correct / $total * 100, 2);
    }

    private function getMiniChartData($list, $role = null)
    {
        $countThisWeek = $list
            ->when($role, fn($collection) => $collection->where('role_id', '=', $role))
            ->where('created_at', '>=', Carbon::today()->subWeeks()->format('Y-m-d H:i:s'))
            ->count();
        $countLastWeek = $list
            ->when($role, fn($collection) => $collection->where('role_id', '=', $role))
            ->where('created_at', '>=', Carbon::today()->subWeeks()->startOfWeek()->format('Y-m-d H:i:s'))
            ->where('created_at', '<=', Carbon::today()->subWeeks()->endOfWeek()->format('Y-m-d H:i:s'))
            ->count();
        return (object) [
            'value' => $countThisWeek,
            'grow_percent' => $this->percentage($countThisWeek, $countLastWeek),
            'chart_labels' => $this->getLabels(),
            'chart_data' => array_reverse(array_map(
                function ($i) use ($list, $role) {
                    return $list
                        ->when($role, fn($collection) => $collection->where('role_id', '=', $role))
                        ->where('created_at', '>=', Carbon::today()->subWeeks($i)->startOfWeek()->format('Y-m-d H:i:s'))
                        ->where('created_at', '<=', Carbon::today()->subWeeks($i)->endOfWeek()->format('Y-m-d H:i:s'))
                        ->count();
                },
                range(0, self::WEEK_LIMIT - 1)
            ))
        ];
    }
}
