<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class IsamsRequestException extends Exception
{
    public function render($request): Response
    {
        libxml_use_internal_errors(true);

        $message = $this->getMessage();
        $xml = simplexml_load_string($message);
        $err = libxml_get_last_error();

        if ($err->message === "Document labelled UTF-16 but has UTF-8 content\n") {
            libxml_clear_errors();
            $message = str_replace("utf-16", "UTF-8", $message);
            $xml = simplexml_load_string($message);
        }
        if (libxml_get_last_error()) {
            return response()->view('selfreflection.errors.isamsrequest', ['message' => $this->getMessage(), 'xml' => null]);
        }
        $json = json_encode($xml);
        $array = json_decode($json, true);
        return response()->view('selfreflection.errors.isamsrequest', ['message' => null, 'xml' => $array]);
    }
}
