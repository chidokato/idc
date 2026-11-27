@extends('admin.layout.main')

@section('content')
@include('admin.alert')

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-database mr-1"></i> Danh sách kết nối &amp; Bảng DB dùng chung
                </h5>
                <p class="text-muted mb-0 small mt-1">
                    Quản lý các kết nối tới Database khác để dùng chung bảng dữ liệu. Hỗ trợ kiểm tra kết nối và xem trước dữ liệu bảng.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.shared-db-configs.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Thêm cấu hình DB mới
                </a>
            </div>
        </div>

        <div class="card-body">
            @if ($configs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <div class="mb-2 text-primary" style="font-size: 36px;">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5>Chưa có cấu hình DB dùng chung nào</h5>
                    <p class="text-muted mb-3">Bạn có thể chủ động thêm thông tin kết nối (Host, Port, Database, Username, Password, Tên bảng) để dùng chung bảng từ DB khác.</p>
                    <a href="{{ route('admin.shared-db-configs.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Thêm cấu hình đầu tiên
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tên cấu hình / Mã gọi</th>
                                <th>Máy chủ &amp; Database</th>
                                <th>Bảng dùng chung</th>
                                <th>Tài khoản DB</th>
                                <th style="width: 130px;">Trạng thái</th>
                                <th class="text-right" style="width: 240px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($configs as $item)
                                <tr>
                                    <td>{{ $loop->iteration + $configs->firstItem() - 1 }}</td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $item->name }}</div>
                                        <div class="small text-muted">
                                            Mã code: <code class="text-primary">{{ $item->code }}</code>
                                        </div>
                                        @if ($item->description)
                                            <div class="small text-muted text-truncate" style="max-width: 240px;" title="{{ $item->description }}">
                                                {{ $item->description }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <span class="badge badge-info text-uppercase mr-1">{{ $item->driver }}</span>
                                            <span class="font-weight-bold">{{ $item->host }}:{{ $item->port }}</span>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            DB: <strong class="text-dark">{{ $item->database }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach ($item->getTableNames() as $tblName)
                                            <span class="badge badge-primary px-2 py-1 mb-1">
                                                <i class="fas fa-table mr-1"></i>{{ $item->prefix }}{{ $tblName }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-user text-muted mr-1"></i>{{ $item->username }}
                                        </div>
                                        <div class="small text-muted">
                                            Charset: {{ $item->charset }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input js-toggle-status"
                                                id="switch_{{ $item->id }}"
                                                data-url="{{ route('admin.shared-db-configs.toggle-status', $item) }}"
                                                {{ $item->is_active ? 'checked' : '' }}
                                            >
                                            <label class="custom-control-label" for="switch_{{ $item->id }}">
                                                <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-secondary' }} js-status-label">
                                                    {{ $item->is_active ? 'Đang bật' : 'Tạm tắt' }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="d-inline-flex align-items-center">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-success mr-1 js-test-row-connection"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                title="Kiểm tra kết nối & Xem dữ liệu bảng"
                                            >
                                                <i class="fas fa-plug mr-1"></i> Kiểm tra / Xem bảng
                                            </button>
                                            <a href="{{ route('admin.shared-db-configs.edit', $item) }}" class="btn btn-sm btn-info mr-1" title="Chỉnh sửa">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.shared-db-configs.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa cấu hình DB dùng chung này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $configs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Xem trước dữ liệu bảng dùng chung -->
<div class="modal fade" id="sharedDbPreviewModal" tabindex="-1" role="dialog" aria-labelledby="sharedDbPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="sharedDbPreviewModalLabel">
                    <i class="fas fa-database text-primary mr-1"></i> Kết quả kiểm tra kết nối &amp; Xem trước bảng
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="sharedDbPreviewBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted mb-0">Đang kết nối tới Database...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";

    // Toggle status
    document.querySelectorAll('.js-toggle-status').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const url = this.getAttribute('data-url');
            const label = this.closest('.custom-switch').querySelector('.js-status-label');

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (label) {
                    label.textContent = data.label;
                    label.className = 'badge js-status-label ' + (data.is_active ? 'badge-success' : 'badge-secondary');
                }
            })
            .catch(() => {
                alert('Không thể cập nhật trạng thái.');
                this.checked = !this.checked;
            });
        });
    });

    const previewBody = document.getElementById('sharedDbPreviewBody');

    document.querySelectorAll('.js-test-row-connection').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const configId = this.getAttribute('data-id');
            const configName = this.getAttribute('data-name');

            document.getElementById('sharedDbPreviewModalLabel').innerHTML =
                `<i class="fas fa-database text-primary mr-1"></i> Kiểm tra kết nối: <strong>${escapeHtml(configName)}</strong>`;
            previewBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted mb-0">Đang kiểm tra kết nối và tải cấu trúc bảng...</p>
                </div>
            `;
            $('#sharedDbPreviewModal').modal('show');

            fetch("{{ route('admin.shared-db-configs.test-connection') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ config_id: configId })
            })
            .then(async res => {
                const data = await res.json();
                renderPreviewResult(data);
            })
            .catch(err => {
                renderPreviewResult({
                    success: false,
                    message: 'Lỗi hệ thống khi gửi yêu cầu kiểm tra: ' + err.message
                });
            });
        });
    });

    function renderPreviewResult(data) {
        if (!previewBody) return;

        if (!data.success) {
            previewBody.innerHTML = `
                <div class="alert alert-danger mb-0">
                    <h6 class="alert-heading font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Kết nối không thành công</h6>
                    <p class="mb-0">${escapeHtml(data.message || 'Không thể kết nối.')}</p>
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

        let tablesHtml = tables.map(tbl => {
            const columns = tbl.columns || [];
            const rows = tbl.sample_rows || [];
            let columnsBadgeHtml = columns.map(col => `<code class="badge badge-light text-dark border mr-1 mb-1">${escapeHtml(col)}</code>`).join('');

            let tableHtml = '';
            if (rows.length === 0) {
                tableHtml = `<div class="alert alert-info mb-0">Bảng <strong>${escapeHtml(tbl.table_name)}</strong> hiện chưa có bản ghi nào (0 dòng).</div>`;
            } else {
                let thead = columns.map(col => `<th class="text-nowrap">${escapeHtml(col)}</th>`).join('');
                let tbody = rows.map(row => {
                    let tds = columns.map(col => {
                        let val = row[col];
                        if (val === null || val === undefined) {
                            return `<td class="text-muted font-italic">NULL</td>`;
                        }
                        let str = typeof val === 'object' ? JSON.stringify(val) : String(val);
                        if (str.length > 80) str = str.substring(0, 80) + '...';
                        return `<td class="text-nowrap">${escapeHtml(str)}</td>`;
                    }).join('');
                    return `<tr>${tds}</tr>`;
                }).join('');

                tableHtml = `
                    <div class="table-responsive border rounded">
                        <table class="table table-sm table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>${thead}</tr>
                            </thead>
                            <tbody>${tbody}</tbody>
                        </table>
                    </div>
                `;
            }

            return `
                <div class="card border mb-3">
                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-2">
                        <h6 class="mb-0 font-weight-bold text-primary">
                            <i class="fas fa-table mr-1"></i> Bảng: <code>${escapeHtml(tbl.table_name)}</code>
                        </h6>
                        <span class="badge badge-primary">${tbl.total_rows} bản ghi</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <div class="small font-weight-bold mb-1">Danh sách cột (${columns.length} cột):</div>
                            <div>${columnsBadgeHtml}</div>
                        </div>
                        <div>
                            <div class="small font-weight-bold mb-1">Xem trước dữ liệu mẫu:</div>
                            ${tableHtml}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        previewBody.innerHTML = `
            <div class="alert alert-success d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-check-circle mr-1"></i>
                    <strong>${escapeHtml(data.message)}</strong>
                </div>
                <span class="badge badge-success">${tables.length} bảng / Tổng ${data.total_rows} dòng</span>
            </div>
            ${tablesHtml}
        `;
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
