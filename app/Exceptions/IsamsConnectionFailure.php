<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsamsConnectionFailure extends Exception
{
    protected $code = 503; // Service Unavailable
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): Response
    {
        return response()->view('selfreflection.errors.isamsconnection')->setStatusCode($this->code, 'ISAMS Connection Failure');
    }
}
