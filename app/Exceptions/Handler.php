<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception)) {
            $statusCode = $exception->getStatusCode();

            // Проверяем, существует ли кастомный шаблон для ошибки
            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [], $statusCode);
            }

            // Если шаблона нет, выводим общее сообщение
            return response()->view('errors.generic', ['message' => 'Ой, что-то пошло не так'], $statusCode);
        }

        // Для всех других исключений можно показать общее сообщение
        return response()->view('errors.generic', ['message' => 'Ой, что-то пошло не так'], 500);
    }
}
