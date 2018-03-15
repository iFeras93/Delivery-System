<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() == null)
            return redirect()->back()->with(['status' => 'You Are Not User.']);

        if ($request->user()->type != "admin")
            return redirect()->back()->with(['status' => 'You Do not Administrator Role.']);
        return $next($request);
    }
}
