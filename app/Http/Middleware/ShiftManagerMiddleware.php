<?php

namespace App\Http\Middleware;

use Closure;

class ShiftManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->roles->pluck('name')->contains('shift_manager')) {
            return $next($request);
        }
        return back()->with('error', 'Unauthorised');
    }
}
