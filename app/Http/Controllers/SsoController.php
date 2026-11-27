<?php

namespace App\Http\Controllers;

use App\Models\SsoApp;
use App\Models\User;
use App\Services\SsoAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SsoController extends Controller
{
    private SsoAuthService $ssoService;

    public function __construct(SsoAuthService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    public function showLogin(Request $request)
    {
        $appKey = $this->resolveAppKey($request->query('app'));
        if ($appKey) {
            session(['sso_pending_app' => $appKey]);
        } else {
            session()->forget('sso_pending_app');
        }

        if ($request->filled('redirect')) {
            session(['sso_pending_redirect' => $request->query('redirect')]);
        }

        // Already logged into SSO portal
        if (session()->has('sso_employee_code')) {
            $profile = DB::table('sso_profiles')->where('employee_code', session('sso_employee_code'))->first();
            if ($profile) {
                if (!$profile->standardized_at) {
                    return redirect()->route('sso.profile');
                }
                if ($appKey) {
                    return $this->redirectToApp($appKey, $profile);
                }
                return redirect()->route('sso.apps');
            }
        }

        $targetApp = $appKey ? SsoApp::findActiveByKey($appKey) : null;
        return view('sso.login', compact('targetApp', 'appKey'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'Vui lòng nhập Email, Số điện thoại hoặc Mã nhân viên.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if ($request->filled('app')) {
            $appKey = $this->resolveAppKey($request->input('app'));
            if ($appKey) {
                session(['sso_pending_app' => $appKey]);
            }
        }

        $result = $this->ssoService->attemptMultiSystemLogin(
            $request->input('identifier'),
            $request->input('password')
        );

        if (!$result['ok']) {
            return back()
                ->withErrors(['identifier' => $result['error']])
                ->withInput($request->only('identifier', 'app'));
        }

        $profile = $result['profile'];
        session([
            'sso_employee_code' => $profile->employee_code,
            'sso_hcc_user_id' => $result['hcc_user']->id ?? null,
            'sso_matched_source' => $result['matched_source'],
        ]);

        // Always direct to profile standardization page on login if not yet standardized,
        // or if user hasn't confirmed their unified email/phone yet.
        if (!$profile->standardized_at) {
            return redirect()->route('sso.profile')
                ->with('info', 'Vui lòng kiểm tra và chuẩn hóa thông tin Email / Số điện thoại dùng chung cho toàn hệ thống.');
        }

        $pendingApp = session()->pull('sso_pending_app');
        if ($pendingApp) {
            return $this->redirectToApp($pendingApp, $profile);
        }

        return redirect()->route('sso.apps');
    }

    public function redirectToGoogle(Request $request)
    {
        if ($request->filled('app')) {
            $appKey = $this->resolveAppKey($request->query('app'));
            if ($appKey) {
                session(['sso_pending_app' => $appKey]);
            }
        }

        return Socialite::driver('google')
            ->redirectUrl(url('/auth/google/callback'))
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(url('/auth/google/callback'))
                ->user();
        } catch (Throwable $e) {
            return redirect()->route('sso.login')
                ->withErrors(['identifier' => 'Đăng nhập Google thất bại hoặc đã bị hủy. Vui lòng thử lại.']);
        }

        $result = $this->ssoService->attemptGoogleLogin(
            (string) $googleUser->getEmail(),
            $googleUser->getId()
        );

        if (!$result['ok']) {
            return redirect()->route('sso.login')
                ->withErrors(['identifier' => $result['error']]);
        }

        $profile = $result['profile'];
        session([
            'sso_employee_code' => $profile->employee_code,
            'sso_hcc_user_id' => $result['hcc_user']->id ?? null,
            'sso_matched_source' => 'google',
        ]);

        if (!$profile->standardized_at) {
            return redirect()->route('sso.profile')
                ->with('info', 'Vui lòng kiểm tra và chuẩn hóa thông tin Email / Số điện thoại dùng chung cho toàn hệ thống.');
        }

        $pendingApp = session()->pull('sso_pending_app');
        if ($pendingApp) {
            return $this->redirectToApp($pendingApp, $profile);
        }

        return redirect()->route('sso.apps');
    }

    public function showProfile()
    {
        $employeeCode = session('sso_employee_code');
        if (!$employeeCode) {
            return redirect()->route('sso.login');
        }

        $profile = DB::table('sso_profiles')->where('employee_code', $employeeCode)->first();
        if (!$profile) {
            session()->forget('sso_employee_code');
            return redirect()->route('sso.login');
        }

        $extraEmails = json_decode($profile->extra_emails ?? '[]', true) ?: [];
        $extraPhones = json_decode($profile->extra_phones ?? '[]', true) ?: [];
        $pendingAppKey = session('sso_pending_app');
        $pendingApp = $pendingAppKey ? SsoApp::findActiveByKey($pendingAppKey) : null;

        return view('sso.profile', compact('profile', 'extraEmails', 'extraPhones', 'pendingApp'));
    }

    public function updateProfile(Request $request)
    {
        $employeeCode = session('sso_employee_code');
        if (!$employeeCode) {
            return redirect()->route('sso.login');
        }

        $request->validate([
            'primary_email' => 'required|email|max:255',
            'primary_phone' => 'required|string|max:30',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'primary_email.required' => 'Email chính không được để trống.',
            'primary_email.email' => 'Email chính không đúng định dạng.',
            'primary_phone.required' => 'Số điện thoại chính không được để trống.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới để đồng bộ toàn hệ thống.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $profile = $this->ssoService->saveStandardizedProfile(
            $employeeCode,
            trim($request->input('primary_email')),
            [],
            trim($request->input('primary_phone')),
            [],
            $request->input('new_password')
        );

        $pendingApp = session()->pull('sso_pending_app');
        if ($pendingApp) {
            return $this->redirectToApp($pendingApp, $profile);
        }

        return redirect()->route('sso.apps')
            ->with('success', 'Đã chuẩn hóa và đồng bộ thông tin đăng nhập thành công!');
    }

    public function apps()
    {
        $employeeCode = session('sso_employee_code');
        if (!$employeeCode) {
            return redirect()->route('sso.login');
        }

        $profile = DB::table('sso_profiles')->where('employee_code', $employeeCode)->first();
        if (!$profile) {
            session()->forget('sso_employee_code');
            return redirect()->route('sso.login');
        }

        $apps = SsoApp::getActiveApps();
        return view('sso.apps', compact('profile', 'apps'));
    }

    public function launch(string $app)
    {
        $employeeCode = session('sso_employee_code');
        if (!$employeeCode) {
            return redirect()->route('sso.login', ['app' => $app]);
        }

        $appKey = $this->resolveAppKey($app);
        if (!$appKey) {
            abort(404, 'Ứng dụng không tồn tại trong hệ thống.');
        }

        $profile = DB::table('sso_profiles')->where('employee_code', $employeeCode)->first();
        if (!$profile) {
            return redirect()->route('sso.login');
        }
        return $this->redirectToApp($appKey, $profile);
    }

    public function localLogin(Request $request)
    {
        $payload = $this->ssoService->verifySignedPayload(
            (string) $request->query('payload'),
            (string) $request->query('signature')
        );

        abort_unless($payload, 403, 'Token SSO không hợp lệ hoặc đã hết hạn.');

        $user = User::where('employee_code', $payload['employee_code'])->first();
        if (!$user && !empty($payload['email'])) {
            $user = User::where('email', $payload['email'])->first();
        }

        if (!$user) {
            $user = new User();
            $user->employee_code = $payload['employee_code'];
            $user->name = $payload['name'];
            $user->yourname = $payload['name'];
            $user->email = $payload['email'] ?: ($payload['employee_code'] . '@sso.local');
            $user->phone = $payload['phone'] ?? null;
            $user->password = Hash::make(Str::random(24));
            $user->permission = (int) ($payload['permission'] ?? 6);
            $user->status = 'active';
            $user->save();
        } else {
            $user->employee_code = $payload['employee_code'];
            if (!empty($payload['email'])) {
                $emailLower = strtolower($payload['email']);
                if (!User::where('email', $emailLower)->where('id', '!=', $user->id)->exists()) {
                    $user->email = $emailLower;
                }
            }
            if (!empty($payload['phone'])) {
                $user->phone = $payload['phone'];
            }
            $user->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if ((int) $user->permission < 6) {
            return redirect()->route('admin');
        }
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        session()->forget(['sso_employee_code', 'sso_hcc_user_id', 'sso_matched_source', 'sso_pending_app', 'sso_pending_redirect']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất khỏi hệ thống.');
    }

    private function resolveAppKey(?string $app): ?string
    {
        if (!$app) {
            return null;
        }
        $app = strtolower(trim($app));
        return array_key_exists($app, SsoApp::getActiveApps()) ? $app : null;
    }

    private function redirectToApp(string $appKey, object $profile)
    {
        $appConfig = SsoApp::findActiveByKey($appKey);
        abort_unless($appConfig, 404, 'Ứng dụng không tồn tại hoặc đang tạm khóa.');
        $hccUser = session('sso_hcc_user_id')
            ? DB::connection('izi')->table('users')->find(session('sso_hcc_user_id'))
            : null;

        $url = $this->ssoService->buildSignedCallbackUrl($appConfig, $profile, $hccUser);
        return redirect()->away($url);
    }
}
