<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TaskRepositoryInterface;
use App\Repositories\TaskRepository;
use App\Http\Middleware\RoleMiddleware;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register()
    {
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            \Illuminate\Support\Facades\Route::aliasMiddleware('role', RoleMiddleware::class);
    }
}
