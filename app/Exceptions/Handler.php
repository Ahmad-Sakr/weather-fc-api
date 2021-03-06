<?php

namespace App\Exceptions;

use App\Traits\ApiResponder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponder;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

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

    public function render($request, Throwable $e)
    {
        if ($request->wantsJson()) {
            //Validation Exception
            if ($e instanceof ValidationException) {
                return $this->error('Validation failure.',Response::HTTP_UNPROCESSABLE_ENTITY, [
                    'errors' => $e->errors()
                ]);
            }

            //BadRequest Exception
            if ($e instanceof BadRequestException) {
                return $this->error($e->getMessage(),Response::HTTP_BAD_REQUEST);
            }

            //ModelNotFound Exception
            if ($e instanceof ModelNotFoundException) {
                return $this->error($e->getMessage(),Response::HTTP_NOT_FOUND);
            }
        }

        return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
