/* eslint-disable prettier/prettier */
import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "Login",
    component: () => import("../views/website/auth/Login.vue"),
  },
  {
    path: "/auth/register",
    name: "Register",
    component: () => import("../views/website/auth/Register.vue"),
  },
  {
    path: "/auth/privacy-policy",
    name: "PrivacyPolicy",
    component: () => import("../views/website/auth/PrivacyPolicy.vue"),
  },
  {
    path: "/auth/verification",
    name: "Verification",
    component: () => import("../views/website/auth/Verification.vue"),
  },
  {
    path: "/auth/verify-otp/:type/:otp",
    name: "VerificationViaLink",
    component: () => import("../views/website/auth/VerificationViaLink.vue"),
  },
  {
    path: "/auth/reset-password",
    name: "ResetPassword",
    component: () => import("../views/website/auth/ResetPassword.vue"),
  },
  {
    path: "/home",
    name: "Home",
    component: () => import("../views/website/home/Home.vue"),
  },
  {
    path: "/promotion",
    name: "Promotion",
    component: () => import("../views/website/home/Promotion.vue"),
  },
  {
    path: "/profile",
    name: "Profile",
    component: () => import("../views/website/profile/Profile.vue"),
  },
  {
    path: "/profile/edit",
    name: "ProfileEdit",
    component: () => import("../views/website/profile/ProfileEdit.vue"),
  },
  {
    path: "/achievement",
    name: "Achievement",
    component: () => import("../views/website/profile/Achievement.vue"),
  },
  {
    path: "/profile/change-password",
    name: "ProfileChangePassword",
    component: () =>
      import("../views/website/profile/ProfileChangePassword.vue"),
  },
  {
    path: "/welcome",
    name: "Welcome",
    component: () => import("../views/website/Welcome.vue"),
  },
  {
    path: "/my-course",
    name: "MyCourse",
    component: () => import("../views/website/course/Course.vue"),
  },
  {
    path: "/course",
    name: "Course",
    component: () => import("../views/website/home/Course.vue"),
  },
  {
    path: "/course/:id",
    name: "CategoryDetail",
    component: () => import("../views/website/course/CategoryDetail.vue"),
  },
  {
    path: "/course/:id/about",
    name: "CategoryAbout",
    component: () => import("../views/website/course/CategoryDetailAbout.vue"),
  },
  {
    path: "/topic/:id",
    name: "CourseDetail",
    component: () => import("../views/website/course/CourseDetail.vue"),
  },
  {
    path: "/topic/:id/about",
    name: "CourseAbout",
    component: () => import("../views/website/course/CourseDetailAbout.vue"),
  },
  {
    path: "/topic/:id_topic/exam/:id",
    name: "CourseExamRules",
    component: () => import("../views/website/course/CourseExamRules.vue"),
  },
  {
    path: "/topic/:id_topic/lesson/:id",
    name: "LessonPage",
    component: () => import("../views/website/lesson/LessonPage.vue"),
  },
  {
    path: "/topic/:id_topic/lesson/:id/rating",
    name: "LessonPageRating",
    component: () => import("../views/website/lesson/LessonPageRating.vue"),
  },
  {
    path: "/topic/:id_topic/exam/:id/page",
    name: "ExamPage",
    component: () => import("../views/website/exam/ExamPage.vue"),
  },
  {
    path: "/topic/:id_topic/exam/:id/score",
    name: "ExamScore",
    component: () => import("../views/website/exam/ExamScore.vue"),
  },
  {
    path: "/topic/:id_topic/exam/:id/score/:id_exam_poin",
    name: "ExamPoinScore",
    component: () => import("../views/website/exam/ExamScore.vue"),
  },
  {
    path: "/certificate/:id",
    name: "Certificate",
    component: () => import("../views/website/certificate/Index.vue"),
  },
  {
    path: "/redeem",
    name: "RedeemVoucher",
    component: () => import("../views/website/voucher/RedeemVoucher.vue"),
  },
  {
    path: "/redeem/success",
    name: "SuccessRedeemVoucher",
    component: () =>
      import("../views/website/voucher/SuccessRedeemVoucher.vue"),
  },

  // Admin Routes
  {
    path: "/admin",
    name: "LoginAdmin",
    component: () => import("../views/admin/Login.vue"),
  },
  {
    path: "/admin/welcome",
    name: "AdminWelcome",
    component: () => import("../views/admin/Welcome.vue"),
  },
  {
    path: "/admin/banner-promo",
    name: "AdminPromo",
    component: () => import("../views/admin/promo/Promo.vue"),
  },
  {
    path: "/admin/voucher",
    name: "AdminVoucher",
    component: () => import("../views/admin/voucher/Voucher.vue"),
  },
  {
    path: "/admin/expert",
    name: "AdminExpert",
    component: () => import("../views/admin/expert/Expert.vue"),
  },
  {
    path: "/admin/course-category",
    name: "AdminCourseCate",
    component: () => import("../views/admin/course/CourseCategory.vue"),
  },
  {
    path: "/admin/course-category/add",
    name: "AdminCourseCateAdd",
    component: () => import("../views/admin/course/CourseCategoryAdd.vue"),
  },
  {
    path: "/admin/course-category/:id/edit",
    name: "AdminCourseCateEdit",
    component: () => import("../views/admin/course/CourseCategoryAdd.vue"),
  },
  {
    path: "/admin/course",
    name: "AdminCourse",
    component: () => import("../views/admin/course/Course.vue"),
  },
  {
    path: "/admin/course/add",
    name: "AdminCourseAdd",
    component: () => import("../views/admin/course/CourseAdd.vue"),
  },
  {
    path: "/admin/course/:id/edit",
    name: "AdminCourseEdit",
    component: () => import("../views/admin/course/CourseAdd.vue"),
  },
  {
    path: "/admin/course/:id/lesson",
    name: "AdminLesson",
    component: () => import("../views/admin/lesson/Lesson.vue"),
  },
  {
    path: "/admin/course/:course_id/lesson/:id/page",
    name: "AdminLessonPage",
    component: () => import("../views/admin/lesson/LessonPage.vue"),
  },
  {
    path: "/admin/course/:course_id/lesson/:lesson_id/exam",
    name: "AdminLessonExam",
    component: () => import("../views/admin/exam/Exam.vue"),
  },
  {
    path: "/admin/course/:course_id/lesson/:lesson_id/exam/:id/page",
    name: "AdminExamPageAdmin",
    component: () => import("../views/admin/exam/ExamPage.vue"),
  },
  {
    path: "/admin/course/:course_id/lesson/:lesson_id/exam/:exam_id/page/add",
    name: "AdminExamPageAddAdmin",
    component: () => import("../views/admin/exam/ExamPageAdd.vue"),
  },
  {
    path: "/admin/course/:course_id/lesson/:lesson_id/exam/:exam_id/page/:exam_page_id/edit",
    name: "AdminExamPageEditAdmin",
    component: () => import("../views/admin/exam/ExamPageAdd.vue"),
  },
  {
    path: "/admin/course/:course_id/lesson/:lesson_id/page/add",
    name: "AdminLessonPageAdd",
    component: () => import("../views/admin/lesson/LessonPageAdd.vue"),
  },
  {
    path: "/admin/course/:course_id/lesson/:lesson_id/page/:lesson_page_id/edit",
    name: "AdminLessonPageEdit",
    component: () => import("../views/admin/lesson/LessonPageAdd.vue"),
  },
  {
    path: "/admin/tracking/user",
    name: "AdminTrackingUser",
    component: () => import("../views/admin/tracking/User.vue"),
  },
  {
    path: "/admin/tracking/exam",
    name: "AdminTrackingExam",
    component: () => import("../views/admin/tracking/Exam.vue"),
  },
  {
    path: "/admin/tracking/exam/:exam_id/score",
    name: "AdminTrackingExamScore",
    component: () => import("../views/admin/tracking/ExamScore.vue"),
  },
  {
    path: "/admin/tracking/exam/:exam_id/answer",
    name: "AdminTrackingExamAnswer",
    component: () => import("../views/admin/tracking/ExamAnswer.vue"),
  },
  {
    path: "/admin/tracking/exam/:exam_id/answer-all",
    name: "AdminTrackingExamAnswerAll",
    component: () => import("../views/admin/tracking/ExamAnswerAll.vue"),
  },
  {
    path: "/admin/tracking/course-category",
    name: "AdminTrackingCourseCategory",
    component: () => import("../views/admin/tracking/CourseCategory.vue"),
  },
  {
    path: "/admin/tracking/course-category/:course_category_id/enroll",
    name: "AdminTrackingCourseCategoryEnroll",
    component: () => import("../views/admin/tracking/CourseCategoryEnroll.vue"),
  },
  {
    path: "/admin/tracking/course-category/:course_category_id/course",
    name: "AdminTrackingCourseCategoryCourse",
    component: () => import("../views/admin/tracking/Course.vue"),
  },
  {
    path: "/admin/tracking/course-category/:course_category_id/course/:course_id/enroll",
    name: "AdminTrackingCourseEnroll",
    component: () => import("../views/admin/tracking/CourseEnroll.vue"),
  },
  {
    path: "/admin/tracking/course-category/:course_category_id/course/:course_id/lesson",
    name: "AdminTrackingLesson",
    component: () => import("../views/admin/tracking/Lesson.vue"),
  },
  {
    path: "/admin/tracking/course-category/:course_category_id/course/:course_id/lesson/:lesson_id/enroll",
    name: "AdminTrackingLessonEnroll",
    component: () => import("../views/admin/tracking/LessonEnroll.vue"),
  },
  {
    path: "/admin/tracking/feedback",
    name: "AdminTrackingFeedback",
    component: () => import("../views/admin/feedback/Course.vue"),
  },
  {
    path: "/admin/tracking/feedback/:course_id",
    name: "AdminTrackingFeedbackLesson",
    component: () => import("../views/admin/feedback/Lesson.vue"),
  },
  {
    path: "/admin/tracking/feedback/:course_id/journal-reflection",
    name: "AdminTrackingFeedbackJournalReflection",
    component: () => import("../views/admin/feedback/JournalReflection.vue"),
  },
  {
    path: "/admin/tracking/feedback/:course_id/journal-reflection/:id",
    name: "AdminTrackingFeedbackJournalReflectionSubmission",
    component: () =>
      import("../views/admin/feedback/JournalReflectionSubmission.vue"),
  },
  {
    path: "/admin/admins",
    name: "AdminAdmins",
    component: () => import("../views/admin/admin/Admins.vue"),
  },
  {
    path: "/admin/users",
    name: "AdminUsers",
    component: () => import("../views/admin/user/Users.vue"),
  },
  {
    path: "/admin/users/:id/courses",
    name: "AdminUsersCourses",
    component: () => import("../views/admin/user/UsersCourses.vue"),
  },
  {
    path: "/admin/course/:course_id/exam",
    name: "AdminExam",
    component: () => import("../views/admin/exam/Exam.vue"),
  },
  {
    path: "/admin/course/:course_id/exam/:id/question-bank",
    name: "AdminQuestionBank",
    component: () => import("../views/admin/exam/QuestionBank.vue"),
  },
  {
    path: "/admin/course/:course_id/exam/:id/question-bank/:question_bank_id/page",
    name: "AdminQuestionBankPage",
    component: () => import("../views/admin/exam/ExamPage.vue"),
  },
  {
    path: "/admin/course/:course_id/exam/:exam_id/question-bank/:question_bank_id/page/add",
    name: "AdminQuestionBankPageAdd",
    component: () => import("../views/admin/exam/ExamPageAdd.vue"),
  },
  {
    path: "/admin/course/:course_id/exam/:exam_id/question-bank/:question_bank_id/page/:exam_page_id/edit",
    name: "AdminQuestionBankPageEdit",
    component: () => import("../views/admin/exam/ExamPageAdd.vue"),
  },
  {
    path: "/admin/course/:course_id/exam/:id/page",
    name: "AdminExamPage",
    component: () => import("../views/admin/exam/ExamPage.vue"),
  },
  {
    path: "/admin/course/:course_id/exam/:exam_id/page/add",
    name: "AdminExamPageAdd",
    component: () => import("../views/admin/exam/ExamPageAdd.vue"),
  },
  {
    path: "/admin/course/:course_id/exam/:exam_id/page/:exam_page_id/edit",
    name: "AdminExamPageEdit",
    component: () => import("../views/admin/exam/ExamPageAdd.vue"),
  },
  {
    path: "/admin/exam-config",
    name: "AdminExamConfig",
    component: () => import("../views/admin/exam/ExamConfig.vue"),
  },
  // ------------------------------
  {
    path: "/*",
    name: "PageNotFound",
    component: () => import("../views/NotFound.vue"),
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 };
  },
  routes,
});

export default router;
