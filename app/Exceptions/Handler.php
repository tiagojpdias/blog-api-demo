<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        return parent::render($request, $exception);
    }
}
