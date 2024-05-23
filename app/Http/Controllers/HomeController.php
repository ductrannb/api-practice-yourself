<?php

namespace App\Http\Controllers;

use App\Http\Requests\HomeExamRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\HomeCourseDetailResource;
use App\Http\Resources\HomeExamDetailResource;
use App\Http\Resources\HomeExamOverviewResource;
use App\Http\Resources\HomeExamResource;
use App\Http\Resources\HomeExamReviewResource;
use App\Http\Resources\HomeLessonDetailResource;
use App\Http\Resources\UserExamListResource;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Exam;
use App\Models\ExamUser;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionChoiceSelected;
use App\Models\User;
use App\Repositories\CourseRepository;
use App\Utils\Messages;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function index()
    {
        $newCourses = Course::latest()->orderByDesc('id', 'desc')->take(8)->get();
        $newExams = Exam::latest()->orderByDesc('id', 'desc')->take(4)->get();
        $overview = [
            'count_user' => User::where('role_id', User::ROLE_USER)->count(),
            'count_question' => Question::count(),
            'count_exam' => Exam::count(),
            'count_course' => Course::count()
        ];
        return $this->responseOk(data: [
            'courses' => CourseResource::collection($newCourses),
            'exams' => ExamResource::collection($newExams),
            'overview' => $overview
        ]);
    }

    public function courseDetail($id)
    {
        $course = $this->courseRepository->find($id, [
            'lessons.course', 'lessons.questions.choices', 'lessons.questions.correctChoices', 'lessons.questions.author', 'lessons.questionsSelected',
            'users',
            'questions'
        ]);
        $course->lessons->map(function ($lesson) {
            $lesson->completion = $lesson->questionsSelected->count() ?? 0;
            return $lesson;
        });
        return $this->responseOk(data: new HomeCourseDetailResource($course));
    }

    public function subscribeCourse($id)
    {
        $course = $this->courseRepository->find($id);
        if ($course->is_bought) {
            return $this->responseError(Messages::REGISTER_COURSE_EXISTED, errorCode: Response::HTTP_NOT_ACCEPTABLE);
        }
        if (auth()->user()->balance < $course->price) {
            return $this->responseError(Messages::REGISTER_COURSE_NOT_ENOUGH_MONEY, errorCode: Response::HTTP_NOT_ACCEPTABLE);
        }
        DB::transaction(function () use ($course) {
            auth()->user()->decrement('balance', $course->price);
            CourseUser::create(['user_id' => auth()->id(), 'course_id' => $course->id, 'type' => CourseUser::TYPE_USER]);
        });
        return $this->responseOk(Messages::REGISTER_COURSE_SUCCESS);
    }

    public function lessonDetail($id)
    {
        $lesson = Lesson::with([
            'questions', 'questions.correctChoices', 'questions.choices',
            'questions.author', 'course', 'questionsSelected', 'questionsSelected.question.correctChoices',
            'course.courseUserAuth'
        ])->find($id);
        $lesson->questions->map(function ($question) use ($lesson) {
            $question->is_selected = $lesson->questionsSelected->contains('question_id', $question->id);
            return $question;
        });
        $lesson->questionsSelected->map(function ($questionSelected) {
            $questionSelected->is_correct = $questionSelected->question_choice_id == $questionSelected->question->correctChoices->first()->id ?? false;
            return $questionSelected;
        });
        if (!$lesson->course || !$lesson->course->is_bought) {
            return $this->responseError(Messages::REGISTER_COURSE_NOT_EXIST, errorCode: Response::HTTP_NOT_ACCEPTABLE);
        }
        return new HomeLessonDetailResource($lesson);
    }

    public function examList(Request $request)
    {
        $exams = Exam::with(['questions'])->latest()->orderByDesc('id')->paginate(10);
        return $this->responsePaginate($exams, HomeExamResource::class);
    }

    public function examOverview($id)
    {
        $exam = Exam::with(['questions', 'histories.exam'])->findOrFail($id);
        return $this->responseOk(data: new HomeExamOverviewResource($exam));
    }

    public function examReview($id)
    {
        $examUser = ExamUser::with(['exam.questions.correctChoices', 'selected.question.correctChoices'])->findOrFail($id);
        $examUser->selected->map(function ($selected) {
            $selected->is_correct = $selected->question_choice_id == $selected->question->correctChoices->first()->id ?? false;
            return $selected;
        });
        return $this->responseOk(data: new HomeExamReviewResource($examUser));
    }

    public function examDetail($id)
    {
        $exam = Exam::with(['questions', 'questions.choices'])->find($id);
        return $this->responseOk(data: new HomeExamDetailResource($exam));
    }

    public function examSubmit(HomeExamRequest $request, $id)
    {
        $exam = Exam::with(['questions'])->findOrFail($id);
        if (!$exam->questions->count()) {
            return $this->responseError(Messages::EXAM_IS_EMPTY, errorCode: Response::HTTP_NOT_ACCEPTABLE);
        }
        DB::transaction(function () use ($request, $exam) {
            $examUser = ExamUser::create(['exam_id' => $exam->id, 'user_id' => auth()->id(), 'total_question' => $exam->questions->count()]);
            $examUser->load(['exam.questions.correctChoices']);
            collect($request->selected)->each(function ($selected) use ($examUser) {
                $examUser->selected()->create(array_merge($selected, [
                    'user_id' => auth()->id(),
                    'is_correct' => $examUser->exam->questions->find($selected['question_id'])->correctChoices->contains('id', $selected['question_choice_id']),
                    'assignable_type' => QuestionChoiceSelected::TYPE_EXAM
                ]));
            });
            $countCorrect = 0;
            $examUser->selected->map(function ($selected) use ($examUser, &$countCorrect) {
                $question = $examUser->exam->questions->find($selected->question_id);
                if ($question->correctChoices->contains('id', $selected->question_choice_id)) {
                    $countCorrect++;
                }
            });
            $score = (10 / $examUser->exam->questions->count()) * $countCorrect ?? 0;
            $examUser->update(['score' =>  $score, 'count_correct_question' => $countCorrect]);
        });
    }

    public function userListCourses(Request $request)
    {
        $courses = Course::whereIn('id', CourseUser::where('user_id', auth()->id())
                ->where('type', CourseUser::TYPE_USER)
                ->pluck('course_id')->toArray()
            )
            ->when($request->keyword, function ($query, $keyword) {
                return $query->where('name', 'like', "%$keyword%");
            })
            ->with(['lessons', 'questions', 'teachers'])
            ->paginate(10);
        return $this->responsePaginate($courses, CourseResource::class);
    }

    public function userListExams(Request $request)
    {
        $exams = Exam::with(['questions', 'histories'])
            ->whereIn('id', ExamUser::select('exam_id')->where('user_id', auth()->id())->distinct()->get()->toArray())
            ->paginate(10);
        return $this->responsePaginate($exams, UserExamListResource::class);
    }
}
