<?php

namespace App\Providers;

use App\Repositories\Event\EventRepository;
use App\Repositories\Event\EventRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Facades\Translatable;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            EventRepositoryInterface::class,
            EventRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Translatable::fallback(
            fallbackAny: true,
        );
    }
}
