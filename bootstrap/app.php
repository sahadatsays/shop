<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Middleware\EnsureAdminAuthenticated;
use App\Http\Middleware\EnsureAdminPermission;
use App\Http\Middleware\EnsureCustomerAuthenticated;
use App\Http\Middleware\EnsureOrderTrackable;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(function (): void {
                    Route::middleware('guest:admin')->group(function (): void {
                        Route::get('login', [AuthController::class, 'create'])->name('login');
                        Route::post('login', [AuthController::class, 'store'])->name('login.store');
                    });

                    Route::middleware('admin.auth')->group(base_path('routes/admin.php'));
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => EnsureAdminAuthenticated::class,
            'admin.permission' => EnsureAdminPermission::class,
            'customer.auth' => EnsureCustomerAuthenticated::class,
            'order.tracking' => EnsureOrderTrackable::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request): ?string => $request->is('admin', 'admin/*')
            ? route('admin.login')
            : null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
