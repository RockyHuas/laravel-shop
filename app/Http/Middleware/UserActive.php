<?php

namespace App\Http\Middleware;

use Closure;

class UserActive
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
        $user = \Auth::user();
        throw_on($user && !$user->status, '请您耐心等待管理员审核', 403);
        return $next($request);
    }
}
