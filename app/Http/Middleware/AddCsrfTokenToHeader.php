<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddCsrfTokenToHeader
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
        $response = $next($request);

        // Thêm CSRF Token vào header
        $response->headers->set('X-CSRF-Token', csrf_token());

        return $response;
    }
}
