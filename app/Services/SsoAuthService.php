<?php

namespace App\Services;

use App\Models\SsoSharedTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class SsoAuthService
{
    public function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone);
        if ($digits === '') {
            return '';
        }
        if (Str::startsWith($digits, '84') && strlen($digits) >= 11) {
            return '0' . substr($digits, 2);
        }
        return $digits;
    }

    /**
     * Attempt login via Google OAuth email across all connected systems, then resolve canonical HCC employee.
     */
    public function attemptGoogleLogin(string $googleEmail, ?string $googleId = null): array
    {
        $lowerEmail = mb_strtolower(trim($googleEmail));
        if ($lowerEmail === '') {
            return [
                'ok' => false,
                'error' => 'Không lấy được địa chỉ email từ tài khoản Google.',
            ];
        }

        $employeeCode = null;

        // 0. Check standardized sso_profiles
        $ssoProfile = DB::table('sso_profiles')
            ->whereRaw('LOWER(primary_email) = ?', [$lowerEmail])
            ->first();
        if (!$ssoProfile) {
            foreach (DB::table('sso_profiles')->whereNotNull('extra_emails')->get() as $p) {
                $emails = array_map('mb_strtolower', json_decode($p->extra_emails ?? '[]', true) ?: []);
                if (in_array($lowerEmail, $emails, true)) {
                    $ssoProfile = $p;
                    break;
                }
            }
        }
        if ($ssoProfile) {
            $employeeCode = $ssoProfile->employee_code;
        }

        // 1. Check idc.users (email, secondary_email, google_id)
        if (!$employeeCode) {
            try {
                $idcUser = DB::connection('mysql')->table('users')
                    ->whereRaw('LOWER(email) = ?', [$lowerEmail])
                    ->orWhereRaw('LOWER(secondary_email) = ?', [$lowerEmail])
                    ->when($googleId, fn ($q) => $q->orWhere('google_id', $googleId))
                    ->first();
                if ($idcUser && !empty($idcUser->employee_code)) {
                    $employeeCode = $idcUser->employee_code;
                }
            } catch (Throwable $e) {
            }
        }

        // 2. Check learning.users
        if (!$employeeCode) {
            try {
                $learningUser = DB::connection('learning')->table('users')
                    ->whereRaw('LOWER(email) = ?', [$lowerEmail])
                    ->when($googleId, fn ($q) => $q->orWhere('google_id', $googleId))
                    ->first();
                if ($learningUser && !empty($learningUser->employee_code)) {
                    $employeeCode = $learningUser->employee_code;
                }
            } catch (Throwable $e) {
            }
        }

        $hccEmployee = $this->resolveHccEmployee($employeeCode, $lowerEmail, null);

        if (!$hccEmployee) {
            return [
                'ok' => false,
                'error' => "Email Google ({$googleEmail}) chưa được liên kết với Mã nhân viên nào trên hệ thống HCC.",
            ];
        }

        if (in_array($hccEmployee->status, ['resigned', 'inactive'], true)) {
            return [
                'ok' => false,
                'error' => 'Nhân sự này hiện không còn hoạt động trên hệ thống HCC.',
            ];
        }

        $hccUser = DB::connection('izi')->table('users')
            ->where('employee_id', $hccEmployee->id)
            ->first();

        if ($hccUser && isset($hccUser->is_active) && !$hccUser->is_active) {
            return [
                'ok' => false,
                'error' => 'Tài khoản HCC của bạn đang bị khóa. Vui lòng liên hệ quản trị viên.',
            ];
        }

        $profile = $this->getOrCreateSsoProfile($hccEmployee, $hccUser);

        return [
            'ok' => true,
            'profile' => $profile,
            'hcc_employee' => $hccEmployee,
            'hcc_user' => $hccUser,
            'matched_source' => 'google',
        ];
    }

    /**
     * Attempt login across all connected systems, then resolve canonical HCC employee.
     *
     * @return array{ok: bool, error?: string, profile?: object, hcc_employee?: object, hcc_user?: object|null, matched_source?: string}
     */
    public function attemptMultiSystemLogin(string $identifier, string $password): array
    {
        $identifier = trim($identifier);
        $normalizedPhone = $this->normalizePhone($identifier);
        $lowerIdentifier = mb_strtolower($identifier);

        $matched = null;
        $source = null;

        // 0. Check standardized sso_profiles first to resolve employee_code from any linked email/phone
        $ssoProfile = DB::table('sso_profiles')
            ->where('employee_code', $identifier)
            ->orWhereRaw('LOWER(primary_email) = ?', [$lowerIdentifier])
            ->orWhere('primary_phone', $identifier)
            ->when($normalizedPhone !== '', fn ($q) => $q->orWhere('primary_phone', $normalizedPhone))
            ->first();

        if (!$ssoProfile) {
            $allProfiles = DB::table('sso_profiles')
                ->whereNotNull('extra_emails')
                ->orWhereNotNull('extra_phones')
                ->get();
            foreach ($allProfiles as $p) {
                $emails = array_map('mb_strtolower', json_decode($p->extra_emails ?? '[]', true) ?: []);
                $phones = json_decode($p->extra_phones ?? '[]', true) ?: [];
                $normPhones = array_map([$this, 'normalizePhone'], $phones);
                if (in_array($lowerIdentifier, $emails, true) || ($normalizedPhone !== '' && in_array($normalizedPhone, $normPhones, true))) {
                    $ssoProfile = $p;
                    break;
                }
            }
        }

        // 1-5. Check all active shared tables configured in Admin (can_login = true)
        $sharedTables = SsoSharedTable::getActiveTables()->where('can_login', true);
        if ($sharedTables->isNotEmpty()) {
            foreach ($sharedTables as $tbl) {
                $matched = $this->findAndVerifyConfiguredTable(
                    $tbl,
                    $identifier,
                    $lowerIdentifier,
                    $normalizedPhone,
                    $password,
                    $ssoProfile->employee_code ?? null
                );
                if ($matched) {
                    $source = $tbl->system_key;
                    break;
                }
            }
        } else {
            // Fallback if sso_shared_tables has not been migrated yet
            $iziMatch = $this->findAndVerifyIzi($identifier, $lowerIdentifier, $normalizedPhone, $password, $ssoProfile->employee_code ?? null);
            if ($iziMatch) {
                $matched = $iziMatch;
                $source = 'izi';
            }
        }

        if (!$matched) {
            return [
                'ok' => false,
                'error' => 'Thông tin đăng nhập hoặc mật khẩu không chính xác.',
            ];
        }

        // Resolve canonical HCC Employee (izi.employees)
        $hccEmployee = $this->resolveHccEmployee(
            $matched['employee_code'] ?? ($ssoProfile->employee_code ?? null),
            $matched['email'] ?? null,
            $matched['phone'] ?? null
        );

        if (!$hccEmployee) {
            return [
                'ok' => false,
                'error' => 'Tài khoản của bạn chưa có Mã nhân viên trên hệ thống HCC. Vui lòng liên hệ nhân sự để cập nhật mã NV trước khi đăng nhập.',
            ];
        }

        if (in_array($hccEmployee->status, ['resigned', 'inactive'], true)) {
            return [
                'ok' => false,
                'error' => 'Nhân sự này hiện không còn hoạt động trên hệ thống HCC.',
            ];
        }

        $hccUser = DB::connection('izi')->table('users')
            ->where('employee_id', $hccEmployee->id)
            ->first();

        if ($hccUser && isset($hccUser->is_active) && !$hccUser->is_active) {
            return [
                'ok' => false,
                'error' => 'Tài khoản HCC của bạn đang bị khóa. Vui lòng liên hệ quản trị viên.',
            ];
        }

        $profile = $this->getOrCreateSsoProfile($hccEmployee, $hccUser);

        return [
            'ok' => true,
            'profile' => $profile,
            'hcc_employee' => $hccEmployee,
            'hcc_user' => $hccUser,
            'matched_source' => $source,
        ];
    }

    private function findAndVerifyConfiguredTable(
        SsoSharedTable $tbl,
        string $raw,
        string $lower,
        string $normPhone,
        string $password,
        ?string $hintCode
    ): ?array {
        if ($tbl->system_key === 'izi_users') {
            return $this->findAndVerifyIzi($raw, $lower, $normPhone, $password, $hintCode);
        }

        if (empty($tbl->password_col)) {
            return null;
        }

        try {
            $conn = $tbl->getDbConnection();
            $query = $conn->table($tbl->table_name);
            $query->where(function ($q) use ($tbl, $raw, $lower, $normPhone, $hintCode) {
                $hasCondition = false;
                if ($tbl->email_col) {
                    $q->whereRaw("LOWER({$tbl->email_col}) = ?", [$lower]);
                    $hasCondition = true;
                }
                if ($tbl->secondary_email_col) {
                    $hasCondition ? $q->orWhereRaw("LOWER({$tbl->secondary_email_col}) = ?", [$lower]) : $q->whereRaw("LOWER({$tbl->secondary_email_col}) = ?", [$lower]);
                    $hasCondition = true;
                }
                if ($tbl->username_col) {
                    $hasCondition ? $q->orWhere($tbl->username_col, $raw) : $q->where($tbl->username_col, $raw);
                    $hasCondition = true;
                }
                if ($tbl->phone_col) {
                    $hasCondition ? $q->orWhere($tbl->phone_col, $raw) : $q->where($tbl->phone_col, $raw);
                    if ($normPhone !== '') {
                        $q->orWhere($tbl->phone_col, $normPhone);
                    }
                    $hasCondition = true;
                }
                if ($tbl->secondary_phone_col) {
                    $hasCondition ? $q->orWhere($tbl->secondary_phone_col, $raw) : $q->where($tbl->secondary_phone_col, $raw);
                    if ($normPhone !== '') {
                        $q->orWhere($tbl->secondary_phone_col, $normPhone);
                    }
                    $hasCondition = true;
                }
                if ($tbl->employee_code_col) {
                    $hasCondition ? $q->orWhere($tbl->employee_code_col, $raw) : $q->where($tbl->employee_code_col, $raw);
                    if ($hintCode) {
                        $q->orWhere($tbl->employee_code_col, $hintCode);
                    }
                }
            });

            $pwCol = $tbl->password_col;
            foreach ($query->get() as $row) {
                if (!empty($row->{$pwCol}) && Hash::check($password, $row->{$pwCol})) {
                    return [
                        'employee_code' => $tbl->employee_code_col ? ($row->{$tbl->employee_code_col} ?? null) : null,
                        'email' => $tbl->email_col ? ($row->{$tbl->email_col} ?? null) : null,
                        'phone' => $tbl->phone_col ? ($row->{$tbl->phone_col} ?? null) : null,
                        'name' => $tbl->name_col ? ($row->{$tbl->name_col} ?? null) : null,
                    ];
                }
            }
        } catch (Throwable $e) {
            return null;
        }

        return null;
    }

    private function findAndVerifyIzi(string $raw, string $lower, string $normPhone, string $password, ?string $hintCode): ?array
    {
        try {
            $query = DB::connection('izi')->table('users as u')
                ->leftJoin('employees as e', 'u.employee_id', '=', 'e.id')
                ->select('u.*', 'e.employee_code', 'e.email as emp_email', 'e.phone as emp_phone', 'e.name as emp_name');

            $query->where(function ($q) use ($raw, $lower, $normPhone, $hintCode) {
                $q->whereRaw('LOWER(u.email) = ?', [$lower])
                    ->orWhere('u.phone', $raw)
                    ->orWhere('u.secondary_phone', $raw)
                    ->orWhere('u.whatsapp_phone', $raw)
                    ->orWhere('e.employee_code', $raw)
                    ->orWhereRaw('LOWER(e.email) = ?', [$lower])
                    ->orWhere('e.phone', $raw);
                if ($normPhone !== '') {
                    $q->orWhere('u.phone', $normPhone)
                        ->orWhere('u.secondary_phone', $normPhone)
                        ->orWhere('e.phone', $normPhone);
                }
                if ($hintCode) {
                    $q->orWhere('e.employee_code', $hintCode);
                }
            });

            foreach ($query->get() as $row) {
                if (!empty($row->password) && Hash::check($password, $row->password)) {
                    return [
                        'employee_code' => $row->employee_code,
                        'email' => $row->emp_email ?: $row->email,
                        'phone' => $row->emp_phone ?: $row->phone,
                        'name' => $row->emp_name ?: $row->name,
                    ];
                }
            }
        } catch (Throwable $e) {
            return null;
        }
        return null;
    }

    private function findAndVerifyIdc(string $raw, string $lower, string $normPhone, string $password, ?string $hintCode): ?array
    {
        try {
            $query = DB::connection('mysql')->table('users')
                ->where(function ($q) use ($raw, $lower, $normPhone, $hintCode) {
                    $q->whereRaw('LOWER(email) = ?', [$lower])
                        ->orWhereRaw('LOWER(secondary_email) = ?', [$lower])
                        ->orWhere('phone', $raw)
                        ->orWhere('employee_code', $raw);
                    if ($normPhone !== '') {
                        $q->orWhere('phone', $normPhone);
                    }
                    if ($hintCode) {
                        $q->orWhere('employee_code', $hintCode);
                    }
                });

            foreach ($query->get() as $row) {
                if (!empty($row->password) && Hash::check($password, $row->password)) {
                    return [
                        'employee_code' => $row->employee_code,
                        'email' => $row->email,
                        'phone' => $row->phone,
                        'name' => $row->yourname ?: $row->name,
                    ];
                }
            }
        } catch (Throwable $e) {
            return null;
        }
        return null;
    }

    private function findAndVerifyLearning(string $raw, string $lower, string $normPhone, string $password, ?string $hintCode): ?array
    {
        try {
            $query = DB::connection('learning')->table('users')
                ->where(function ($q) use ($raw, $lower, $normPhone, $hintCode) {
                    $q->whereRaw('LOWER(email) = ?', [$lower])
                        ->orWhere('username', $raw)
                        ->orWhere('phone', $raw)
                        ->orWhere('secondary_phone', $raw)
                        ->orWhere('whatsapp_phone', $raw)
                        ->orWhere('employee_code', $raw);
                    if ($normPhone !== '') {
                        $q->orWhere('phone', $normPhone)
                            ->orWhere('secondary_phone', $normPhone);
                    }
                    if ($hintCode) {
                        $q->orWhere('employee_code', $hintCode);
                    }
                });

            foreach ($query->get() as $row) {
                if (!empty($row->password) && Hash::check($password, $row->password)) {
                    return [
                        'employee_code' => $row->employee_code,
                        'email' => $row->email,
                        'phone' => $row->phone,
                        'name' => $row->name,
                    ];
                }
            }
        } catch (Throwable $e) {
            return null;
        }
        return null;
    }

    private function findAndVerifyCrm(string $raw, string $lower, string $normPhone, string $password): ?array
    {
        try {
            $query = DB::connection('bds_crm')->table('users')
                ->where(function ($q) use ($raw, $lower, $normPhone) {
                    $q->whereRaw('LOWER(email) = ?', [$lower])
                        ->orWhere('phone', $raw);
                    if ($normPhone !== '') {
                        $q->orWhere('phone', $normPhone);
                    }
                });

            foreach ($query->get() as $row) {
                if (!empty($row->password_hash) && Hash::check($password, $row->password_hash)) {
                    return [
                        'employee_code' => $row->employee_code ?? null,
                        'email' => $row->email,
                        'phone' => $row->phone,
                        'name' => $row->full_name,
                    ];
                }
            }
        } catch (Throwable $e) {
            return null;
        }
        return null;
    }

    private function findAndVerifyIdcTool(string $raw, string $lower, string $normPhone, string $password): ?array
    {
        try {
            $query = DB::connection('idc_tool')->table('users')
                ->where(function ($q) use ($raw, $lower, $normPhone) {
                    $q->whereRaw('LOWER(email) = ?', [$lower])
                        ->orWhere('phone', $raw);
                    if ($normPhone !== '') {
                        $q->orWhere('phone', $normPhone);
                    }
                });

            foreach ($query->get() as $row) {
                if (!empty($row->password) && Hash::check($password, $row->password)) {
                    return [
                        'employee_code' => $row->employee_code ?? null,
                        'email' => $row->email,
                        'phone' => $row->phone,
                        'name' => $row->name,
                    ];
                }
            }
        } catch (Throwable $e) {
            return null;
        }
        return null;
    }

    public function resolveHccEmployee(?string $employeeCode, ?string $email, ?string $phone): ?object
    {
        $db = DB::connection('izi');

        if (!empty($employeeCode)) {
            $emp = $db->table('employees')->where('employee_code', trim($employeeCode))->whereNull('deleted_at')->first();
            if ($emp) {
                return $emp;
            }
        }

        if (!empty($email)) {
            $lower = mb_strtolower(trim($email));
            $emp = $db->table('employees')->whereRaw('LOWER(email) = ?', [$lower])->whereNull('deleted_at')->first();
            if ($emp) {
                return $emp;
            }
            $user = $db->table('users')->whereRaw('LOWER(email) = ?', [$lower])->first();
            if ($user && $user->employee_id) {
                $emp = $db->table('employees')->where('id', $user->employee_id)->whereNull('deleted_at')->first();
                if ($emp) {
                    return $emp;
                }
            }
        }

        if (!empty($phone)) {
            $norm = $this->normalizePhone($phone);
            $emp = $db->table('employees')
                ->where(function ($q) use ($phone, $norm) {
                    $q->where('phone', $phone);
                    if ($norm !== '') {
                        $q->orWhere('phone', $norm);
                    }
                })
                ->whereNull('deleted_at')
                ->first();
            if ($emp) {
                return $emp;
            }
        }

        return null;
    }

    public function getOrCreateSsoProfile(object $hccEmployee, ?object $hccUser): object
    {
        $existing = DB::table('sso_profiles')->where('employee_code', $hccEmployee->employee_code)->first();
        if ($existing) {
            return $existing;
        }

        $primaryEmail = $hccEmployee->email ?: ($hccUser->email ?? null);
        $primaryPhone = $hccEmployee->phone ?: ($hccUser->phone ?? null);

        $extraEmails = [];
        if ($hccUser && !empty($hccUser->email) && mb_strtolower($hccUser->email) !== mb_strtolower((string) $primaryEmail)) {
            $extraEmails[] = $hccUser->email;
        }

        // Gather secondary_email from idc.users if available
        try {
            $idcUser = DB::connection('mysql')->table('users')->where('employee_code', $hccEmployee->employee_code)->first();
            if ($idcUser && !empty($idcUser->secondary_email) && mb_strtolower($idcUser->secondary_email) !== mb_strtolower((string) $primaryEmail)) {
                $extraEmails[] = $idcUser->secondary_email;
            }
        } catch (Throwable $e) {
        }

        $extraPhones = [];
        if ($hccUser) {
            foreach ([$hccUser->secondary_phone ?? null, $hccUser->whatsapp_phone ?? null] as $p) {
                if (!empty($p) && $p !== $primaryPhone && !in_array($p, $extraPhones, true)) {
                    $extraPhones[] = $p;
                }
            }
        }

        $id = DB::table('sso_profiles')->insertGetId([
            'employee_code' => $hccEmployee->employee_code,
            'hcc_employee_id' => $hccEmployee->id,
            'hcc_user_id' => $hccUser->id ?? null,
            'name' => $hccEmployee->name,
            'primary_email' => $primaryEmail,
            'extra_emails' => json_encode(array_values(array_unique($extraEmails))),
            'primary_phone' => $primaryPhone,
            'extra_phones' => json_encode(array_values(array_unique($extraPhones))),
            'standardized_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('sso_profiles')->find($id);
    }

    /**
     * Save standardized profile and sync to HCC (izi) + all connected systems.
     */
    public function saveStandardizedProfile(
        string $employeeCode,
        string $primaryEmail,
        array $extraEmails,
        string $primaryPhone,
        array $extraPhones,
        ?string $newPassword = null
    ): object {
        $profile = DB::table('sso_profiles')->where('employee_code', $employeeCode)->first();
        abort_unless($profile, 404, 'Không tìm thấy hồ sơ SSO.');
        $oldEmail = $profile->primary_email;
        $oldPhone = $profile->primary_phone;

        $cleanExtraEmails = array_values(array_unique(array_filter(array_map(function ($e) use ($primaryEmail) {
            $e = trim((string) $e);
            return ($e !== '' && mb_strtolower($e) !== mb_strtolower($primaryEmail)) ? $e : null;
        }, $extraEmails))));

        $cleanExtraPhones = array_values(array_unique(array_filter(array_map(function ($p) use ($primaryPhone) {
            $p = trim((string) $p);
            return ($p !== '' && $p !== $primaryPhone) ? $p : null;
        }, $extraPhones))));

        DB::table('sso_profiles')->where('id', $profile->id)->update([
            'primary_email' => $primaryEmail,
            'extra_emails' => json_encode($cleanExtraEmails),
            'primary_phone' => $primaryPhone,
            'extra_phones' => json_encode($cleanExtraPhones),
            'standardized_at' => now(),
            'updated_at' => now(),
        ]);

        $hashedPassword = $newPassword ? Hash::make($newPassword) : null;

        // 1. Update HCC (izi.employees & izi.users)
        try {
            DB::connection('izi')->table('employees')
                ->where('employee_code', $employeeCode)
                ->update([
                    'email' => $primaryEmail,
                    'phone' => $primaryPhone,
                    'updated_at' => now(),
                ]);

            $emp = DB::connection('izi')->table('employees')->where('employee_code', $employeeCode)->first();
            if ($emp) {
                $userUpdate = [
                    'email' => $primaryEmail,
                    'phone' => $primaryPhone,
                    'secondary_phone' => $cleanExtraPhones[0] ?? null,
                    'whatsapp_phone' => $cleanExtraPhones[1] ?? null,
                    'updated_at' => now(),
                ];
                if ($hashedPassword) {
                    $userUpdate['password'] = $hashedPassword;
                }
                DB::connection('izi')->table('users')
                    ->where('employee_id', $emp->id)
                    ->update($userUpdate);
            }
        } catch (Throwable $e) {
            // Keep going so other DBs still sync
        }

        // 2. Sync to idc.users (Main + MKT)
        try {
            $idcUpdate = [
                'employee_code' => $employeeCode,
                'email' => $primaryEmail,
                'secondary_email' => $cleanExtraEmails[0] ?? null,
                'phone' => $primaryPhone,
            ];
            if ($hashedPassword) {
                $idcUpdate['password'] = $hashedPassword;
            }
            DB::connection('mysql')->table('users')
                ->where('employee_code', $employeeCode)
                ->when($oldEmail, fn ($q) => $q->orWhere('email', $oldEmail))
                ->update($idcUpdate);
        } catch (Throwable $e) {
        }

        // 3. Sync to learning.users
        try {
            $learningUpdate = [
                'employee_code' => $employeeCode,
                'email' => $primaryEmail,
                'phone' => $primaryPhone,
                'secondary_phone' => $cleanExtraPhones[0] ?? null,
                'whatsapp_phone' => $cleanExtraPhones[1] ?? null,
                'updated_at' => now(),
            ];
            if ($hashedPassword) {
                $learningUpdate['password'] = $hashedPassword;
            }
            DB::connection('learning')->table('users')
                ->where('employee_code', $employeeCode)
                ->when($oldEmail, fn ($q) => $q->orWhere('email', $oldEmail))
                ->update($learningUpdate);
        } catch (Throwable $e) {
        }

        // 4. Sync to bds_crm.users
        try {
            $crmUpdate = [
                'email' => $primaryEmail,
                'phone' => $this->normalizePhone($primaryPhone) ?: $primaryPhone,
                'updated_at' => now(),
            ];
            if (Schema::connection('bds_crm')->hasColumn('users', 'employee_code')) {
                $crmUpdate['employee_code'] = $employeeCode;
            }
            if ($hashedPassword) {
                $crmUpdate['password_hash'] = $hashedPassword;
            }
            DB::connection('bds_crm')->table('users')
                ->where(function ($q) use ($oldEmail, $oldPhone, $employeeCode) {
                    if ($oldEmail) {
                        $q->where('email', $oldEmail);
                    }
                    if ($oldPhone) {
                        $q->orWhere('phone', $oldPhone)->orWhere('phone', $this->normalizePhone($oldPhone));
                    }
                    if (Schema::connection('bds_crm')->hasColumn('users', 'employee_code')) {
                        $q->orWhere('employee_code', $employeeCode);
                    }
                })
                ->update($crmUpdate);
        } catch (Throwable $e) {
        }

        // 5. Sync to idc_tool.users
        try {
            $toolUpdate = [
                'email' => $primaryEmail,
                'phone' => $primaryPhone,
                'updated_at' => now(),
            ];
            if (Schema::connection('idc_tool')->hasColumn('users', 'employee_code')) {
                $toolUpdate['employee_code'] = $employeeCode;
            }
            if ($hashedPassword) {
                $toolUpdate['password'] = $hashedPassword;
            }
            DB::connection('idc_tool')->table('users')
                ->where(function ($q) use ($oldEmail, $oldPhone, $employeeCode) {
                    if ($oldEmail) {
                        $q->where('email', $oldEmail);
                    }
                    if ($oldPhone) {
                        $q->orWhere('phone', $oldPhone);
                    }
                    if (Schema::connection('idc_tool')->hasColumn('users', 'employee_code')) {
                        $q->orWhere('employee_code', $employeeCode);
                    }
                })
                ->update($toolUpdate);
        } catch (Throwable $e) {
        }

        return DB::table('sso_profiles')->where('id', $profile->id)->first();
    }

    public function buildSignedCallbackUrl(array $appConfig, object $profile, ?object $hccUser = null): string
    {
        $payload = [
            'employee_code' => $profile->employee_code,
            'name' => $profile->name,
            'email' => $profile->primary_email,
            'phone' => $profile->primary_phone,
            'permission' => $hccUser->permission ?? 3,
            'avatar' => $hccUser->avatar ?? null,
            'timestamp' => now()->timestamp,
            'nonce' => Str::random(16),
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $encoded = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', $encoded, (string) config('sso.secret'));

        $base = rtrim($appConfig['url'], '/') . '/' . ltrim($appConfig['callback_path'], '/');
        return $base . '?payload=' . urlencode($encoded) . '&signature=' . $signature;
    }

    public function verifySignedPayload(string $encodedPayload, string $signature): ?array
    {
        $secret = (string) config('sso.secret');
        if ($secret === '' || $encodedPayload === '' || $signature === '') {
            return null;
        }

        $expected = hash_hmac('sha256', $encodedPayload, $secret);
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $json = base64_decode(strtr($encodedPayload, '-_', '+/'), true);
        if ($json === false) {
            return null;
        }

        $payload = json_decode($json, true);
        if (!is_array($payload) || empty($payload['timestamp'])) {
            return null;
        }

        $ttl = (int) config('sso.token_ttl', 300);
        if (abs(now()->timestamp - (int) $payload['timestamp']) > $ttl) {
            return null;
        }

        return $payload;
    }
}
