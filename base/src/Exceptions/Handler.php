<?php

namespace Polirium\Core\Base\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected function getHttpExceptionView(HttpExceptionInterface $e)
    {
        $noneView = 'core/base::error.none';
        $view = 'core/base::error.' . $e->getStatusCode();

        if (view()->exists($view)) {
            return $view;
        }

        return $noneView;
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return redirect()->guest(route('login'));
    }
}
