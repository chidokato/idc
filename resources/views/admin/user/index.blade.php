@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')

@if ($errors->any())
<div class="alert alert-danger">
    {{ $errors->first() }}
</div>
@endif

<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý người dùng</h2>
    <a class="add-iteam" href="{{ route('users.create') }}">
        <button class="btn-success form-control" type="button">
            <i class="fa fa-plus" aria-hidden="true"></i> {{ __('lang.add') }}
        </button>
    </a>
</div>

<div class="row">
    <div class="col-xl-12 col-lg-12">
        <form method="GET" action="{{ route('users.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input
                        type="text"
                        value="{{ request('key') }}"
                        placeholder="Mã NV / tên / email / số điện thoại"
                        class="form-control"
                        name="key"
                    >
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-control" name="department_id">
                        <option value="">Tất cả phòng ban</option>
                        {!! $departmentOptions !!}
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select class="form-control" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-success mr-2">Tìm kiếm</button>
                    <a href="{{ route('users.index') }}" class="btn btn-warning">Reset</a>
                    <a href="{{ route('users.member') }}" class="btn btn-info">Danh sách member</a>
                </div>
            </div>
        </form>

        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a class="nav-link active" href="{{ route('users.index') }}">Admin</a></li>
                    <li><a class="nav-link" href="{{ route('users.member') }}">Member</a></li>
                </ul>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="tab1">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mã NV</th>
                                <th>Họ tên</th>
                                <th>Phòng / sàn / công ty</th>
                                <th>Email</th>
                                <th>Quyền</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $val)
                            <tr>
                                <td>{{ $val->id }}</td>
                                <td>{{ $val->employee_code }}</td>
                                <td>
                                    <input
                                        type="text"
                                        value="{{ $val->yourname }}"
                                        name="name"
                                        class="form-control user-name-input"
                                        data-id="{{ $val->id }}"
                                    >
                                </td>
                                <td>
                                    {{ $val->department?->name ?: '--' }} /
                                    {{ $val->departmentlv2?->name ?: '--' }} /
                                    {{ $val->departmentlv1?->name ?: '--' }}
                                </td>
                                <td>{{ $val->email }}</td>
                                <td>{{ $val->permission }}</td>
                                <td>
                                    <label class="container">
                                        <input
                                            type="checkbox"
                                            class="change-user-status"
                                            data-id="{{ $val->id }}"
                                            {{ $val->status == 'active' ? 'checked' : '' }}
                                        >
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>{{ $val->created_at }}</td>
                                <td style="display: flex;">
                                    <a href="{{ route('users.edit', [$val->id]) }}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    <form action="{{ route('users.destroy', [$val->id]) }}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button class="button_none" onclick="return confirm('Bạn muốn xóa bản ghi?')"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Không có người dùng phù hợp</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
@endsection

@section('js')
<script>
    $(document).on('change', '.change-user-status', function() {
        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 'active' : 'inactive';

        $.ajax({
            url: "{{ route('user.changeStatus') }}",
            type: 'POST',
            data: {
                id: id,
                status: status,
                _token: "{{ csrf_token() }}"
            },
            success: function() {
                showToast('success', 'Cập nhật trạng thái thành công!');
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi cập nhật!');
            }
        });
    });

    $(document).ready(function () {
        $('.user-name-input').on('change blur', function () {
            let input = $(this);
            let userId = input.data('id');
            let name = input.val();

            if (name.trim() === '') {
                showToast('error', 'Tên không được để trống!');
                return;
            }

            $.ajax({
                url: "{{ route('users.updateName') }}",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: userId,
                    yourname: name
                },
                success: function() {
                    showToast('success', 'Cập nhật tên thành công!');
                    input.css('border', '1px solid #28a745');
                },
                error: function() {
                    showToast('error', 'Có lỗi xảy ra khi cập nhật!');
                    input.css('border', '1px solid red');
                }
            });
        });

        $('.user-name-input').on('keypress', function (e) {
            if (e.which === 13) {
                $(this).blur();
            }
        });
    });
</script>
@endsection
