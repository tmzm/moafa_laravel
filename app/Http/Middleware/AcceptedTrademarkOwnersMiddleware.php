<?php

namespace App\Http\Middleware;

use App\Http\Helpers\MessageHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptedTrademarkOwnersMiddleware
{
    use MessageHelper;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->user()->accepted_by_admin) {
            return $this->unAuth();
        }
        return $next($request);
    }
}
