<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', 'Bạn chưa đăng nhập');
        }

        $user = Auth::user();

        if ($user->permission == 1) {
            return $next($request);
        }

        return redirect()->route('account.main')->with('error', 'Bạn không có quyền vào trang admin');
    }
}
