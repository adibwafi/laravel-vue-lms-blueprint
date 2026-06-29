<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [App\Http\Controllers\API\Auth\RegisterController::class, 'store'])->name('auth.register');

        Route::post('verification-otp', [App\Http\Controllers\API\Auth\OTPController::class, 'verificationOTP'])->name('auth.verification-otp');

        Route::post('login', [App\Http\Controllers\API\Auth\LoginController::class, 'store'])->name('auth.login');

        Route::post('resend-otp', [App\Http\Controllers\API\Auth\OTPController::class, 'resend'])->name('auth.resend-otp');

        Route::post('reset-password', [App\Http\Controllers\API\Auth\ResetPasswordController::class, 'store'])->name('auth.reset-password');
    });
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('admin')->group(function () {
            Route::middleware(['user.verified', 'role:Super-Admin'])->group(function () {
                Route::prefix('tutor')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\TutorController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\TutorController::class, 'store']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\TutorController::class, 'show']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\TutorController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\TutorController::class, 'destroy']);
                });
                Route::prefix('course-category')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\CourseCategoryController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\CourseCategoryController::class, 'store']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\CourseCategoryController::class, 'show']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\CourseCategoryController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\CourseCategoryController::class, 'destroy']);
                });
                Route::prefix('course')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\CourseController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\CourseController::class, 'store']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\CourseController::class, 'show']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\CourseController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\CourseController::class, 'destroy']);
                });
                Route::prefix('banner-promotion')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\BannerPromotionController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\BannerPromotionController::class, 'store']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\BannerPromotionController::class, 'show']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\BannerPromotionController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\BannerPromotionController::class, 'destroy']);
                    Route::post('/moveup', [App\Http\Controllers\API\BaseController::class, 'moveUp']);
                    Route::post('/movedown', [App\Http\Controllers\API\BaseController::class, 'moveDown']);
                });
                Route::prefix('lesson')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\LessonController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\LessonController::class, 'store']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\LessonController::class, 'show']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\LessonController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\LessonController::class, 'destroy']);
                    Route::post('/moveup', [App\Http\Controllers\API\BaseController::class, 'moveUp']);
                    Route::post('/movedown', [App\Http\Controllers\API\BaseController::class, 'moveDown']);
                });
                Route::prefix('lesson-page')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\LessonPageController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\LessonPageController::class, 'store']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\LessonPageController::class, 'show']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\LessonPageController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\LessonPageController::class, 'destroy']);
                    Route::post('/moveup', [App\Http\Controllers\API\BaseController::class, 'moveUp']);
                    Route::post('/movedown', [App\Http\Controllers\API\BaseController::class, 'moveDown']);
                });
                Route::prefix('lesson-page-rating')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\LessonPageRatingController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\LessonPageRatingController::class, 'store']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\LessonPageRatingController::class, 'show']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\LessonPageRatingController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\LessonPageRatingController::class, 'destroy']);
                });
                Route::prefix('user-course-category')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\UserCourseCategoryController::class, 'indexCms']);
                    Route::post('/create', [App\Http\Controllers\API\UserCourseCategoryController::class, 'storeCms']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\UserCourseCategoryController::class, 'updateCms']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\UserCourseCategoryController::class, 'destroy']);
                });
                Route::prefix('user-course')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\UserCourseController::class, 'index']);
                });
                Route::prefix('user-lesson')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\UserLessonController::class, 'index']);
                    Route::get('/answers', [App\Http\Controllers\API\UserLessonController::class, 'getAnswers']);
                    Route::post('/answers/{id}/feedbacks', [App\Http\Controllers\API\UserLessonController::class, 'feedbackAnswer']);
                });
                Route::prefix('user')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\UserController::class, 'getAllUser']);
                    Route::get('/{id}', [App\Http\Controllers\API\UserController::class, 'show']);
                    Route::post('/create/admin', [App\Http\Controllers\API\UserController::class, 'storeAdmin']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\UserController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\UserController::class, 'destroy']);
                    Route::post('/is-blocked/{id}', [App\Http\Controllers\API\UserController::class, 'updateIsBlocked']);
                });

                Route::prefix('exam')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\ExamController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\ExamController::class, 'store']);
                    Route::post('/import', [App\Http\Controllers\API\ExamController::class, 'import']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\ExamController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\ExamController::class, 'destroy']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\ExamController::class, 'show']);
                });

                Route::prefix('exam-config')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\ExamConfigController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\ExamConfigController::class, 'store']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\ExamConfigController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\ExamConfigController::class, 'destroy']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\ExamConfigController::class, 'show']);
                });

                Route::prefix('exam-poin')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\ExamPoinController::class, 'index']);
                });

                Route::prefix('exam-page')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\ExamPageController::class, 'indexAdmin']);
                    Route::post('/create', [App\Http\Controllers\API\ExamPageController::class, 'store']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\ExamPageController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\ExamPageController::class, 'destroy']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\ExamPageController::class, 'show']);
                    Route::post('/moveup', [App\Http\Controllers\API\BaseController::class, 'moveUp']);
                    Route::post('/movedown', [App\Http\Controllers\API\BaseController::class, 'moveDown']);
                });

                Route::prefix('question-bank')->group(function () {
                    Route::get('/', [App\Http\Controllers\QuestionBankController::class, 'index']);
                    Route::post('/import', [App\Http\Controllers\QuestionBankController::class, 'import']);
                    Route::post('/create', [App\Http\Controllers\QuestionBankController::class, 'store']);
                    Route::post('/update/{id}', [App\Http\Controllers\QuestionBankController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\QuestionBankController::class, 'destroy']);
                    Route::get('/read/{id}', [App\Http\Controllers\QuestionBankController::class, 'show']);
                });

                Route::prefix('exam-answer')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\ExamAnswerController::class, 'indexAdmin']);
                    Route::get('/journal-reflection/{lesson_id}', [App\Http\Controllers\API\ExamAnswerController::class, 'getJournalReflection']);
                });

                Route::prefix('voucher')->group(function () {
                    Route::get('/', [App\Http\Controllers\API\VoucherController::class, 'index']);
                    Route::post('/create', [App\Http\Controllers\API\VoucherController::class, 'store']);
                    Route::post('/update/{id}', [App\Http\Controllers\API\VoucherController::class, 'update']);
                    Route::post('/delete/{id}', [App\Http\Controllers\API\VoucherController::class, 'destroy']);
                    Route::get('/read/{id}', [App\Http\Controllers\API\VoucherController::class, 'show']);
                });
            });
        });

        Route::prefix('lesson')->group(function () {
            Route::get('/', [App\Http\Controllers\API\LessonController::class, 'indexUser']);
            Route::get('/read/{id}', [App\Http\Controllers\API\LessonController::class, 'show']);
        });
        Route::prefix('lesson-rating')->group(function () {
            Route::post('/create', [App\Http\Controllers\API\LessonRatingController::class, 'store']);
        });
        Route::prefix('user-lesson')->group(function () {
            Route::post('/create', [App\Http\Controllers\API\UserLessonController::class, 'store']);
            Route::post('/answer', [App\Http\Controllers\API\UserLessonController::class, 'answer']);
        });
        Route::prefix('user-course')->group(function () {
            Route::get('/', [App\Http\Controllers\API\UserCourseController::class, 'index']);
            Route::get('/read/{id}', [App\Http\Controllers\API\UserCourseController::class, 'show']);
            Route::post('/create', [App\Http\Controllers\API\UserCourseController::class, 'store']);
        });
        Route::prefix('user-course-category')->group(function () {
            Route::get('/', [App\Http\Controllers\API\UserCourseCategoryController::class, 'index']);
            Route::post('/create', [App\Http\Controllers\API\UserCourseCategoryController::class, 'store']);
            Route::get('/read/{id}', [App\Http\Controllers\API\UserCourseCategoryController::class, 'show']);
        });
        Route::prefix('lesson-page')->group(function () {
            Route::get('/', [App\Http\Controllers\API\LessonPageController::class, 'index']);
            Route::get('/read/{id}', [App\Http\Controllers\API\LessonPageController::class, 'show']);
        });

        Route::prefix('profile')->group(function () {
            Route::get('/me', [App\Http\Controllers\API\UserController::class, 'index']);
            Route::post('/update/{id}', [App\Http\Controllers\API\UserController::class, 'update']);
            Route::post('/update-password/{id}', [App\Http\Controllers\API\UserController::class, 'updatePassword']);
        });

        Route::prefix('voucher')->group(function () {
            Route::post('/redeem', [App\Http\Controllers\API\VoucherController::class, 'redeem']);
            Route::post('/voucher', [App\Http\Controllers\API\VoucherController::class, 'voucher']);
        });

        Route::prefix('exam')->group(function () {
            Route::get('/', [App\Http\Controllers\API\ExamController::class, 'index']);
            Route::get('/read/{id}', [App\Http\Controllers\API\ExamController::class, 'showUser']);
        });

        Route::prefix('exam-page')->group(function () {
            Route::get('/', [App\Http\Controllers\API\ExamPageController::class, 'index']);
        });

        Route::prefix('exam-poin')->group(function () {
            Route::post('/create', [App\Http\Controllers\API\ExamPoinController::class, 'store']);
            Route::post('/update/{id}', [App\Http\Controllers\API\ExamPoinController::class, 'update']);
            Route::get('/{exam_poin_id}', [App\Http\Controllers\API\ExamPoinController::class, 'showSpecific']);
            Route::get('/read/{id}', [App\Http\Controllers\API\ExamPoinController::class, 'show']);
        });

        Route::prefix('exam-answer')->group(function () {
            Route::post('/create', [App\Http\Controllers\API\ExamAnswerController::class, 'store']);
            Route::get('/last-answer/{exam_id}', [App\Http\Controllers\API\ExamAnswerController::class, 'lastAnswer']);
        });

        Route::prefix('exam-config')->group(function () {
            Route::get('/', [App\Http\Controllers\API\ExamConfigController::class, 'indexUser']);
        });

        Route::prefix('certificate')->group(function () {
            Route::post('/create', [App\Http\Controllers\API\CertificatesController::class, 'store']);
        });
    });

    Route::middleware(['optional.auth'])->group(function () {
        Route::prefix('course')->group(function () {
            Route::get('/', [App\Http\Controllers\API\CourseController::class, 'indexForUser']);
            Route::get('/read/{id}', [App\Http\Controllers\API\CourseController::class, 'showForUser']);
        });

        Route::prefix('banner-promotion')->group(function () {
            Route::get('/', [App\Http\Controllers\API\BannerPromotionController::class, 'index']);
        });

        Route::prefix('course-category')->group(function () {
            Route::get('/', [App\Http\Controllers\API\CourseCategoryController::class, 'indexUser']);
            Route::get('/read/{id}', [App\Http\Controllers\API\CourseCategoryController::class, 'show']);
        });
    });

    Route::prefix('certificate')->group(function () {
        Route::get('/read/{id}', [App\Http\Controllers\API\CertificatesController::class, 'show']);
    });

    Route::prefix('ckeditor')->group(function () {
        Route::post('/', [App\Http\Controllers\CkeditorFileUploadController::class, 'store']);
    });
});
