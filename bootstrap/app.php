<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(StartSession::class);
        $middleware->append(ShareErrorsFromSession::class);
        $middleware->statefulApi();
        $middleware->trustProxies([
            //ip 'xxx.xxx.x.x',
        ])->validateCsrfTokens([
            'log-viewer/*',
        ])->alias([]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    \App\Exceptions\Handler::class
);
    
return $app;