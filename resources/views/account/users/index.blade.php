@extends('account.layout.index')

@section('css')
<style>
  @media (min-width: 992px) {
    .user-filter-row {
      display: grid;
      grid-template-columns: minmax(280px, 2.2fr) minmax(220px, 1.4fr) minmax(190px, 1.2fr) auto;
      gap: 12px;
      align-items: end;
    }

    .user-filter-row > div {
      max-width: none;
      width: 100%;
      padding-right: 0;
      padding-left: 0;
    }

    .user-filter-row .user-filter-actions .d-flex {
      flex-wrap: nowrap !important;
    }

    .user-filter-row .mb-2 {
      margin-bottom: 0 !important;
    }
  }
</style>
@endsection

@section('content')
<div class="content container-fluid">
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-sm mb-2 mb-sm-0">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-no-gutter">
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ route('account.main') }}">Account</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
          </ol>
        </nav>
        <h1 class="page-header-title">{{ $pageTitle }}</h1>
      </div>
      <div class="col-sm-auto">
        <a class="btn btn-primary" href="{{ route('account.users.create', ['type' => $type]) }}">
          <i class="tio-add mr-1"></i> Thêm người dùng
        </a>
      </div>
    </div>
  </div>

  <div class="mb-3">
    <div class="btn-group" role="group" aria-label="User type switch">
      <a href="{{ route('account.users.index') }}" class="btn {{ $type === 'admin' ? 'btn-primary' : 'btn-white' }}">Admin</a>
      <a href="{{ route('account.users.members') }}" class="btn {{ $type === 'member' ? 'btn-primary' : 'btn-white' }}">Member</a>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <form method="GET" action="{{ $type === 'member' ? route('account.users.members') : route('account.users.index') }}">
        <div class="row align-items-end user-filter-row">
          <div class="col-12 col-md-6 mb-2 user-filter-key">
            <input type="text" name="key" value="{{ request('key') }}" class="form-control" placeholder="Mã NV / tên / email / email phụ / số điện thoại">
          </div>
          <div class="col-12 col-md-6 mb-2 user-filter-department">
            <select class="form-control" name="department_id">
              <option value="">Tất cả phòng ban</option>
              {!! $departmentOptions !!}
            </select>
          </div>
          <div class="col-12 col-md-4 mb-2 user-filter-status">
            <select class="form-control" name="status">
              <option value="">Tất cả trạng thái</option>
              <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
              <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
            </select>
          </div>
          <div class="col-12 col-md-8 mb-2 user-filter-actions">
            <div class="d-flex flex-wrap">
              <button type="submit" class="btn btn-primary mr-2 mb-2">Tìm kiếm</button>
              <a href="{{ $type === 'member' ? route('account.users.members') : route('account.users.index') }}" class="btn btn-white mb-2">Reset</a>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-lg table-thead-bordered table-nowrap table-align-middle card-table">
        <thead class="thead-light">
          <tr>
            <th>ID</th>
            <th>Mã NV</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Email phụ</th>
            <th>Phòng / nhóm</th>
            <th>Sàn / chi nhánh</th>
            <th>Công ty</th>
            <th>Chức vụ</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th class="text-right"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->employee_code }}</td>
            <td>{{ $item->yourname }}</td>
            <td>{{ $item->email }}</td>
            <td>{{ $item->secondary_email ?: '--' }}</td>
            <td>{{ $item->department?->name ?: '--' }}</td>
            <td>{{ $item->departmentlv2?->name ?: '--' }}</td>
            <td>{{ $item->departmentlv1?->name ?: '--' }}</td>
            <td>
              @if((int) $item->rank === 1)
                Giám đốc
              @elseif((int) $item->rank === 2)
                Trưởng phòng
              @elseif((int) $item->rank === 3)
                Nhân viên
              @else
                --
              @endif
            </td>
            <td>
              <span class="badge badge-soft-{{ $item->status === 'active' ? 'success' : 'secondary' }}">
                {{ $item->status === 'active' ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
              </span>
            </td>
            <td>{{ $item->created_at }}</td>
            <td class="text-right">
              <a href="{{ route('account.users.edit', $item) }}" class="btn btn-sm btn-white">Sửa</a>
              <form action="{{ route('account.users.destroy', $item) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn muốn xóa người dùng này?')">Xóa</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="12" class="text-center text-muted">Không có người dùng phù hợp.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer">
      <div class="row align-items-center">
        <div class="col-sm mb-2 mb-sm-0 text-muted">
          Hiển thị {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} / {{ $users->total() }} bản ghi
        </div>
        <div class="col-sm-auto">
          {{ $users->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
