# Clone the repository and navigate to project directory
git clone <repository_url>
cd <project_directory>

# Install Composer dependencies
docker-compose run --rm composer install --no-interaction --prefer-dist --optimize-autoloader

# Copy environment variables
cp .env.example .env

# Generate application key
docker-compose exec app php artisan key:generate

# Start Docker containers
docker-compose up -d

# Serve the Laravel application
docker exec -it laravel_app_todo php artisan serve --host=0.0.0.0 --port=8000

# Rebuild Docker containers after modifying Dockerfile
docker-compose down --volumes --remove-orphans
docker-compose build --no-cache
docker-compose up -d

# Clear Cache and Configurations
docker exec -it laravel_app_todo php artisan config:clear
docker exec -it laravel_app_todo php artisan route:clear
docker exec -it laravel_app_todo php artisan cache:clear
docker exec -it laravel_app_todo composer dump-autoload

# Run migrations
docker exec -it laravel_app_todo php artisan migrate

# Fresh migrations and seed
docker exec -it laravel_app_todo php artisan migrate:fresh --seed

# Install JWT Authentication package
docker exec -it laravel_app_todo composer require tymon/jwt-auth

# Publish JWT config
docker exec -it laravel_app_todo php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

# Generate JWT secret
docker exec -it laravel_app_todo php artisan jwt:secret

# User roles and migrations
docker exec -it laravel_app_todo php artisan make:migration create_roles_table
docker exec -it laravel_app_todo php artisan make:migration create_role_user_table
docker exec -it laravel_app_todo php artisan migrate
docker exec -it laravel_app_todo php artisan make:model Role
docker exec -it laravel_app_todo php artisan make:seeder RoleSeeder
docker exec -it laravel_app_todo php artisan make:seeder UserSeeder
docker exec -it laravel_app_todo php artisan db:seed

# Create categories, priorities, and tasks tables
docker exec -it laravel_app_todo php artisan make:migration create_categories_table --create=categories
docker exec -it laravel_app_todo php artisan make:migration create_priorities_table --create=priorities
docker exec -it laravel_app_todo php artisan make:migration create_tasks_table --create=tasks

# Create seeder files for categories, priorities, and tasks
docker exec -it laravel_app_todo php artisan make:seeder CategorySeeder
docker exec -it laravel_app_todo php artisan make:seeder PrioritySeeder
docker exec -it laravel_app_todo php artisan make:seeder TaskSeeder

# Create models for categories, priorities, and tasks
docker exec -it laravel_app_todo php artisan make:model Category
docker exec -it laravel_app_todo php artisan make:model Priority
docker exec -it laravel_app_todo php artisan make:model Task

# Migrate and seed fresh database
docker exec -it laravel_app_todo php artisan migrate:fresh --seed

# Create controllers
docker exec -it laravel_app_todo php artisan make:controller Auth/RegisterController
docker exec -it laravel_app_todo php artisan make:controller Auth/LoginController
docker exec -it laravel_app_todo php artisan make:controller TaskController

# Create directories for repositories, DTOs, services, and interfaces
docker exec -it laravel_app_todo mkdir -p app/Repositories app/DTOs app/Services app/Interfaces

# Create Task resource
docker exec -it laravel_app_todo php artisan make:resource TaskResource

# Migrate database again
docker exec -it laravel_app_todo php artisan migrate

# Seed the database
docker exec -it laravel_app_todo php artisan db:seed

# Edit auth.php for authentication configurations
docker exec -it laravel_app_todo vi /var/www/config/auth.php

# Start a bash session inside the container
docker exec -it laravel_app_todo bash

# Create Role middleware
docker exec -it laravel_app_todo php artisan make:middleware RoleMiddleware

# Create tests
docker exec -it laravel_app_todo php artisan make:test Http/Controllers/TaskControllerTest --unit
docker exec -it laravel_app_todo php artisan make:test Http/Controllers/TaskControllerTest

# Run all tests
docker exec -it laravel_app_todo php artisan test

# Run only unit tests
docker exec -it laravel_app_todo php artisan test --testsuite=Unit

# Run only feature tests
docker exec -it laravel_app_todo php artisan test --testsuite=Feature

# Run specific test file
docker exec -it laravel_app_todo php artisan test tests/Feature/Http/Controllers/TaskControllerTest.php

# Create factories for tasks, priorities, and categories
docker exec -it laravel_app_todo php artisan make:factory TaskFactory --model=Task
docker exec -it laravel_app_todo php artisan make:factory PriorityFactory --model=Priority
docker exec -it laravel_app_todo php artisan make:factory CategoryFactory --model=Category

# Refresh migrations
docker exec -it laravel_app_todo php artisan migrate:refresh

# Clear optimization cache
docker exec -it laravel_app_todo php artisan optimize:clear

# Create request for registration
docker exec -it laravel_app_todo php artisan make:request RegisterRequest

# API Endpoints
# Tasks with filters
curl http://localhost:8000/api/tasks?status=pending&priority_id=3&sort_by=due_date&sort_direction=asc

# User registration
curl -X POST http://localhost:8000/api/register -d '{"name":"wael","email":"wael@gmail.com","password":"123456","password_confirmation":"123456"}'
