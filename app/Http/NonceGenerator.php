<?php

namespace App\Http;

use Illuminate\Support\Facades\Vite;
use Spatie\Csp\Nonce\RandomString;

class NonceGenerator extends RandomString
{
    public function generate(): string
    {
        return Vite::cspNonce();
    }


}
