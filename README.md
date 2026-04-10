# Task Management System

A modern, full-featured task management system built with Laravel 11, Jetstream, Livewire, and Tailwind CSS.

## Features

- ✅ User Authentication (Laravel Jetstream)
- ✅ Create, Read, Update, Delete (CRUD) Tasks
- ✅ Task Status Management (Pending, In Progress, Completed)
- ✅ Priority Levels (Low, Medium, High)
- ✅ Due Date Tracking
- ✅ Overdue Task Detection
- ✅ Advanced Filtering & Search
- ✅ Sortable Columns
- ✅ Real-time Statistics Dashboard
- ✅ Responsive Design (Mobile-friendly)
- ✅ Comprehensive Testing (Feature & Unit Tests)
- ✅ RESTful API Support

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Authentication**: Laravel Jetstream
- **Database**: MySQL
- **Testing**: PHPUnit

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL

## Installation

### 1. Clone the repository
```bash
git clone <your-repo-url>
cd task-management-system
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install NPM dependencies
```bash
npm install
```

### 4. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Create database
```bash
mysql -u root -p
CREATE DATABASE task_management;
exit;
```

### 7. Run migrations
```bash
php artisan migrate
```

### 8. Seed database (optional)
```bash
php artisan db:seed --class=TaskSeeder
```

This creates a test user:
- Email: test@example.com
- Password: password

### 9. Build assets
```bash
npm run build
```

### 10. Start development server
```bash
php artisan serve
```

Visit: http://localhost:8000

## Testing

Run all tests:
```bash
php artisan test
```

Run specific test suite:
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

Run with coverage:
```bash
php artisan test --coverage
```

## API Documentation

### Authentication
All API endpoints require authentication using Laravel Sanctum.

### Endpoints

**Get all tasks**
```http
GET /api/tasks
```

Query parameters:
- `status`: pending|in_progress|completed
- `priority`: low|medium|high
- `search`: search term

**Create task**
```http
POST /api/tasks
Content-Type: application/json

{
  "title": "Task title",
  "description": "Task description",
  "status": "pending",
  "priority": "medium",
  "due_date": "2024-12-31"
}
```

**Get single task**
```http
GET /api/tasks/{id}
```

**Update task**
```http
PUT /api/tasks/{id}
Content-Type: application/json

{
  "title": "Updated title",
  "status": "completed"
}
```

**Delete task**
```http
DELETE /api/tasks/{id}
```

**Get statistics**
```http
GET /api/tasks-stats
```

## Project Structure

app/
├── Http/
│   └── Controllers/
│       └── Api/
│           └── TaskController.php
├── Livewire/
│   ├── TaskList.php
│   └── TaskForm.php
├── Models/
│   ├── Task.php
│   └── User.php
database/
├── factories/
│   └── TaskFactory.php
├── migrations/
│   └── xxxx_create_tasks_table.php
├── seeders/
│   └── TaskSeeder.php
resources/
├── views/
│   └── livewire/
│       ├── task-list.blade.php
│       └── task-form.blade.php
routes/
├── web.php
└── api.php
tests/
├── Feature/
│   └── TaskManagementTest.php
└── Unit/
└── TaskModelTest.php


## Key Design Decisions

### 1. **Livewire vs Traditional Controllers**
- Chose Livewire for reactive UI without complex JavaScript
- Provides SPA-like experience with minimal frontend code
- Better for rapid development

### 2. **Database Schema**
- Added indexes on frequently queried fields (user_id, status, due_date)
- Used ENUM for status and priority for data integrity
- Implemented soft deletes for data recovery

### 3. **Testing Strategy**
- Feature tests for user workflows
- Unit tests for model logic
- API tests for REST endpoints
- Coverage of edge cases (authorization, validation)

### 4. **Security**
- All routes protected with authentication middleware
- Authorization checks in Livewire components
- CSRF protection enabled
- SQL injection prevention via Eloquent ORM

## Future Enhancements

- [ ] Task categories/tags
- [ ] File attachments
- [ ] Task comments
- [ ] Team collaboration
- [ ] Email notifications
- [ ] Calendar view
- [ ] Export functionality (PDF, CSV)
- [ ] Task templates
- [ ] Recurring tasks

## License

This project is open-sourced software licensed under the MIT license.

## Author

Your Name - Full Stack Laravel Developer

## Support

For support, email your-email@example.com