<?php

namespace Database\Seeders;

use App\Models\BannerPromotion;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamConfig;
use App\Models\ExamPage;
use App\Models\ExamPoin;
use App\Models\Lesson;
use App\Models\LessonPage;
use App\Models\Tutor;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserCourseCategory;
use App\Models\UserLesson;
use Database\Factories\ExamPageFactory;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;

use Faker\Generator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use PDO;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;

class DatabaseSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    private function log($message)
    {
        echo sprintf("%s %s", date("Y-m-d H:i:s"), $message);
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    private function PopulateImage()
    {
        $configs = [
            [
                "width" => 1920,
                "height" => 1080,
                "count" => 50,
            ],
            [
                "width" => 1080,
                "height" => 1080,
                "count" => 50,
            ]
        ];
        foreach ($configs as $key => $v) {
            for ($i = 1; $i <= $v["count"]; $i++) {
                $files_in_storage = Storage::disk("s3")->allFiles(sprintf("random-image/%dx%d", $v["width"], $v["height"]));
                $number_files_in_storage  = count($files_in_storage);
                if ($number_files_in_storage >= $v["count"]) {
                    $this->log(sprintf("Skipping populate image %dx%d already have %d image in storage\n", $v["width"], $v["height"], $number_files_in_storage));
                    break;
                }
                $this->GetRandomImageAndSave($v["width"], $v["height"]);
                $this->log(sprintf("Image %d/%d %dx%d finished\n", $i, $v["count"], $v["width"], $v["height"]));
            }
        };
    }

    private function GetRandomImageAndSave(int $width, int $height)
    {
        $this->faker->addProvider(new FakerPicsumImagesProvider($this->faker));
        $folder_path = "tmp";
        $image_path = $this->faker->image($dir = $folder_path, $width = $width, $height = $height);
        $filename = str_replace($folder_path, "", $image_path);
        $s3_file_path = sprintf('random-image/%dx%d/%s', $width, $height, $filename);
        Storage::disk('s3')->put($s3_file_path, file_get_contents($image_path));
    }

    private function simulateUserDataAnswer($exam_page): string
    {
        if ($exam_page->content_type === "multiple_choice") {
            $answerData = json_decode($exam_page->data, true);
            $choices = $answerData["choices"];
            $userAnswerCorrect = $this->faker->boolean(70);
            $correct_choice_index = [];
            $wrong_choice_index = [];
            for ($i = 0; $i < count($choices); $i++) {
                if (filter_var($choices[$i]['checked'], FILTER_VALIDATE_BOOLEAN)) {
                    array_push($correct_choice_index, $i);
                } else {
                    array_push($wrong_choice_index, $i);
                }
            }

            $userChoiceIndex = 0;
            if ($userAnswerCorrect) {
                $userChoiceIndex = $correct_choice_index[random_int(0, count($correct_choice_index) - 1)];
            } else {
                $userChoiceIndex = $wrong_choice_index[random_int(0, count($wrong_choice_index) - 1)];
            }
            for ($i = 0; $i < count($choices); $i++) {
                if ($userChoiceIndex === $i) {
                    $choices[$i]['selected'] = true;
                } else {
                    $choices[$i]['selected'] = false;
                }
            }
            $dataAnswerString = json_encode($choices, true);
            return $dataAnswerString;
        } else if ($exam_page->content_type === "answer") {
            $userAnswerCorrect = $this->faker->boolean(70);
            $correctAnswerDataDirty =  explode(",",  json_decode($exam_page->data, true)['keyword']);
            $correctAnswerData =  array_map(function ($item) {
                return trim($item);
            }, $correctAnswerDataDirty);
            if ($userAnswerCorrect) {
                $dataAnswer = [
                    'user_answer' => $correctAnswerData[random_int(0, count($correctAnswerData) - 1)],
                    'correct_answer' => $correctAnswerData
                ];
                $dataAnswerString = json_encode($dataAnswer);

                return $dataAnswerString;
            } else {
                $dataAnswer = [
                    'user_answer' => $this->faker->word(),
                    'correct_answer' => $correctAnswerData
                ];
                $dataAnswerString = json_encode($dataAnswer);
                return $dataAnswerString;
            }
        } else if ($exam_page->content_type === "fill_blank") {
            $userAnswerCorrect = $this->faker->boolean(70);
            $keyAndValue = json_decode($exam_page->data, true)['answer'];
            $keys = array_map(function ($item) {
                return $item['key'];
            }, $keyAndValue);
            $values = array_map(function ($item) {
                return $item['value'];
            }, $keyAndValue);

            $dataAnswer = [];

            if ($userAnswerCorrect) {
                foreach ($keyAndValue as $knv) {
                    array_push($dataAnswer, [
                        'correct_answer_key' => $knv['key'],
                        'correct_answer' => $knv['value'],
                        'user_answer_key' => $knv['key'],
                        'user_answer' => $knv['value']
                    ]);
                }

                $dataAnswerString = json_encode($dataAnswer);
                return $dataAnswerString;
            } else {
                $number_user_answer_correct = random_int(0, count($keyAndValue) - 1);
                for ($i = 0; $i < count($keyAndValue); $i++) {
                    $knv = $keyAndValue[$i];
                    if ($i < $number_user_answer_correct) {
                        array_push($dataAnswer, [
                            'correct_answer_key' => $knv['key'],
                            'correct_answer' => $knv['value'],
                            'user_answer_key' => $knv['key'],
                            'user_answer' => $knv['value']
                        ]);
                    } else {
                        array_push($dataAnswer, [
                            'correct_answer_key' => $knv['key'],
                            'correct_answer' => $knv['value'],
                            'user_answer_key' => 'wrong_answer',
                            'user_answer' => 'wrong_answer'
                        ]);
                    }
                }
                $dataAnswerString = json_encode($dataAnswer);
                return $dataAnswerString;
            }
        } else {
            return null;
        }
    }

    private function simulateUserDoExam($exam, $number_do_exam, $randomUserIds)
    {
        for ($i = 0; $i < $number_do_exam; $i++) {
            $exam_pages = $exam->examPage()->get();
            $number_of_exam_pages = $exam_pages->count();

            $max_attempt = $exam->max_attempt;
            if ($max_attempt) {
                $number_attempt_by_user = random_int(1, $max_attempt);
            } else {
                $number_attempt_by_user = random_int(1, 5);
            }
            for ($j = 0; $j < $number_attempt_by_user; $j++) {
                $start_time = now()->addHour(random_int(1, 48))->addMinutes(random_int(0, 60));
                $time_finish = $start_time->addMinutes(30);
                $finished_at = $start_time->addMinutes(random_int(10, 30));
                $exam_poin = ExamPoin::factory([
                    'exam_id' => $exam->id,
                    'user_id' => $randomUserIds[$i],
                    'poin' => random_int(0, $number_of_exam_pages),
                    'time_finish' => $time_finish,
                    'finished_at' => $finished_at,
                ])->create();
                foreach ($exam_pages as $exam_page) {
                    ExamAnswer::factory([
                        'user_id' => $randomUserIds[$i],
                        'exam_poin_id' => $exam_poin->id,
                        'exam_page_id' => $exam_page->id,
                        'data_answer' => $this->simulateUserDataAnswer($exam_page),
                    ])->create();
                }
            }
        }
    }

    private function simulateUserActivity($course_categories, $user)
    {
        $this->log("Starting Simulate User Activity\n");
        foreach ($course_categories as $course_category) {
            $this->log("Simulating course category " . $course_category->name . " activity\n");
            $random_course_category_students_count = random_int(50, 100);
            $getIdFunc = function ($item) {
                return $item->id;
            };
            $this->log("Generating random user id\n");
            $randomUserIds = array_map($getIdFunc, $this->faker->randomElements($user, $random_course_category_students_count, false));
            for ($i = 0; $i < $random_course_category_students_count; $i++) {
                $randomUserId = $randomUserIds[$i];

                UserCourseCategory::factory([
                    'course_category_id' => $course_category->id,
                    'user_id' => $randomUserId,
                ])->create();
            }

            $courses = $course_category->course()->get();
            $number_user_join_course = $random_course_category_students_count - random_int(0, 5);
            foreach ($courses as $idx => $course) {
                $this->log("Simulating course " . $course->name . " activity\n");
                // Simullate course is not opened
                if ($idx > random_int(2, $courses->count() - 1)) {
                    break;
                }

                for ($i = 0; $i < $number_user_join_course; $i++) {
                    UserCourse::factory([
                        'courses_id' => $course->id,
                        'users_id' => $randomUserIds[$i],
                    ])->create();
                }

                $lessons = $course->lesson()->get();
                $number_user_join_lessons = $number_user_join_course;
                foreach ($lessons as $idx => $lesson) {
                    if ($lesson->is_quiz) {
                        $this->log("Simulating quiz " . $lesson->name . " activity\n");
                        for ($i = 0; $i < $number_user_join_lessons; $i++) {
                            UserLesson::factory([
                                'courses_id' => $course->id,
                                'lessons_id' => $lesson->id,
                                'users_id' => $randomUserIds[$i],
                            ])->create();
                        }
                        $exam = $lesson->exam()->first();
                        $this->log("Simulating user doing quiz " . $lesson->name .  "\n");
                        $this->simulateUserDoExam($exam, $number_user_join_lessons, $randomUserIds);
                    } else {
                        $this->log("Simulating lesson " . $lesson->name . " activity\n");
                        for ($i = 0; $i < $number_user_join_lessons; $i++) {
                            UserLesson::factory([
                                'courses_id' => $course->id,
                                'lessons_id' => $lesson->id,
                                'users_id' => $randomUserIds[$i],
                            ])->create();
                        }
                    }
                    $this->log("Finishing Simulating lesson " . $lesson->name . " activity\n");
                    $number_user_join_lessons -=  random_int(0, 2);
                }

                $exams = $course->exam()->get();
                if ($exams->count() > 0) {
                    foreach ($exams as $exam) {
                        $this->log("Simulating exam " . $exam->name . " activity\n");
                        $this->simulateUserDoExam($exam, $number_user_join_course, $randomUserIds);
                        $this->log("Finishing Simulating exam " . $exam->name . " activity\n");
                    }
                }

                $number_user_join_course -= random_int(5, 10);
                $this->log("Finishing Simulating course " . $course->name . " activity\n");
            }
        }
    }
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->PopulateImage();
        $prakerja_pretest_course = Course::factory([
            'name' => 'Pre-Test',
            'order' => -100,
        ])->prakerja()->state(
            fn () => ['link_zoom' => null]
        )->has(
            Lesson::factory([
                'name' => "Penjelasan",
                'sorter' => 1,
            ])->has(
                LessonPage::factory([
                    'name' => "Penjelasan"
                ])->imageMedia()
            )->notUsingZoom()
        )->has(
            Exam::factory([
                'name' => "Pre-Test",
            ])->has(
                ExamPage::factory()->answer()->count(rand(2, 5))
            )->has(
                ExamPage::factory()->multipleChoice()->count(rand(2, 5))
            )->has(
                ExamPage::factory()->fillBlank()->count(rand(2, 5))
            )
        )->has(
            Tutor::factory([
                'name' => "Startup Campus Team",
            ])->count(1)
        );

        $prakerja_posttest_course = Course::factory([
            'name' => 'Post-Test',
            'order' => 100,
        ])->prakerja()->state(
            fn () => ['link_zoom' => null]
        )->has(
            Lesson::factory([
                'name' => "Penjelasan",
                'sorter' => 1,
            ])->has(
                LessonPage::factory([
                    'name' => "Penjelasan"
                ])->imageMedia()
            )->notUsingZoom()
        )->has(
            Exam::factory([
                'name' => "Post-Test",
            ])->has(
                ExamPage::factory()->answer()->count(rand(2, 5))
            )->has(
                ExamPage::factory()->multipleChoice()->count(rand(2, 5))
            )->has(
                ExamPage::factory()->fillBlank()->count(rand(2, 5))
            )
        )->has(
            Tutor::factory([
                'name' => "Startup Campus Team",
            ])->count(1)
        );

        $prakerja_random_lesson_zoom = Lesson::factory()->usingZoom()->count(1)
            ->state(function (array $attributes, Course $course) {
                $sort = Lesson::where('courses_id', $course->id)->latest('created_at')->first();
                if ($sort) {
                    $sorter = intval($sort->sorter) + 1;
                } else {
                    $sorter = 1;
                }
                return ['sorter' => $sorter];
            });


        $prakerja_random_lesson_quiz = Lesson::factory()->quiz()->count(1)
            ->state(function (array $attributes, Course $course) {
                $sort = Lesson::where('courses_id', $course->id)->latest('created_at')->first();
                if ($sort) {
                    $sorter = intval($sort->sorter) + 1;
                } else {
                    $sorter = 1;
                }
                return ['sorter' => $sorter];
            })->has(
                Exam::factory()->quiz()->has(
                    ExamPage::factory()->answer()->count(rand(2, 5))
                )->has(
                    ExamPage::factory()->multipleChoice()->count(rand(2, 5))
                )->has(
                    ExamPage::factory()->fillBlank()->count(rand(2, 5))
                )
            );

        $prakerja_random_lesson_content = Lesson::factory()->notUsingZoom()
            ->has(
                LessonPage::factory()->imageMedia()->count(rand(1, 3))
            )
            ->has(
                LessonPage::factory()->videoMedia()->count(rand(1, 3))
            )
            ->has(
                LessonPage::factory()->count(rand(1, 3))
            )
            ->count(rand(1, 2))
            ->state(function (array $attributes, Course $course) {
                $sort = Lesson::where('courses_id', $course->id)->latest('created_at')->first();
                if ($sort) {
                    $sorter = intval($sort->sorter) + 1;
                } else {
                    $sorter = 1;
                }
                return ['sorter' => $sorter];
            });

        $this->log("Creating Prakerja Course Category\n");
        $prakerja_course_category = CourseCategory::factory()->prakerja()->count(3)->hasTutor(rand(1, 4))->has(
            $prakerja_pretest_course
        )->has(
            Course::factory()->prakerja()->hasTutor(rand(1, 3))->has(
                $prakerja_random_lesson_zoom
            )->has(
                $prakerja_random_lesson_content
            )->has(
                $prakerja_random_lesson_quiz
            )->count(3)
        )->has($prakerja_posttest_course)->create();
        $this->log("Finish Creating Prakerja Course Category\n");

        $this->log("Creating Non Prakerja Course Category\n");
        $nonprakerja_course_category = CourseCategory::factory()->nonPrakerja()->count(5)->hasTutor(rand(1, 4))->has(
            Course::factory()->nonPrakerja()->hasTutor(rand(1, 3))->has(
                $prakerja_random_lesson_content->count(rand(2, 4))
            )->count(random_int(3, 5))
        )->create();
        $this->log("Finish Creating Non Prakerja Course Category\n");
        BannerPromotion::factory()->count(rand(3, 5))->create();
        ExamConfig::factory()->create();
        Artisan::call('create:superadmin');

        $this->log("Creating User\n");
        $user = User::factory()->count(500)->create();
        $this->log("Finish Creating User\n");
        $this->simulateUserActivity($prakerja_course_category, $user);
        $this->simulateUserActivity($nonprakerja_course_category, $user);
    }
}
