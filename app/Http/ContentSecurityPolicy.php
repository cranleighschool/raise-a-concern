<?php

namespace App\Http;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic;

class ContentSecurityPolicy extends Basic
{
    private function forUpPage()
    {
        $this->addDirective(Directive::SCRIPT, 'self');
        $this->addDirective(Directive::STYLE, 'self');
        $this->addDirective(Directive::STYLE, 'fonts.bunny.net');
        $this->addDirective(Directive::SCRIPT, 'cdn.tailwindcss.com');
    }
    public function configure()
    {
        parent::configure();
        if (app()->environment('production')) {
            if (request()->is('up')) {
                $this->forUpPage();
            }
            $this
                ->addDirective(Directive::SCRIPT, 'self')
                ->addDirective(Directive::STYLE, 'self')
                ->addNonceForDirective(Directive::SCRIPT)
                ->addNonceForDirective(Directive::STYLE);

            $this->addDirective(Directive::STYLE, 'fonts.googleapis.com');
            $this->addDirective(Directive::CONNECT, 'cdn.tiny.cloud');
            $this->addDirective(Directive::SCRIPT, 'cdn.tiny.cloud');
            $this->addDirective(Directive::STYLE, 'cdn.tiny.cloud');
            $this->addDirective(Directive::FONT, 'fonts.gstatic.com');
            $this->addDirective(Directive::FONT, 'data:');
            $this->addDirective(Directive::FONT, 'self');
            $this->addDirective(Directive::IMG, 'sp.tinymce.com');
            $this->addDirective(Directive::STYLE, 'rsms.me'); // for laravel health check page
        }
    }
}
