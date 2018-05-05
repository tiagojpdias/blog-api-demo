<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * {@inheritdoc}
     */
    protected $dontReport = [
        //
    ];

    /**
     * {@inheritdoc}
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * {@inheritdoc}
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof HttpException) {
            return response()->httpError($exception);
        }

        if ($exception instanceof ValidationException) {
            return response()->validationError($exception);
        }

        if ($exception instanceof JWTException) {
            return response()->exceptionError($exception, 400);
        }

        return parent::render($request, $exception);
    }
}
