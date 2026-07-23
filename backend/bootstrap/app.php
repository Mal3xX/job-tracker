<?php

declare(strict_types=1);

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(HandleCors::class);
        $middleware->prependToGroup('api', HandleCors::class);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });
        $exceptions->render(function (TokenMismatchException $e) {
            return response()->json(['message' => 'CSRF token mismatch.'], 419)
                ->header('Access-Control-Allow-Origin', env('FRONTEND_URL', 'http://localhost:5173'))
                ->header('Access-Control-Allow-Credentials', 'true');
        });
        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        });
        $exceptions->render(function (NotFoundHttpException $e) {
            return response()->json(['message' => 'Not found.'], 404);
        });
        $exceptions->render(function (AccessDeniedHttpException $e) {
            return response()->json(['message' => 'Forbidden.'], 403);
        });
        $exceptions->render(function (ThrottleRequestsException $e) {
            return response()->json(['message' => 'Too many requests.'], 429);
        });
        $exceptions->render(function (HttpException $e) {
            if ($e->getStatusCode() === 419) {
                return response()->json(['message' => 'CSRF token mismatch.'], 419)
                    ->header('Access-Control-Allow-Origin', env('FRONTEND_URL', 'http://localhost:5173'))
                    ->header('Access-Control-Allow-Credentials', 'true');
            }
            return null;
        });
    })->create();
