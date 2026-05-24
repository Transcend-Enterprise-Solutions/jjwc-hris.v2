<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrackAccountSwitching
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !session()->has('original_account_id')) {
            session(['original_account_id' => auth()->id()]);
        }

        return $next($request);
    }
}