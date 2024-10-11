<?php

namespace App\Http\Middleware;

use App\Models\Users;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userCode = session('user_code');

        $user = Users::where('code', $userCode)
            ->where('status', 0)
            ->onlyTrashed()
            ->first();

        if ($userCode && $user) {

            session()->forget(['user_code', 'isAdmin']);

            return redirect()->route('home');
        }

        if (empty(session('user_code'))) {

            return redirect()->route('home');
        }

        return $next($request);
    }
}
