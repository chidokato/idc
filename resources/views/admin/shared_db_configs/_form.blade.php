@csrf

<div class="row">
    <div class="col-xl-8">
        <div class="card shadow-sm border mb-4">
            <div class="card-header d-flex align-items-center justify-content-between bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-database mr-1"></i> Thông tin kết nối Database &amp; Bảng dùng chung
                </h6>
                <button type="button" class="btn btn-sm btn-success" id="btn-test-connection-form">
                    <i class="fas fa-plug mr-1"></i> Kiểm tra kết nối &amp; Xem bảng
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold small">Tên gợi nhớ cấu hình <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $config->name ?? '') }}"
                                placeholder="Ví dụ: DB Nhân sự tổng (Bảng users), DB CRM..."
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="form-group">
                            <label for="code" class="font-weight-bold small">Mã định danh (Code gọi trong hệ thống)</label>
                            <input
                                type="text"
                                id="code"
                                name="code"
                                class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code', $config->code ?? '') }}"
                                placeholder="Ví dụ: hr_users, crm_customers..."
                            >
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Tự động tạo nếu để trống. Dùng để gọi nhanh trong code.</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="driver" class="font-weight-bold small">Hệ quản trị CSDL (Driver) <span class="text-danger">*</span></label>
                            @php
                                $selectedDriver = old('driver', $config->driver ?? 'mysql');
                            @endphp
                            <select id="driver" name="driver" class="form-control @error('driver') is-invalid @enderror" required>
                                <option value="mysql" {{ $selectedDriver === 'mysql' ? 'selected' : '' }}>MySQL / MariaDB (mysql)</option>
                                <option value="pgsql" {{ $selectedDriver === 'pgsql' ? 'selected' : '' }}>PostgreSQL (pgsql)</option>
                                <option value="sqlsrv" {{ $selectedDriver === 'sqlsrv' ? 'selected' : '' }}>SQL Server (sqlsrv)</option>
                                <option value="sqlite" {{ $selectedDriver === 'sqlite' ? 'selected' : '' }}>SQLite (sqlite)</option>
                            </select>
                            @error('driver')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="form-group">
                            <label for="host" class="font-weight-bold small">DB Host (Địa chỉ máy chủ) <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="host"
                                name="host"
                                class="form-control @error('host') is-invalid @enderror"
                                value="{{ old('host', $config->host ?? '127.0.0.1') }}"
                                placeholder="127.0.0.1 hoặc localhost hoặc IP server"
                                required
                            >
                            @error('host')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="port" class="font-weight-bold small">DB Port (Cổng) <span class="text-danger">*</span></label>
                            <input
                                type="number"
                                id="port"
                                name="port"
                                class="form-control @error('port') is-invalid @enderror"
                                value="{{ old('port', $config->port ?? 3306) }}"
                                placeholder="3306"
                                required
                            >
                            @error('port')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="database" class="font-weight-bold small">Tên Database (DB Name) <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="database"
                                name="database"
                                class="form-control @error('database') is-invalid @enderror"
                                value="{{ old('database', $config->database ?? '') }}"
                                placeholder="Nhập tên cơ sở dữ liệu cần kết nối..."
                                required
                            >
                            @error('database')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="table_name" class="font-weight-bold small">Tên bảng dùng chung (Table Name) <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="table_name"
                                name="table_name"
                                class="form-control border-primary @error('table_name') is-invalid @enderror"
                                value="{{ old('table_name', $config->table_name ?? '') }}"
                                placeholder="Ví dụ: employees, departments (ngăn cách bởi dấu phẩy)"
                                required
                            >
                            @error('table_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Có thể nhập nhiều bảng trong cùng 1 DB, ngăn cách bởi dấu phẩy (ví dụ: <code>employees, departments</code>).</small>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="username" class="font-weight-bold small">Tài khoản DB (Username) <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username', $config->username ?? '') }}"
                                placeholder="Ví dụ: root"
                                required
                            >
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="password" class="font-weight-bold small">Mật khẩu DB (Password)</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    value="{{ old('password', $config->password ?? '') }}"
                                    placeholder="Để trống nếu DB không có mật khẩu"
                                >
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-toggle-password" title="Hiện/Ẩn mật khẩu">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label for="description" class="font-weight-bold small">Ghi chú / Mô tả mục đích sử dụng</label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Ghi chú thêm về bảng dữ liệu dùng chung này..."
                            >{{ old('description', $config->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Khu vực hiển thị kết quả Test Connection ngay trên form -->
                <div id="form-test-connection-result" class="mt-3 d-none"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tùy chọn nâng cao</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="prefix" class="font-weight-bold small">Tiền tố bảng (Table Prefix)</label>
                    <input
                        type="text"
                        id="prefix"
                        name="prefix"
                        class="form-control @error('prefix') is-invalid @enderror"
                        value="{{ old('prefix', $config->prefix ?? '') }}"
                        placeholder="Ví dụ: tbl_ (để trống nếu không có)"
                    >
                    @error('prefix')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="charset" class="font-weight-bold small">Bảng mã (Charset)</label>
                    <input
                        type="text"
                        id="charset"
                        name="charset"
                        class="form-control @error('charset') is-invalid @enderror"
                        value="{{ old('charset', $config->charset ?? 'utf8mb4') }}"
                        placeholder="utf8mb4"
                    >
                    @error('charset')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="collation" class="font-weight-bold small">Collation</label>
                    <input
                        type="text"
                        id="collation"
                        name="collation"
                        class="form-control @error('collation') is-invalid @enderror"
                        value="{{ old('collation', $config->collation ?? 'utf8mb4_unicode_ci') }}"
                        placeholder="utf8mb4_unicode_ci"
                    >
                    @error('collation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="custom-control custom-switch mt-3">
                    <input
                        type="checkbox"
                        class="custom-control-input"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $config->is_active ?? true) ? 'checked' : '' }}
                    >
                    <label class="custom-control-label font-weight-bold small" for="is_active">Kích hoạt cấu hình này</label>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border bg-light">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-code mr-1"></i> Hướng dẫn gọi nhanh trong Code</h6>
            </div>
            <div class="card-body small">
                <p class="text-muted mb-2">Sau khi lưu cấu hình với mã <code>code</code>, bạn có thể truy vấn trực tiếp bất kỳ bảng dùng chung nào trong DB bằng Model <code>SharedDbConfig</code>:</p>
                <pre class="bg-dark text-light p-2 rounded mb-0" style="font-size: 12px;"><code>use App\Models\SharedDbConfig;

// Gọi theo mã code và tên bảng cụ thể:
$emp = SharedDbConfig::tableByCode('<span id="code-preview-text">{{ $config->code ?: 'ma_code' }}</span>', 'employees')
    ->limit(20)
    ->get();

$dept = SharedDbConfig::tableByCode('{{ $config->code ?: 'ma_code' }}', 'departments')
    ->get();</code></pre>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    const codePreviewText = document.getElementById('code-preview-text');

    function slugifyCode(val) {
        return val
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
    }

    if (nameInput && codeInput) {
        let isCodeEdited = codeInput.value.trim() !== '';

        codeInput.addEventListener('input', function () {
            isCodeEdited = codeInput.value.trim() !== '';
            if (codePreviewText) {
                codePreviewText.textContent = codeInput.value.trim() || 'ma_code';
            }
        });

        nameInput.addEventListener('input', function () {
            if (!isCodeEdited) {
                const generated = slugifyCode(nameInput.value);
                codeInput.value = generated;
                if (codePreviewText) {
                    codePreviewText.textContent = generated || 'ma_code';
                }
            }
        });
    }

    const btnTogglePwd = document.getElementById('btn-toggle-password');
    const pwdInput = document.getElementById('password');
    if (btnTogglePwd && pwdInput) {
        btnTogglePwd.addEventListener('click', function () {
            const isPwd = pwdInput.getAttribute('type') === 'password';
            pwdInput.setAttribute('type', isPwd ? 'text' : 'password');
            this.innerHTML = isPwd ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });
    }

    const btnTest = document.getElementById('btn-test-connection-form');
    const resultBox = document.getElementById('form-test-connection-result');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";

    if (btnTest && resultBox) {
        btnTest.addEventListener('click', function () {
            const payload = {
                driver: document.getElementById('driver')?.value || 'mysql',
                host: document.getElementById('host')?.value || '',
                port: document.getElementById('port')?.value || 3306,
                database: document.getElementById('database')?.value || '',
                username: document.getElementById('username')?.value || '',
                password: document.getElementById('password')?.value || '',
                table_name: document.getElementById('table_name')?.value || '',
                prefix: document.getElementById('prefix')?.value || '',
                charset: document.getElementById('charset')?.value || 'utf8mb4',
                collation: document.getElementById('collation')?.value || 'utf8mb4_unicode_ci',
            };

            if (!payload.host || !payload.database || !payload.username || !payload.table_name) {
                resultBox.classList.remove('d-none');
                resultBox.innerHTML = `
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Vui lòng điền đầy đủ <strong>DB Host, Tên Database, Tài khoản DB</strong> và <strong>Tên bảng dùng chung</strong> trước khi kiểm tra kết nối.
                    </div>
                `;
                return;
            }

            btnTest.disabled = true;
            btnTest.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Đang kiểm tra...';
            resultBox.classList.remove('d-none');
            resultBox.innerHTML = `
                <div class="alert alert-info mb-0">
                    <span class="spinner-border spinner-border-sm mr-2"></span> Đang thử kết nối tới Database <strong>${escapeHtml(payload.database)}</strong>...
                </div>
            `;

            fetch("{{ route('admin.shared-db-configs.test-connection', [], false) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload)
            })
            .then(async res => {
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    data = { success: false, message: 'Phản hồi không hợp lệ (HTTP ' + res.status + ').' };
                }
                btnTest.disabled = false;
                btnTest.innerHTML = '<i class="fas fa-plug mr-1"></i> Kiểm tra kết nối & Xem bảng';

                if (!data.success) {
                    resultBox.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            <h6 class="alert-heading font-weight-bold mb-1"><i class="fas fa-exclamation-circle mr-1"></i> Kiểm tra thất bại</h6>
                            <div>${escapeHtml(data.message || 'Không thể kết nối tới Database.')}</div>
                        </div>
                    `;
                    return;
                }

                const tables = data.tables || [{
                    table_name: data.table_name,
                    columns: data.columns || [],
                    total_rows: data.total_rows || 0,
                    sample_rows: data.sample_rows || []
                }];

                let tablesBlocksHtml = tables.map(tbl => {
                    const columns = tbl.columns || [];
                    const rows = tbl.sample_rows || [];
                    const colBadges = columns.map(c => `<code class="badge badge-light text-dark border mr-1 mb-1">${escapeHtml(c)}</code>`).join('');

                    let sampleHtml = '';
                    if (rows.length > 0) {
                        let ths = columns.slice(0, 8).map(c => `<th class="text-nowrap">${escapeHtml(c)}</th>`).join('');
                        let trs = rows.slice(0, 5).map(r => {
                            let tds = columns.slice(0, 8).map(c => {
                                let v = r[c];
                                let s = v === null || v === undefined ? 'NULL' : (typeof v === 'object' ? JSON.stringify(v) : String(v));
                                if (s.length > 50) s = s.substring(0, 50) + '...';
                                return `<td class="text-nowrap">${escapeHtml(s)}</td>`;
                            }).join('');
                            return `<tr>${tds}</tr>`;
                        }).join('');

                        sampleHtml = `
                            <div class="mt-2">
                                <div class="small font-weight-bold mb-1">Dữ liệu mẫu bảng <code>${escapeHtml(tbl.table_name)}</code> (tối đa 5 dòng đầu):</div>
                                <div class="table-responsive bg-white border rounded">
                                    <table class="table table-sm table-bordered mb-0 small">
                                        <thead class="thead-light"><tr>${ths}</tr></thead>
                                        <tbody>${trs}</tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }

                    return `
                        <div class="border rounded bg-white p-2 mt-2">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="font-weight-bold text-primary">
                                    <i class="fas fa-table mr-1"></i> Bảng: <code>${escapeHtml(tbl.table_name)}</code>
                                </span>
                                <span class="badge badge-primary">${tbl.total_rows} bản ghi</span>
                            </div>
                            <div class="small mb-1"><strong>Các cột (${columns.length}):</strong></div>
                            <div>${colBadges}</div>
                            ${sampleHtml}
                        </div>
                    `;
                }).join('');

                resultBox.innerHTML = `
                    <div class="alert alert-success mb-0">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="alert-heading font-weight-bold mb-0">
                                <i class="fas fa-check-circle mr-1"></i> ${escapeHtml(data.message)}
                            </h6>
                            <span class="badge badge-success">${tables.length} bảng / ${data.total_rows} bản ghi</span>
                        </div>
                        ${tablesBlocksHtml}
                    </div>
                `;
            })
            .catch(err => {
                btnTest.disabled = false;
                btnTest.innerHTML = '<i class="fas fa-plug mr-1"></i> Kiểm tra kết nối & Xem bảng';
                resultBox.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        Lỗi kết nối: ${escapeHtml(err.message)}
                    </div>
                `;
            });
        });
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
</script>
@endsection
