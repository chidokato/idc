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
      
    </div>
    <!-- End Row -->
  </div>
  <div class="card">
  <!-- Header -->
  <div class="card-header">
    <div class="row align-items-center flex-grow-1" id="filterBar">
      <div class="col-sm-2 col-md-2 mb-sm-0">
        <input type="text" name="yourname" id="filterName" class="form-control" placeholder="Tìm theo tên / mã NV / email...">
      </div>

      <div class="col-sm-3 col-md-3 mb-sm-0">
        <select name="department_id" id="filterDepartment" class="form-control select2">
          {!! $departmentOptions !!}
        </select>
      </div>
      <div class="col-sm-3 col-md-3 mb-sm-0">
        <select name="report_id" id="filterReport" class="form-control">
          @foreach($reports as $report)
            <option value="{{ $report->id }}" {{ (int)($reportId ?? 0) === (int)$report->id ? 'selected' : '' }}>
              {{ $report->name }} ({{ \Carbon\Carbon::parse($report->time_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($report->time_end)->format('d/m/Y') }})
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-sm-2 col-md-2 mb-sm-0">
        <select name="approved" id="filterApproved" class="form-control">
          <option value="1">Đã duyệt</option>
          <option value="0">Không duyệt</option>
          <option value="">Tất cả</option>
        </select>
      </div>
    </div>
    <!-- End Row -->
  </div>
  <!-- End Header -->
  <!-- Table -->
  <div class="table-responsive datatable-custom">
    <table id="taskTable" class="table table-lg table-thead-bordered table-nowrap table-align-middle card-table">
      <thead class="thead-light"> 
        <tr> 
          <th></th>
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
          <th></th>
        </tr>
        @if($tasks->count())
        <tr class="font-weight-bold bg-light">
          <td colspan="6" class="text-end">Tổng:</td>
          <td class="text-end" id="sumTotalText">{{ number_format($sumTotal, 0, ',', '.') }}</td>
          <td></td>
          <td class="text-end" id="sumPaidText">{{ number_format($sumPaid, 0, ',', '.') }}</td>
          <td colspan="3"></td>
        </tr>
        @endif
      </thead>
      <tbody id="taskTableBody">
        @include('account.task.partials.task_rows', ['tasks' => $tasks])
      </tbody>
    </table>
  </div>
  <!-- End Table -->
  </div>
</div>

@endsection


@section('js')


@endsection