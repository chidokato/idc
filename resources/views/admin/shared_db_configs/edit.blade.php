@extends('admin.layout.main')

@section('content')
@include('admin.alert')

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0 text-gray-800 font-weight-bold">CHỈNH SỬA CẤU HÌNH DB DÙNG CHUNG</h1>
        <ol class="breadcrumb mb-0 bg-transparent p-0 small">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.shared-db-configs.index') }}">Cấu hình DB dùng chung</a></li>
            <li class="breadcrumb-item active">{{ $config->name }}</li>
        </ol>
    </div>

    <form action="{{ route('admin.shared-db-configs.update', $config) }}" method="POST">
        @method('PUT')
        @include('admin.shared_db_configs._form', ['config' => $config])

        <div class="d-flex align-items-center justify-content-end mt-3 mb-4">
            <a href="{{ route('admin.shared-db-configs.index') }}" class="btn btn-light mr-2">Hủy bỏ</a>
            <button type="submit" class="btn btn-primary">
                <i class="far fa-save mr-1"></i> Cập nhật cấu hình DB
            </button>
        </div>
    </form>
</div>
@endsection
