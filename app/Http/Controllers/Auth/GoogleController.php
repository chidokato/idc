<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    // Bước 1: redirect user tới Google
    public function redirectToGoogle()
    {
        // Nếu muốn request thêm quyền (scope) hoặc offline access để lấy refresh token, có thể thêm ->with() / ->scopes()
        // Ví dụ: ->with(['access_type' => 'offline', 'prompt' => 'consent'])
        return Socialite::driver('google')->redirect();
    }

    // Bước 2: Google callback trả về thông tin user
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            // Ghi log nếu cần: \Log::error($e->getMessage());
            return redirect('/admin')->with('error', 'Không thể đăng nhập bằng Google: ' . $e->getMessage());
        }

        // Tìm user theo google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // Nếu có account cùng email nhưng chưa link google -> link vào
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'google_token' => isset($googleUser->token) ? encrypt($googleUser->token) : null,
                    'google_refresh_token' => isset($googleUser->refreshToken) ? encrypt($googleUser->refreshToken) : null,
                    'google_token_expires_at' => isset($googleUser->expiresIn) ? now()->addSeconds($googleUser->expiresIn) : null,
                ]);
            } else {
                // Tạo user mới
                $status = Str::endsWith($googleUser->getEmail(), '@dxmb.vn') ? 'active' : 'inactive';
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'User',
                    'email' => $googleUser->getEmail(),
                    'permission' => 6,
                    'rank' => 3,
                    'status' => $status, // tự động active nếu email @dxmb.vn
                    'password' => bcrypt(Str::random(24)), // user sẽ login bằng Google, password random
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'google_token' => isset($googleUser->token) ? encrypt($googleUser->token) : null,
                    'google_refresh_token' => isset($googleUser->refreshToken) ? encrypt($googleUser->refreshToken) : null,
                    'google_token_expires_at' => isset($googleUser->expiresIn) ? now()->addSeconds($googleUser->expiresIn) : null,
                ]);
            }
        } else {
            // Update token khi cần
            $user->update([
                'google_token' => isset($googleUser->token) ? encrypt($googleUser->token) : $user->google_token,
                'google_refresh_token' => isset($googleUser->refreshToken) ? encrypt($googleUser->refreshToken) : $user->google_refresh_token,
                'google_token_expires_at' => isset($googleUser->expiresIn) ? now()->addSeconds($googleUser->expiresIn) : $user->google_token_expires_at,
            ]);
        }

        // Login user trong Laravel
        // Auth::login($user, true);

        // Login user trong Laravel
        Auth::login($user, true);

        // Phân luồng theo permission
        if ($user->permission < 6) {
            // Admin
            return redirect()->route('admin'); // route admin
        } elseif ($user->permission == 6) {
            // User bình thường
            if ($user->status == 'active') {
                return redirect()->route('account.main'); // route admin hoặc route giao diện người dùng
            }
            else{
                Auth::logout();
                return redirect('dangnhap')->with('center_error', 'Bạn đã kết nối vào hệ thống thành công. Do hệ thống chỉ lưu hành nội bộ. Bạn cần liên hệ Admin để cấp quyền truy cập cao hơn ! Admin: 0977572947');
            }
        } else {
            // Nếu permission > 6, phòng trường hợp không hợp lệ
            Auth::logout();
            return redirect('dangnhap')->with('error', 'Tài khoản không hợp lệ');
        }
        // 
        
    }
}
