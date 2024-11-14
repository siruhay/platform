<?php

namespace Monoland\Platform\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Module\System\Models\SystemUser;
use Symfony\Component\HttpFoundation\Response;

class Impersonate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession() && $request->session()->has('impersonate_source_id')) {
            Auth::setUser(
                SystemUser::find($request->session()->get('impersonate_source_id'))
            );
        }

        return $next($request);
    }
}
