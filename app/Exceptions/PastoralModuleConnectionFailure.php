<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PastoralModuleConnectionFailure extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): Response
    {
        return response()->view('selfreflection.errors.pastoralconnection')->setStatusCode($this->code, 'Pastoral Module Connection Failure');
    }
}
