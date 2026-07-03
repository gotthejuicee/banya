<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Шаред-хостинг ховає сайт за реверс-проксі: без цього throttle
        // та IP у заявках бачили б адресу проксі, а не відвідувача.
        $middleware->trustProxies(at: '*');

        // Приватна аналітика відвідувачів (без кук і сторонніх сервісів)
        $middleware->web(append: [
            \App\Http\Middleware\TrackVisits::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // JSON-відповіді про помилки й для AJAX-заявок з лендингу (fetch шле Accept: application/json)
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
