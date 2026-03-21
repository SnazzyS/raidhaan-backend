<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $renderUnauthorized = function (string $message, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            return redirect('/')
                ->with('error', $message);
        };

        $exceptions->render(function (AuthorizationException $exception, Request $request) use ($renderUnauthorized) {
            return $renderUnauthorized($exception->getMessage() ?: 'Admin access required.', $request);
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) use ($renderUnauthorized) {
            return $renderUnauthorized('Admin access required.', $request);
        });
    })->create();

if ($storagePath = env('APP_STORAGE_PATH')) {
    $app->useStoragePath($storagePath);
}

return $app;
