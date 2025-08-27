<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
//    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware(['api','force_json'])
                ->prefix('api/v1')
                ->group(base_path('routes/V1/api.php'));
            Route::middleware(['api','force_json'])
                ->prefix('api/v1/admin')
                ->name('Admin.')
                ->group(base_path('Modules/Admin/routes/V1/api.php'));
            Route::middleware(['api','force_json'])
                ->prefix('api/v1/lk')
                ->name('lk.')
                ->group(base_path('Modules/Lk/routes/V1/api.php'));
            Route::middleware(['api','force_json'])
                ->prefix('api/v1/broadcast')
                ->name('broadcast.')
                ->group(base_path('Modules/Broadcast/routes/V1/api.php'));
            Route::middleware(['api','force_json'])
                ->group(base_path('routes/web.php'));

            Route::fallback(function () {
                return response()->json([
                    'message' => 'Not Found.'
                ], 404);
            });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(180)->by($request->user()?->id ?: $request->ip());
        });
    }
}
