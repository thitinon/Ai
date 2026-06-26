<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Eloquent\EloquentCourseRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\EloquentEnrollmentRepository;
use App\Repositories\Eloquent\EloquentOrderRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CourseRepositoryInterface::class,
            EloquentCourseRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            EnrollmentRepositoryInterface::class,
            EloquentEnrollmentRepository::class
        );

        $this->app->bind(
            OrderRepositoryInterface::class,
            EloquentOrderRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
