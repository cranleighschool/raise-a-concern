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

        $this->addDirective(Directive::STYLE, 'fonts.googleapis.com');
        $this->addDirective(Directive::SCRIPT, 'https://cdn.tiny.cloud');
        $this->addDirective(Directive::FONT, 'fonts.gstatic.com');
        $this->addDirective(Directive::FONT, 'data:');
        $this->addDirective(Directive::FONT, 'self');
        $this->addDirective(Directive::IMG, 'https://sp.tinymce.com');
    }
}
