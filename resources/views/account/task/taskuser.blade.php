@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')

@endsection

@section('body') @endsection

@section('content')

<div class="content container-fluid">
  <div class="page-header">
    <div class="row align-items-end">
      <div class="col-sm mb-2 mb-sm-0">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-no-gutter">
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="account/main">Account</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách link đăng ký MKT</li>
          </ol>
        </nav>

        <h1 class="page-header-title">Danh sách link đăng ký MKT</h1>
      </div>

      <!-- <div class="col-sm-auto">
        <a class="btn btn-primary" href="users-add-user.html">
          <i class="tio-user-add mr-1"></i> Add user
        </a>
      </div> -->
    </div>
    <!-- End Row -->
  </div>

  <div class="card">
  <!-- Header -->
  <div class="card-header">
  <div class="row justify-content-between align-items-center flex-grow-1">
  <div class="col-sm-6 col-md-4 mb-3 mb-sm-0">
  <form>
  <!-- Search -->
  <div class="input-group input-group-merge input-group-flush">
  <div class="input-group-prepend">
  <div class="input-group-text">
  <i class="tio-search"></i>
  </div>
  </div>
  <input id="datatableSearch" type="search" class="form-control" placeholder="Search users" aria-label="Search users">
  </div>
  <!-- End Search -->
  </form>
  </div>
  <div class="col-sm-6">
  <div class="d-sm-flex justify-content-sm-end align-items-sm-center">
  <!-- Datatable Info -->
  <div id="datatableCounterInfo" class="mr-2 mb-2 mb-sm-0" style="display: none;">
  <div class="d-flex align-items-center">
  <span class="font-size-sm mr-3">
  <span id="datatableCounter">0</span>
  Selected
  </span>
  <a class="btn btn-sm btn-outline-danger" href="javascript:;">
  <i class="tio-delete-outlined"></i> Delete
  </a>
  </div>
  </div>
  <!-- End Datatable Info -->
  </div>
  </div>
  </div>
  <!-- End Row -->
  </div>
  <!-- End Header -->
  <!-- Table -->
  <div class="table-responsive datatable-custom">
  <table class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
  <thead class="thead-light">
  <tr>
    <th class="table-column-pr-0">
    <div class="custom-control custom-checkbox">
    <input id="datatableCheckAll" type="checkbox" class="custom-control-input">
    <label class="custom-control-label" for="datatableCheckAll"></label>
    </div>
    </th>
    <th>Mã NV</th>
    <th>Họ & Tên</th>
    <th>Phòng / nhóm</th>
    <th>Dự án</th>
    <th>Kênh</th>
    <th>Tổng tiền</th>
    <th>Hỗ trợ</th>
    <th>Tiền nộp</th>
    <th>Đóng tiền</th>
    <th>Ghi chú</th>
  </tr>
  </thead>
  <tbody>
@foreach($tasks as $task)
  <tr>
    <td class="table-column-pr-0">
      <div class="custom-control custom-checkbox">
        <input id="datatableCheck{{ $task->id }}" type="checkbox" class="custom-control-input row-check" value="{{ $task->id }}">
        <label class="custom-control-label" for="datatableCheck{{ $task->id }}"></label>
      </div>
    </td>

    <td>{{ $task->handler?->employee_code }}</td>

    <td>{{ $task->handler?->yourname }}</td>

    <td>
      {{ $task->department?->name }}
    </td>

    <td>{{ $task->Post?->name }}</td>

    <td>{{ $task->channel?->name ?? $task->channel ?? '' }}</td>

    <td class="text-end">
      {{ number_format((float)($task->expected_costs * $task->days), 0, ',', '.') }}
    </td>

    <td class="text-end">
     {{ $task->rate }}%
    </td>

    <td class="text-end">
      {{ number_format((float)( ($task->expected_costs * $task->days) * (1-$task->rate/100)  ), 0, ',', '.') }}
    </td>

    {{-- Đóng tiền (trạng thái) --}}
    <td>
      @if(($task->paid ?? 0) == 1)
        <span class="badge badge-soft-success">Đã đóng</span>
      @else
        <span class="badge badge-soft-warning">Chưa đóng</span>
      @endif
    </td>

    {{-- Ghi chú --}}
    <td>{{ $task->note ?? '' }}</td>
  </tr>
@endforeach
</tbody>

  </table>
  </div>
  <!-- End Table -->
  </div>
</div>

@endsection


@section('js')

@endsection