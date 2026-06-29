# Contributing Guide

Terima kasih telah berkontribusi pada proyek **LMS Backend Startup Campus**! Dokumen ini berisi panduan untuk developer yang akan melanjutkan atau berkontribusi pada codebase ini.

---

## 📋 Daftar Isi
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Git Workflow](#git-workflow)
- [Testing Guidelines](#testing-guidelines)
- [Database Changes](#database-changes)
- [API Development](#api-development)
- [Code Review Process](#code-review-process)
- [Common Pitfalls](#common-pitfalls)

---

## 🚀 Getting Started

### Prerequisites
Sebelum memulai development, pastikan Anda sudah:
1. Membaca `README.md` secara lengkap
2. Setup environment lokal (database, Redis, dll)
3. Akses ke Bitwarden SC untuk credentials
4. Terhubung dengan tim development
5. Familiar dengan Laravel 8.x

### First Time Setup
```bash
# Clone repository
git clone https://github.com/startup-campus-id/lms-backend.git
cd lms-backend

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed
php artisan create:superadmin

# Test aplikasi
php artisan serve
```

---

## 🔄 Development Workflow

### 1. Sebelum Memulai Task Baru

```bash
# Update branch develop terbaru
git checkout develop
git pull origin develop

# Buat branch baru untuk feature/bugfix
git checkout -b feature/nama-fitur
# atau
git checkout -b bugfix/nama-bug
```

### 2. Naming Conventions untuk Branch

- **Feature**: `feature/short-description`
  - Contoh: `feature/add-assignment-submission`
- **Bugfix**: `bugfix/short-description`
  - Contoh: `bugfix/fix-exam-timer`
- **Hotfix**: `hotfix/short-description`
  - Contoh: `hotfix/security-patch`
- **Refactor**: `refactor/short-description`
  - Contoh: `refactor/improve-exam-controller`

### 3. Development Cycle

1. **Analyze**: Pahami requirement dengan baik
2. **Design**: Rencanakan struktur code/database
3. **Implement**: Tulis code dengan clean & readable
4. **Test**: Test fungsionalitas secara menyeluruh
5. **Document**: Update dokumentasi jika perlu
6. **Review**: Self-review sebelum commit
7. **Commit**: Commit dengan message yang jelas
8. **Push**: Push ke branch Anda
9. **PR**: Buat Pull Request ke develop

---

## 📝 Coding Standards

### Laravel Best Practices

#### 1. Controller
```php
// ✅ GOOD - Thin controller
public function store(CreateCourseRequest $request)
{
    $course = $this->courseService->create($request->validated());
    return response()->json(['data' => $course], 201);
}

// ❌ BAD - Fat controller with business logic
public function store(Request $request)
{
    $course = new Course();
    $course->title = $request->title;
    // ... banyak logic
    $course->save();
    // ... lebih banyak logic
}
```

#### 2. Validation
```php
// ✅ GOOD - Form Request
class CreateCourseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:course_categories,id',
        ];
    }
}

// ❌ BAD - Inline validation
$request->validate([...]);
```

#### 3. Eloquent Relationships
```php
// ✅ GOOD - Use Eager Loading
$courses = Course::with(['category', 'tutors', 'lessons'])->get();

// ❌ BAD - N+1 Query Problem
$courses = Course::all();
foreach ($courses as $course) {
    echo $course->category->name; // N+1 query
}
```

#### 4. Query Builder
```php
// ✅ GOOD - Readable & Chainable
$courses = Course::query()
    ->where('status', 'published')
    ->whereHas('category', fn($q) => $q->where('is_active', true))
    ->with('tutors')
    ->latest()
    ->paginate(15);

// ❌ BAD - Complex & Hard to Read
$courses = Course::where('status', 'published')->whereHas('category', function($q) { $q->where('is_active', true); })->with('tutors')->latest()->paginate(15);
```

### PHP Standards

```php
// ✅ GOOD - PSR-12 Standard
namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function create(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $course = Course::create($data);
            $this->attachTutors($course, $data['tutor_ids']);
            return $course;
        });
    }
    
    private function attachTutors(Course $course, array $tutorIds): void
    {
        $course->tutors()->attach($tutorIds);
    }
}

// ❌ BAD
class CourseService {
  public function create($data) {
    $course = Course::create($data);
    $course->tutors()->attach($data['tutor_ids']);
    return $course;
  }
}
```

### Naming Conventions

#### Variables & Functions
```php
// ✅ GOOD - Descriptive names
$activeUsers = User::where('status', 'active')->get();
$completedCoursesCount = $user->courses()->completed()->count();

public function calculateCompletionPercentage(): float

// ❌ BAD - Unclear names
$users = User::where('status', 'active')->get();
$count = $user->courses()->completed()->count();

public function calc(): float
```

#### Database Tables & Columns
```php
// ✅ GOOD
// Table: user_course_categories
// Columns: id, user_id, course_category_id, enrolled_at, completed_at

// ❌ BAD
// Table: UserCourseCategory
// Columns: Id, userId, courseCatID, created
```

---

## 🌿 Git Workflow

### Commit Messages

Format: `<type>(<scope>): <subject>`

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, semicolons)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

**Examples:**
```bash
# ✅ GOOD
git commit -m "feat(exam): add question bank support"
git commit -m "fix(voucher): resolve redeem code validation issue"
git commit -m "docs(readme): update installation instructions"
git commit -m "refactor(course): extract service layer from controller"

# ❌ BAD
git commit -m "update"
git commit -m "fix bug"
git commit -m "WIP"
```

### Pull Request Process

#### 1. Before Creating PR

```bash
# Update dari develop terbaru
git checkout develop
git pull origin develop

# Merge develop ke branch Anda
git checkout feature/your-feature
git merge develop

# Resolve conflicts jika ada
# Test aplikasi
php artisan test

# Push ke remote
git push origin feature/your-feature
```

#### 2. PR Template

Saat membuat PR, sertakan informasi:

```markdown
## Description
Jelaskan perubahan yang Anda buat

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Changes Made
- List perubahan 1
- List perubahan 2

## Testing
- [ ] Unit tests pass
- [ ] Feature tests pass
- [ ] Manual testing done

## Screenshots (if applicable)
Attach screenshots jika ada UI changes

## Checklist
- [ ] Code follows project coding standards
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No commented debug code (dd, dump, var_dump)
- [ ] Environment variables documented
- [ ] Migration runs successfully
```

#### 3. PR Review Guidelines

**Reviewer Checklist:**
- [ ] Code quality & readability
- [ ] Follows Laravel & project conventions
- [ ] Proper error handling
- [ ] Security considerations
- [ ] Performance implications
- [ ] Database queries optimized
- [ ] Tests included
- [ ] Documentation updated

---

## 🧪 Testing Guidelines

### Writing Tests

```php
// tests/Feature/CourseTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_course()
    {
        // Arrange
        $admin = User::factory()->create();
        $admin->assignRole('Super-Admin');
        Sanctum::actingAs($admin);
        
        $courseData = [
            'title' => 'Test Course',
            'description' => 'Test Description',
            'category_id' => 1,
        ];
        
        // Act
        $response = $this->postJson('/v1/admin/course/create', $courseData);
        
        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('courses', ['title' => 'Test Course']);
    }
    
    /** @test */
    public function user_cannot_create_course()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        // Act
        $response = $this->postJson('/v1/admin/course/create', []);
        
        // Assert
        $response->assertStatus(403);
    }
}
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/CourseTest.php

# Run specific test method
php artisan test --filter test_admin_can_create_course

# With coverage
php artisan test --coverage
```

---

## 🗄 Database Changes

### Creating Migrations

```bash
# Create migration
php artisan make:migration create_assignments_table
php artisan make:migration add_status_to_courses_table
```

### Migration Best Practices

```php
// ✅ GOOD - Reversible & Safe
public function up()
{
    Schema::create('assignments', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('lesson_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->text('description');
        $table->dateTime('due_date')->nullable();
        $table->timestamps();
        $table->softDeletes();
        
        $table->index('lesson_id');
    });
}

public function down()
{
    Schema::dropIfExists('assignments');
}

// ❌ BAD - Not reversible, no foreign keys
public function up()
{
    Schema::create('assignments', function (Blueprint $table) {
        $table->id();
        $table->integer('lesson');
        $table->text('data');
    });
}

public function down()
{
    // Empty - tidak bisa rollback
}
```

### Seeding Data

```php
// database/seeders/CourseSeeder.php

public function run()
{
    // ✅ GOOD - Use factories & realistic data
    $categories = CourseCategory::factory(5)->create();
    
    $categories->each(function ($category) {
        Course::factory(3)->create([
            'course_category_id' => $category->id,
        ])->each(function ($course) {
            Lesson::factory(5)->create([
                'course_id' => $course->id,
            ]);
        });
    });
}
```

---

## 🌐 API Development

### API Response Format

```php
// ✅ GOOD - Consistent format
return response()->json([
    'success' => true,
    'message' => 'Course created successfully',
    'data' => new CourseResource($course),
], 201);

// Error response
return response()->json([
    'success' => false,
    'message' => 'Validation error',
    'errors' => $validator->errors(),
], 422);

// ❌ BAD - Inconsistent format
return ['course' => $course];
return $course;
```

### API Resources

```php
// app/Http/Resources/CourseResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail_url' => $this->thumbnail_url,
            'category' => new CourseCategoryResource($this->whenLoaded('category')),
            'tutors' => TutorResource::collection($this->whenLoaded('tutors')),
            'lessons_count' => $this->when($this->lessons_count !== null, $this->lessons_count),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
```

---

## 🔍 Code Review Process

### Sebagai Author

1. **Self-Review First**
   - Review semua perubahan Anda
   - Test semua fungsionalitas
   - Remove commented code
   - Check for console.log/dd/dump

2. **Provide Context**
   - Jelaskan "why" bukan hanya "what"
   - Link ke issue/ticket jika ada
   - Highlight critical changes

3. **Respond Professionally**
   - Terima feedback dengan baik
   - Diskusikan jika tidak setuju
   - Update code berdasarkan feedback

### Sebagai Reviewer

1. **Focus On**
   - Code correctness
   - Security implications
   - Performance issues
   - Maintainability
   - Test coverage

2. **Provide Constructive Feedback**
   ```markdown
   # ✅ GOOD
   "Consider using eager loading here to avoid N+1 queries. 
   You can add ->with('tutors') to the query."
   
   # ❌ BAD
   "This is slow."
   ```

3. **Be Specific**
   - Point to exact line numbers
   - Suggest alternatives
   - Explain reasoning

---

## ⚠️ Common Pitfalls

### 1. N+1 Query Problem

```php
// ❌ BAD - N+1 queries
$courses = Course::all();
foreach ($courses as $course) {
    echo $course->category->name; // Query per course
}

// ✅ GOOD - Single query with eager loading
$courses = Course::with('category')->get();
foreach ($courses as $course) {
    echo $course->category->name;
}
```

### 2. Mass Assignment Vulnerability

```php
// ❌ BAD - Unprotected mass assignment
Course::create($request->all());

// ✅ GOOD - Use fillable or validation
Course::create($request->only(['title', 'description', 'category_id']));
// or use validated data
Course::create($request->validated());
```

### 3. Not Using Transactions

```php
// ❌ BAD - No transaction
$course = Course::create($data);
$course->tutors()->attach($tutorIds);
$course->lessons()->createMany($lessons);
// If lessons creation fails, course & tutors already created

// ✅ GOOD - Use transaction
DB::transaction(function () use ($data, $tutorIds, $lessons) {
    $course = Course::create($data);
    $course->tutors()->attach($tutorIds);
    $course->lessons()->createMany($lessons);
});
```

### 4. Hardcoded Values

```php
// ❌ BAD
if ($user->role === 'Super-Admin') { ... }
$maxUploadSize = 5242880; // Magic number

// ✅ GOOD
if ($user->hasRole('Super-Admin')) { ... }
$maxUploadSize = config('app.max_upload_size');
```

### 5. Not Handling Errors

```php
// ❌ BAD
$user = User::findOrFail($id);
$course->delete();

// ✅ GOOD
try {
    $user = User::findOrFail($id);
    $course->delete();
    return response()->json(['message' => 'Success'], 200);
} catch (ModelNotFoundException $e) {
    return response()->json(['error' => 'User not found'], 404);
} catch (\Exception $e) {
    Log::error('Course deletion failed', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Server error'], 500);
}
```

---

## 🆘 Getting Help

### Resources
1. **Laravel Documentation**: https://laravel.com/docs/8.x
2. **Project README**: Comprehensive setup guide
3. **SECURITY.md**: Security guidelines
4. **CHANGELOG.md**: Recent changes & history

### Team Communication
- **Slack/Discord**: Quick questions
- **GitHub Issues**: Bug reports & feature requests
- **Pull Request Comments**: Code-specific discussions
- **Team Meetings**: Complex discussions

### Before Asking
1. Search existing issues/PRs
2. Check Laravel documentation
3. Google the error message
4. Try debugging yourself

### When Asking
1. Provide context
2. Show what you've tried
3. Include error messages
4. Attach relevant code snippets

---

## 📚 Additional Resources

- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [PHP The Right Way](https://phptherightway.com/)
- [PSR Standards](https://www.php-fig.org/psr/)
- [Eloquent Performance Patterns](https://eloquent-course.reinink.ca/)

---

## 🎉 Thank You!

Terima kasih telah membaca contributing guide ini. Happy coding! 🚀

---

**Last Updated**: November 2025  
**Maintained by**: Startup Campus Development Team
