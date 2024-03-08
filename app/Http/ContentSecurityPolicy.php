<?php

namespace App\Http;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic;

class ContentSecurityPolicy extends Basic
{
    public function configure()
    {
        parent::configure();
        $this
            ->addDirective(Directive::SCRIPT, 'self')
            ->addDirective(Directive::STYLE, 'self')
            ->addNonceForDirective(Directive::SCRIPT)
            ->addNonceForDirective(Directive::STYLE);

        $this->addDirective(Directive::SCRIPT, 'https://fonts.googleapis.com');
        $this->addDirective(Directive::SCRIPT, 'https://cdn.tiny.cloud');
    }
}
