<?php

namespace App\Http;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Presets\Basic;

class ContentSecurityPolicy extends Basic
{
    public function configure(Policy $policy): void
    {
        parent::configure($policy);
        if (app()->environment('production')) {
            //            $policy
            //                ->add(Directive::SCRIPT, 'self')
            //                ->add(Directive::STYLE, 'self')
            //                ->addNonce(Directive::SCRIPT)
            //                ->addNonce(Directive::STYLE);

            $policy->add(Directive::STYLE, 'fonts.googleapis.com')
                ->add(Directive::CONNECT, 'cdn.tiny.cloud')
                ->add(Directive::SCRIPT, 'cdn.tiny.cloud')
                ->add(Directive::STYLE, 'cdn.tiny.cloud')
                ->add(Directive::FONT, 'fonts.gstatic.com')
                ->add(Directive::FONT, 'data:')
                // ->add(Directive::FONT, 'self')
                ->add(Directive::SCRIPT, Keyword::UNSAFE_INLINE)
                ->add(Directive::IMG, 'sp.tinymce.com')
                ->add(Directive::IMG, 'www.cranleigh.org')
                ->add(Directive::STYLE, 'rsms.me'); // for laravel health check page
        }
    }
}
