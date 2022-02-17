<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$args)
    {
        $admin = ($request->session()->get('user')->is_admin == 1) ? 'admin' : 'user';
        if (in_array($admin, $args)) {
            return $next($request);
        }

        return response()->json("You don\'t have access privilages", 401);
    }
}
