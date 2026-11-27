@extends('admin.layout.main')

@section('content')
@include('admin.alert')

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Cấu hình Bảng Dùng Chung SSO</h1>
            <p class="mb-0 text-muted small">
                Khai báo các database, bảng người dùng / tài khoản và ánh xạ tên cột dùng chung để đăng nhập &amp; đồng bộ dữ liệu trên toàn hệ thống.
            </p>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addSharedTableModal">
                <i class="fas fa-plus mr-1"></i> Thêm bảng dùng chung
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách bảng dữ liệu người dùng &amp; tài khoản</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sso-shared-tables.update-all') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 55px;">TT</th>
                                <th style="min-width: 190px;">Hệ thống &amp; Vai trò</th>
                                <th style="min-width: 180px;">Database &amp; Bảng</th>
                                <th style="min-width: 150px;">Cột Mã NV / Tên</th>
                                <th style="min-width: 170px;">Cột Email / Username</th>
                                <th style="min-width: 160px;">Cột SĐT / Password</th>
                                <th style="width: 110px;">Trạng thái DB</th>
                                <th style="width: 115px;" class="text-center">Quyền SSO</th>
                                <th style="width: 50px;" class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tables as $tbl)
                                @php $status = $health[$tbl->id] ?? ['ok' => false, 'message' => '']; @endphp
                                <tr>
                                    <td>
                                        <input type="number" name="tables[{{ $tbl->id }}][sort_order]" class="form-control form-control-sm" value="{{ $tbl->sort_order }}">
                                    </td>
                                    <td>
                                        <input type="text" name="tables[{{ $tbl->id }}][system_name]" class="form-control form-control-sm mb-1 font-weight-bold" value="{{ $tbl->system_name }}" required>
                                        <select name="tables[{{ $tbl->id }}][role_type]" class="form-control form-control-sm">
                                            <option value="source_employee" {{ $tbl->role_type === 'source_employee' ? 'selected' : '' }}>Bảng Nhân sự gốc</option>
                                            <option value="source_user" {{ $tbl->role_type === 'source_user' ? 'selected' : '' }}>Bảng Tài khoản gốc</option>
                                            <option value="sync_target" {{ $tbl->role_type === 'sync_target' ? 'selected' : '' }}>Web con đồng bộ</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="tables[{{ $tbl->id }}][database_name]" class="form-control form-control-sm mb-1" value="{{ $tbl->database_name }}" placeholder="Database" required>
                                        <input type="text" name="tables[{{ $tbl->id }}][table_name]" class="form-control form-control-sm" value="{{ $tbl->table_name }}" placeholder="Table" required>
                                    </td>
                                    <td>
                                        <input type="text" name="tables[{{ $tbl->id }}][employee_code_col]" class="form-control form-control-sm mb-1" value="{{ $tbl->employee_code_col }}" placeholder="employee_code">
                                        <input type="text" name="tables[{{ $tbl->id }}][name_col]" class="form-control form-control-sm" value="{{ $tbl->name_col }}" placeholder="name / full_name">
                                    </td>
                                    <td>
                                        <input type="text" name="tables[{{ $tbl->id }}][email_col]" class="form-control form-control-sm mb-1" value="{{ $tbl->email_col }}" placeholder="email">
                                        <div class="d-flex gap-1">
                                            <input type="text" name="tables[{{ $tbl->id }}][secondary_email_col]" class="form-control form-control-sm mr-1" value="{{ $tbl->secondary_email_col }}" placeholder="email phụ">
                                            <input type="text" name="tables[{{ $tbl->id }}][username_col]" class="form-control form-control-sm" value="{{ $tbl->username_col }}" placeholder="username">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex mb-1">
                                            <input type="text" name="tables[{{ $tbl->id }}][phone_col]" class="form-control form-control-sm mr-1" value="{{ $tbl->phone_col }}" placeholder="phone">
                                            <input type="text" name="tables[{{ $tbl->id }}][secondary_phone_col]" class="form-control form-control-sm" value="{{ $tbl->secondary_phone_col }}" placeholder="phone phụ">
                                        </div>
                                        <input type="text" name="tables[{{ $tbl->id }}][password_col]" class="form-control form-control-sm" value="{{ $tbl->password_col }}" placeholder="password">
                                    </td>
                                    <td>
                                        @if($status['ok'])
                                            <span class="badge badge-success d-block py-1"><i class="fas fa-check-circle mr-1"></i> OK</span>
                                        @else
                                            <span class="badge badge-danger d-block py-1" title="{{ $status['message'] }}"><i class="fas fa-exclamation-triangle mr-1"></i> Lỗi</span>
                                        @endif
                                        <small class="text-muted d-block text-center mt-1">{{ $tbl->system_key }}</small>
                                    </td>
                                    <td class="small">
                                        <label class="d-flex align-items-center mb-1">
                                            <input type="checkbox" name="tables[{{ $tbl->id }}][can_login]" value="1" {{ $tbl->can_login ? 'checked' : '' }} class="mr-1"> Đăng nhập
                                        </label>
                                        <label class="d-flex align-items-center mb-1">
                                            <input type="checkbox" name="tables[{{ $tbl->id }}][can_sync]" value="1" {{ $tbl->can_sync ? 'checked' : '' }} class="mr-1"> Đồng bộ
                                        </label>
                                        <label class="d-flex align-items-center mb-0">
                                            <input type="checkbox" name="tables[{{ $tbl->id }}][is_active]" value="1" {{ $tbl->is_active ? 'checked' : '' }} class="mr-1"> Kích hoạt
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="if(confirm('Xóa cấu hình {{ $tbl->system_name }}?')) document.getElementById('del-tbl-{{ $tbl->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="far fa-save mr-1"></i> Lưu cấu hình bảng dùng chung
                    </button>
                </div>
            </form>

            @foreach($tables as $tbl)
                <form id="del-tbl-{{ $tbl->id }}" action="{{ route('admin.sso-shared-tables.destroy', $tbl->id) }}" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        </div>
    </div>
</div>

{{-- Modal thêm bảng dùng chung --}}
<div class="modal fade" id="addSharedTableModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('admin.sso-shared-tables.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Thêm bảng dùng chung mới</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold">Mã hệ thống (system_key) <span class="text-danger">*</span></label>
                        <input type="text" name="system_key" class="form-control" placeholder="VD: voice_users" required>
                    </div>
                    <div class="col-md-5 form-group">
                        <label class="small font-weight-bold">Tên hiển thị <span class="text-danger">*</span></label>
                        <input type="text" name="system_name" class="form-control" placeholder="VD: Voice (omni.users)" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Vai trò <span class="text-danger">*</span></label>
                        <select name="role_type" class="form-control">
                            <option value="sync_target">Web con đồng bộ</option>
                            <option value="source_employee">Bảng Nhân sự gốc</option>
                            <option value="source_user">Bảng Tài khoản gốc</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Tên Database <span class="text-danger">*</span></label>
                        <input type="text" name="database_name" class="form-control" placeholder="VD: facebookads" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Tên Bảng <span class="text-danger">*</span></label>
                        <input type="text" name="table_name" class="form-control" value="users" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Cột Mã NV</label>
                        <input type="text" name="employee_code_col" class="form-control" value="employee_code">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Cột Họ tên</label>
                        <input type="text" name="name_col" class="form-control" value="name">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Cột Email</label>
                        <input type="text" name="email_col" class="form-control" value="email">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Cột SĐT</label>
                        <input type="text" name="phone_col" class="form-control" value="phone">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold">Cột Password</label>
                        <input type="text" name="password_col" class="form-control" value="password">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold">Cột Email phụ</label>
                        <input type="text" name="secondary_email_col" class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold">Cột Username</label>
                        <input type="text" name="username_col" class="form-control">
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <label class="mr-3"><input type="checkbox" name="can_login" value="1" checked> Cho phép kiểm tra đăng nhập</label>
                    <label><input type="checkbox" name="can_sync" value="1" checked> Tự động đồng bộ khi chuẩn hóa</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Thêm bảng</button>
            </div>
        </form>
    </div>
</div>
@endsection
