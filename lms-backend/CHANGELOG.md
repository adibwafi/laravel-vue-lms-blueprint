# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### 🔒 Security
- Security audit completed - 10 vulnerabilities identified (see SECURITY.md)
- Dependencies need updates (aws/aws-sdk-php, laravel/framework, nesbot/carbon)

### 📝 Documentation
- Comprehensive README.md update with detailed setup instructions
- Added SECURITY.md with security audit results and recommendations
- Added CHANGELOG.md for tracking project changes

### ⚠️ Known Issues
- CORS configuration too permissive (allows all origins)
- API rate limiting partially disabled
- Some commented debug statements in codebase

---

## [1.0.0] - 2024-03-27

### ✨ Added
- **Reflective Learning**: Added reflective learning features to lessons
- **Scope Management**: Added scope field to courses and lessons
- **User Lesson Answers**: New table for storing student answers with feedback
- **Prakerja State Tracking**: Enhanced Prakerja integration with state tracking
- **Redeem Code**: Voucher redeem functionality for course access

### 🔧 Changed
- Modified `user_course_categories` table to include redeem_code and prakerja_state
- Renamed `is_reflective` to clearer field name in lessons table
- Updated scope column in courses table
- Improved user lesson tracking with URL and notes fields

### 🐛 Fixed
- Fixed redeem code logic for Prakerja courses
- Improved voucher validation and status checking
- Enhanced success response for redeem code endpoint

---

## [0.9.0] - 2023-04-06

### ✨ Added
- **Question Bank System**: Reusable question pool for exams
- Added question bank to exam configuration
- Question bank support in exam pages
- Question bank tracking in exam scores

### 🔧 Changed
- Enhanced exam system to support both inline questions and question bank
- Improved exam randomization with random seed

---

## [0.8.0] - 2023-02-21

### ✨ Added
- **Quiz System**: Quiz functionality integrated with lessons
- **Certificate System**: Auto-generation of certificates upon course completion
- Certificate identifier for unique certificate tracking
- Journal and reflection assignments

### 🔧 Changed
- Modified lesson system to support quiz mode
- Enhanced exam system with KKM (passing grade) and max attempts
- Improved exam answer tracking and storage
- Changed sorter data type in lesson pages

### 🐛 Fixed
- Fixed exam page ID reference in exam answers
- Corrected exam configuration for quiz mode

---

## [0.7.0] - 2023-01-27

### ✨ Added
- **Voucher System**: Complete voucher management with redemption
- **Zoom Integration**: Optional Zoom link override for courses and lessons
- Valid property for voucher expiration tracking
- Updated_by tracking for voucher management

### 🔧 Changed
- Restructured course-tutor relationship to support multiple tutors per category
- Enhanced course categories with tutor assignments
- Improved link payment handling for course categories
- Modified course structure to remove direct tutor relationship

### 🐛 Fixed
- Fixed voucher table naming consistency
- Corrected Zoom link override behavior

---

## [0.6.0] - 2023-01-23

### ✨ Added
- **Prakerja Integration**: Full integration with Prakerja platform
- Prakerja course ID mapping
- Prakerja-specific columns for courses and categories
- Order field for courses and exam pages

### 🔧 Changed
- Changed description fields to TEXT type for longer content
- Modified course and lesson structures for Prakerja compatibility
- Enhanced exam page ordering system

### 🐛 Fixed
- Fixed data type issues with description fields
- Corrected course and lesson column configurations

---

## [0.5.0] - 2022-12-21

### ✨ Added
- **Exam System**: Complete exam/assessment functionality
- Exam pages with questions and answers
- Exam configuration (time limits, passing scores)
- Exam scoring and results tracking
- Time tracking for exam completion
- Certificate generation upon passing

### 🔧 Changed
- Enhanced user course tracking with exam results
- Improved lesson completion tracking

---

## [0.4.0] - 2022-11-27

### ✨ Added
- **User Profiles**: Phone number and profile image support
- **Lesson Rating System**: Students can rate lessons
- **User Blocking**: Admin ability to block malicious users
- Lesson completion status tracking

### 🔧 Changed
- Enhanced user model with additional fields
- Improved lesson tracking system

---

## [0.3.0] - 2022-11-02

### ✨ Added
- **Paid Courses**: Support for paid courses and categories
- Price and link payment fields for course categories
- User course category enrollment tracking
- Course completion tracking

### 🔧 Changed
- Modified course categories to support pricing
- Enhanced user course tracking

---

## [0.2.0] - 2022-10-18

### ✨ Added
- **Payment Integration**: Payment link support for courses
- Course status field (draft, published, archived)
- Banner promotion ordering (move up/down)
- Lesson page ordering improvements

### 🔧 Changed
- Enhanced banner promotion management
- Improved lesson page organization

---

## [0.1.0] - 2022-09-23

### ✨ Added
- **Initial Release**: Core LMS functionality
- User authentication with OTP verification
- User registration and email verification
- Password reset functionality
- Course management (CRUD)
- Course categories
- Lessons and lesson pages
- Lesson ratings
- Tutor management
- Banner promotions
- Role-based access control (Super-Admin, User)
- AWS S3 integration for file storage
- Email notifications via Mailgun

### 🛠 Infrastructure
- Laravel 8.x framework
- MySQL database
- Docker support
- PHPUnit testing setup
- Seeder for development data

---

## Version Notes

### Breaking Changes
- **v1.0.0**: Redeem code moved from user_courses to user_course_categories
- **v0.7.0**: Course-tutor relationship restructured (breaking for existing data)
- **v0.6.0**: Prakerja integration requires new environment variables

### Migration Notes
- Always backup database before running migrations
- Run `php artisan migrate` after pulling new changes
- Check `.env.example` for new environment variables
- Run `php artisan optimize:clear` after updates

---

## Deployment History

### Recent Deployments
- **2024-11-25**: Docker configuration improvements for testing
- **2024-11-20**: Rate limiting added to voucher redeem endpoint
- **2024-11-18**: Prakerja voucher redeem logic improvements
- **2024-11-15**: PRAM code exclusion for database commits

### Infrastructure Changes
- GitHub Actions CI/CD pipeline implemented
- AWS App Runner deployment (staging & production)
- Automated testing on PR submissions
- Container-based deployments

---

## Contributors

This project is maintained by the **Muhamad Adibwafi Menako**.

For contribution guidelines, contact your team lead.

---

## Support

For questions or issues:
1. Check this CHANGELOG for recent changes
2. Review README.md for setup instructions
3. Check SECURITY.md for security concerns
4. Contact development team via internal channels

---

**Last Updated**: November 25, 2025
