@extends('admin.layout.main')

@section('content')
@include('admin.alert')

@if ($errors->any())
<div class="alert alert-danger">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('users.update', [$data->id]) }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed">
    <button type="button" id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
    <ul class="navbar-nav ">
        <li class="nav-item"><a class="nav-link line-1" href="{{ $data->permission == 6 ? route('users.member') : route('users.index') }}"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span class="mobile-hide">Quay lại</span></a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item mobile-hide">
            <button type="reset" class="btn-danger mr-2 form-control"><i class="fas fa-sync"></i> Làm mới</button>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item">
            <button type="submit" class="btn-success form-control"><i class="far fa-save"></i> Lưu lại</button>
        </li>
    </ul>
</nav>

<div class="d-sm-flex align-items-center justify-content-between mb-3 flex" style="height: 38px;">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Cập nhật người dùng</h2>
</div>

<div class="row">
    <div class="col-xl-9 col-lg-9">
        <div class="card shadow mb-2">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#vi">Người dùng</a></li>
                </ul>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="vi">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input value="{{ old('email', $data->email) }}" name="email" placeholder="..." type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Quyền người dùng</label>
                                    <select name="permission" class="form-control">
                                        <option value="1" {{ old('permission', $data->permission) == 1 ? 'selected' : '' }}>SuperAdmin</option>
                                        <option value="2" {{ old('permission', $data->permission) == 2 ? 'selected' : '' }}>Admin</option>
                                        <option value="3" {{ old('permission', $data->permission) == 3 ? 'selected' : '' }}>Editor</option>
                                        <option value="6" {{ old('permission', $data->permission) == 6 ? 'selected' : '' }}>Member</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Chức vụ</label>
                                    <select name="rank" class="form-control">
                                        <option value="">---</option>
                                        <option value="1" {{ old('rank', $data->rank) == 1 ? 'selected' : '' }}>Giám đốc</option>
                                        <option value="2" {{ old('rank', $data->rank) == 2 ? 'selected' : '' }}>Trưởng phòng</option>
                                        <option value="3" {{ old('rank', $data->rank) == 3 ? 'selected' : '' }}>Nhân viên</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="status" class="form-control">
                                        <option value="active" {{ old('status', $data->status) == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                        <option value="inactive" {{ old('status', $data->status) == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Phòng/nhóm</label>
                                    <select name="department_id" class="form-control select2">
                                        <option value="">-- Chọn phòng ban cấp cuối --</option>
                                        {!! $options !!}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="edit_pass">
                                        <label>Mật khẩu</label>
                                        <label class="cursor_pointer"><input type="checkbox" id="changepassword" name="changepassword" /> <strong>EDIT</strong></label>
                                    </div>
                                    <input disabled name="password" placeholder="Password" type="password" class="form-control pass">
                                </div>
                                <div class="form-group">
                                    <label class="">Nhập lại mật khẩu</label>
                                    <input disabled name="passwordagain" placeholder="Confirm password" type="password" class="form-control pass">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mã NV</label>
                                    <input value="{{ old('employee_code', $data->employee_code) }}" name="employee_code" placeholder="..." type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Họ & Tên</label>
                                    <input value="{{ old('yourname', $data->yourname) }}" name="yourname" placeholder="..." type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Địa chỉ</label>
                                    <input value="{{ old('address', $data->address) }}" name="address" placeholder="..." type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Số điện thoại</label>
                                    <input value="{{ old('phone', $data->phone) }}" name="phone" placeholder="..." type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Facebook</label>
                                    <input value="{{ old('facebook', $data->facebook) }}" name="facebook" placeholder="..." type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
