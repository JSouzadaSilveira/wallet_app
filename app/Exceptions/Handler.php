<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected function invalidJson($request, $exception) {
        if ($exception instanceof AuthenticationException) {
            return response()->json(['message' => 'unauthenticated.'], 401);
        }

        return parent::invalidJson($request, $exception);
    }

    public function render($request, Throwable $exception) {
        if ($exception instanceof AuthenticationException) {
            return response()->json(['message' => 'unauthenticated.'], 401);
        }

        return parent::render($request, $exception);
    }

}
