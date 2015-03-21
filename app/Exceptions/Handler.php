<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Inertia\Inertia;

class Handler extends ExceptionHandler
{
    /**
     * @inheritDoc
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (
            !app()->environment(['local', 'testing'])
            && in_array($response->status(), [403, 404, 500, 503])
        ) {
            return Inertia::render('Error', ['status' => $response->status()])
                ->toResponse($request)
                ->setStatusCode($response->status());
        }

        if ($response->status() === 419) {
            return back()->with([
                'message' => 'The page expired, please try again.',
            ]);
        }

        return $response;
    }
}
