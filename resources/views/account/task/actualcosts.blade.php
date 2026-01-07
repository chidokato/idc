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
        <input type="text" name="name" class="form-control" placeholder="...">
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
        @forelse($tasks as $task)
      @php
        // dùng lại cách ép số giống controller (nhanh gọn ở đây)
        $cost = (float) preg_replace('/[^\d\-]/', '', (string)($task->expected_costs ?? 0));
        $days = (float) preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', (string)($task->days ?? 0)));
        $rate = (float) preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', (string)($task->rate ?? 0)));
        $rowTotal = $cost * $days;
        $rowPaid  = $rowTotal * (1 - $rate/100);
      @endphp
  <tr>
    <td>
      @if($task->approved == 1)
          <span class="badge btn-success">Duyệt</span>
      @else
          <span class="badge btn-danger">Không</span>
      @endif
    </td>
    <td>{{ $task->handler?->employee_code }}</td>
    <td>{{ $task->handler?->yourname }}</td>
    <td>{{ $task->department?->name }}</td>
    <td>{{ $task->Post?->name }}</td>
    <td>{{ $task->channel?->name ?? $task->channel ?? '' }}</td>

    <td class="text-end">
      {{ number_format((float)($task->expected_costs * $task->days), 0, ',', ',') }}
    </td>

    <td class="text-end">
      @if(($task->paid ?? 0) == 1)
      <div class="note text-success" data-toggle="tooltip" data-placement="left" title="" data-original-title="{{ (int) $task->rate }}%">
        {{ number_format((float)(($task->expected_costs * $task->days) * (1 - $task->rate/100)), 0, ',', ',') }}
      </div>
      @else
      <div class="note text-danger" data-toggle="tooltip" data-placement="left" title="" data-original-title="{{ (int) $task->rate }}%">
        {{ number_format((float)(($task->expected_costs * $task->days) * (1 - $task->rate/100)), 0, ',', ',') }}
      </div>
      @endif
    </td>

    <td>
      <div class="note" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $task->content ?? '' }}">
        {{ $task->content ?? '' }}
      </div>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="11" class="text-center text-muted py-4">Không có dữ liệu phù hợp</td>
  </tr>
@endforelse


      </tbody>
    </table>
  </div>
  <!-- End Table -->
  </div>
</div>

@endsection


@section('js')



@endsection