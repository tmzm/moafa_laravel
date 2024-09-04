<?php

namespace App\Http\Middleware;

use App\Http\Helpers\MessageHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedEmail
{
    use MessageHelper;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->email_verified_at == null) {
            return $this->unAuth();
        }
        
        return $next($request);
    }
}
