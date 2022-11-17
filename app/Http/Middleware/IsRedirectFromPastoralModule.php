<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsRedirectFromPastoralModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('from') && $request->get('from')==='pm') {
            session()->put('from-pastoral-alert', 'The Pastoral Module is only accessible from within the Cranleigh network. However if you wish to raise a concern you can now do this following the instructions below.');
        }
        return $next($request);
    }
}
