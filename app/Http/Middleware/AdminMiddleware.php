<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next, $level = 1)
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', 'Bạn chưa đăng nhập');
        }

        $user = Auth::user();

        // kiểm tra quyền
        if ($user->permission <= $level) {
            return $next($request);
        }
        return redirect()->route('admin')->with('error', 'Bạn không có quyền vào trang này');
    }
}
