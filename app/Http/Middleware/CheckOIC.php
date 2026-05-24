<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOIC
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is an OIC
        if (Auth::check() && Auth::user()->is_oic) {
            return $next($request);
        }

        // Redirect to home with error message
        return redirect()->route('home')->with('error', 'You are not authorized to access this page. Only OIC users are allowed.');
    }
}