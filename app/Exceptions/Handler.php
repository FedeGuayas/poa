<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        return parent::render($request, $e);
    }

//    protected function convertExceptionToResponse(Exception $e)
//    {
//        $debug = config('app.debug', false);
//        if($debug) {
//            return response()->view('errors.500', [], 500);
//        }
//
//        return parent::convertExceptionToResponse($e);
//    }

//    protected function convertExceptionToResponse(Exception $e)
//    {
//        $debug = config('app.debug', false);
//        if($debug) {
//            $e = FlattenException::create($e);
//            $handler = new SymfonyExceptionHandler(config('app.debug'));
//            $decorated = $this->decorate($handler->getContent($e), $handler->getStylesheet($e));
//            return SymfonyResponse::create($decorated, $e->getStatusCode(), $e->getHeaders());
//        }
//        else{
//            return response()->view('errors.503');
//        }
//    }
}
