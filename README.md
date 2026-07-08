# E-Learning Platform - Complete Implementation

A comprehensive Laravel 11 e-learning platform with roles, courses, lessons, quizzes, payments, video processing, and more.

## Features

### Core Platform
- ✅ Multi-role system (Admin, Instructor, Student)
- ✅ Course management with sections & lessons
- ✅ Quiz system with multiple question types
- ✅ Progress tracking & enrollment management
- ✅ Certificate generation (PDF)
- ✅ Discussion boards for lessons

### Video Streaming
- ✅ FFmpeg-based HLS transcoding (3 bitrates)
- ✅ Poster image generation
- ✅ S3 storage integration
- ✅ CloudFront CDN optimization
- ✅ Signed URLs for private content

### Payments
- ✅ Stripe integration (checkout, webhooks)
- ✅ PromptPay QR code generation
- ✅ Order & refund management
- ✅ Coupon system

### Analytics & Dashboards
- ✅ Admin dashboard (users, revenue, courses)
- ✅ Instructor dashboard (course stats, revenue)
- ✅ Student dashboard (progress, recommendations)
- ✅ Revenue charts (30-day history)

### API & Real-time
- ✅ RESTful API with Sanctum auth
- ✅ Livewire reactive components
- ✅ Advanced search with filters
- ✅ Event-driven notifications

### Testing
- ✅ Feature tests (enrollment, authorization)
- ✅ Unit tests (services)
- ✅ Policy tests (access control)

## Installation

### Prerequisites
```bash
php 8.2+
composer
node.js 18+
ffmpeg & ffprobe
```

### Setup

1. **Clone & Install**
```bash
git clone <repo>
cd <repo>
composer install
npm install && npm run build
```

2. **Environment**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Database**
```bash
php artisan migrate --seed
```

4. **Storage (S3)**
```env
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

5. **Payments**
```env
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
PROMPTPAY_MERCHANT_ID=...
PROMPTPAY_PHONE=...
```

6. **Queue (for video transcoding)**
```env
QUEUE_CONNECTION=database
```

Start worker:
```bash
php artisan queue:work
```

## Routes

### Web
```
GET  /dashboard              Dashboard (role-based)
GET  /courses               Course listing
GET  /courses/{slug}        Course detail
POST /enrollments           Enroll in course
GET  /certificates/{id}     Download certificate
```

### API
```
GET  /api/courses           List published courses
GET  /api/courses/{id}      Course detail
GET  /api/courses/featured  Featured courses
GET  /api/enrollments       User enrollments
GET  /api/search            Advanced search
POST /api/payments/checkout Create checkout
POST /api/payments/confirm  Confirm payment
POST /webhooks/stripe       Stripe webhook
```

## Key Models

- User (with roles: student/instructor/admin)
- Course, Section, Lesson
- Enrollment, LessonProgress
- Certificate
- Quiz, QuizQuestion, QuizOption, QuizAttempt
- Order, OrderItem, Coupon
- Review, Announcement
- Discussion, DiscussionReply
- Video (HLS metadata)

## Service Layer

- `CourseService` — course CRUD & search
- `EnrollmentService` — progress tracking
- `PaymentService` — Stripe & PromptPay
- `VideoService` — HLS streaming URLs
- `CertificateService` — PDF generation

## Events

- `UserEnrolledInCourse`
- `CoursePublished`
- `PaymentCompleted`
- `VideoTranscodingCompleted`
- `EnrollmentProgressUpdated`

## Livewire Components

- `CourseList` — searchable listings
- `CourseEditor` — create/edit courses
- `EnrollButton` — enroll workflow
- `LessonViewer` — video player + progress
- `ReviewForm` — submit course reviews
- `DiscussionBoard` — Q&A threads

## Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=EnrollmentTest

# Run with coverage
php artisan test --coverage
```

## Deployment

### Production Checklist
- [ ] Use `.env` for secrets
- [ ] Set `APP_DEBUG=false`
- [ ] Configure S3 + CloudFront
- [ ] Setup Stripe webhook
- [ ] Configure queue worker (supervisor)
- [ ] Enable email (SMTP or SES)
- [ ] Setup Laravel Horizon for job monitoring
- [ ] Configure backups (S3)

## File Structure

```
app/
├── Models/              (25+ Eloquent models)
├── Services/            (Payment, Video, Certificate)
├── Actions/             (Single-purpose actions)
├── Policies/            (Authorization rules)
├── Http/
│   ├── Controllers/     (API + Web)
│   ├── Livewire/        (Reactive components)
│   ├── Resources/       (JSON transformers)
│   └── Middleware/
├── Events/              (Domain events)
├── Listeners/           (Event handlers)
├── Notifications/       (Mail + database)
├── Jobs/                (Video transcoding)
└── Repositories/        (Data access abstraction)

database/
├── migrations/          (6 migration files)
├── factories/           (8 factories)
└── seeders/             (DatabaseSeeder)

resources/
├── views/
│   ├── dashboard/       (Admin, Instructor, Student)
│   ├── courses/
│   ├── certificates/
│   └── livewire/
└── js/
    └── app.js           (Vue/Alpine setup)

tests/
├── Feature/             (Integration tests)
└── Unit/                (Service tests)
```

## Contributing

1. Create branch: `git checkout -b feature/xyz`
2. Commit changes: `git commit -am 'Add feature xyz'`
3. Push: `git push origin feature/xyz`
4. Open PR

## License

MIT
