<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CKFinderMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('login');
        });
        $middleware->alias([
            'ckfinder' => CKFinderMiddleware::class,
        ]);
        $middleware->encryptCookies(except: [
            'ckCsrfToken',
        ]);
        $middleware->validateCsrfTokens(
            except: ['ckfinder/*']
        );

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
