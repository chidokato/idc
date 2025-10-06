<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', 'Bạn chưa đăng nhập');
        }

        $user = Auth::user();
        
        if ($user->permission == 2 || $user->permission == 1) {
            return $next($request);
        }

        return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền vào trang người dùng');
    }
}
