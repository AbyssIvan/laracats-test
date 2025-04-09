<?php

declare(strict_types=1);

namespace App\Exceptions;

use DomainException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use RuntimeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @psalm-var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        DomainException::class     => LogLevel::ERROR,
        RuntimeException::class    => LogLevel::CRITICAL,
        ValidationException::class => LogLevel::WARNING,
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @psalm-var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @psalm-var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register() : void
    {
        $this->renderable(function (DomainException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => $e->getMessage()], 409);
            }

            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        });

        $this->renderable(function (RecordsNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            Log::warning($e->getMessage(), $request->all());
            if ($request->is('api/*')) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        });

        $this->renderable(function (MultipleRecordsFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        });

        $this->renderable(function (RuntimeException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => $e->getMessage()], 500);
            }

            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        });

        $this->reportable(function (Throwable $e) {
        });
    }
}
