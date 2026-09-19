<?php

use App\Http\Middleware\EnsurePasswordChanged;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'password.changed' => EnsurePasswordChanged::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request): string {
            $guards = $request->route()?->gatherMiddleware() ?? [];
            foreach ($guards as $m) {
                if (str_starts_with((string) $m, 'auth:member')) {
                    return route('member.login');
                }
                if (str_starts_with((string) $m, 'auth:applicant')) {
                    return route('applicant.login');
                }
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            $status = $e->getStatusCode();
            $view = view()->exists("errors.{$status}") ? "errors.{$status}" : 'errors.generic';

            return response()->view($view, ['exception' => $e, 'status' => $status], $status);
        });
    })->create();
