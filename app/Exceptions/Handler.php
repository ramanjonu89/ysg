<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        if ($e instanceof AuthenticationException) {
            if ($request->expectsJson() || $request->is('api/*') || $request->is('mobile/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'data' => []
                ], 401);
            }
        }

        if (
            $e instanceof ModelNotFoundException &&
            $request->wantsJson()
        ) {
            return response()->json([
                'message' => "Resource not found"
            ], 404);
        }
        return parent::render($request, $e);
    }
}

