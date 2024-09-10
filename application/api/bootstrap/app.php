<?php

use App\Helpers\JsonResponseHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware()
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $e) {
            $body = match (get_class($e)) {
                ValidationException::class => [
                    collect($e?->errors() ?? [])->flatten()->toArray(),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                ],
                default => [[$e->getMessage()]],
            };

            return JsonResponseHelper::error(...$body);
        });
    })->create();
