<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Users;
use Symfony\Component\HttpFoundation\Response;

class Authentication
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

        if (!empty($userCode)) {

            return redirect()->route('system.index');
        }

        return $next($request);
    }
}
