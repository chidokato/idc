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
      <div class="col-sm-auto">
          <div id="addtask" data-toggle="popover-dark">
            <a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#newProjectModal">
              <i class="tio-add mr-1"></i> New project
            </a>
          </div>
        </div>
    </div>
    <!-- End Row -->
  </div>
  <div class="card overflow-hidden mb-3 mb-lg-5">
    <div class="card-header">
      <div class="row align-items-sm-center flex-grow-1">
        <div class="col-sm mb-2 mb-sm-0">
          <form id="filterForm" method="GET" action="{{ url()->current() }}">
            <div class="row" id="filterBar">
              <div class="col-sm-2 col-md-2">
                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Mã NV / Họ tên"
                       value="{{ request('name') }}">
              </div>
              <div class="col-sm-3 col-md-3">
                <select name="department_id" class="form-control select2">
                  <option value="">-- Phòng/nhóm --</option>
                  {!! $departmentOptions !!}
                </select>
              </div>
              <div class="col-sm-3 col-md-3">
                <select name="report_id" class="form-control">
                  <option value="">-- Báo cáo --</option>
                  @foreach($reports as $val)
                    <option value="{{ $val->id }}" {{ (string)$selectedReportId === (string)$val->id ? 'selected' : '' }}>
                      {{ \Carbon\Carbon::parse($val->time_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($val->time_end)->format('d/m/Y') }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-sm-2 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="btnSearch">Lọc</button>
                <a href="{{ url()->current() }}" class="btn btn-warning" id="btnReset">Reset</a>
              </div>

            </div>
          </form>
        </div>
        
      </div>
      <!-- End Row -->
    </div>


  <!-- Header -->

  <!-- End Header -->
  <!-- Table -->
  <div class="table-responsive datatable-custom">
  <table id="taskTable" class="table table-lg table-thead-bordered table-nowrap table-align-middle card-table">
    <thead class="thead-light">
      <tr>
        <th>Duyệt</th>
        <th>Mã NV</th>
        <th>Họ & Tên</th>
        <th>Phòng / nhóm</th>
        <th>Dự án</th>
        <th>Kênh</th>
        <th class="text-end">Tổng tiền</th>
        <th class="text-end">Tiền nộp</th>
        <th class="text-end">Tất toán</th>
        <th>Thực tế</th>
        <th class="text-end" title="Nộp thêm/Trả lại">Nộp/Trả</th>
        <th>Ghi chú</th>
        <th colspan="2"></th>
      </tr>

      <tr id="sumRow" class="font-weight-bold bg-light" style="{{ $tasks->count() ? '' : 'display:none' }}">
        <td colspan="6" class="text-end"></td>
        <td class="text-end" id="sumTotalText">{{ number_format($sumTotal, 0, ',', '.') }}</td>
        <td class="text-end" id="sumPaidText">{{ number_format($sumPaid, 0, ',', '.') }}</td>
        <td class="text-end" id="">{{ number_format($sumActual, 0, ',', '.') }}</td>
        <td colspan="4"></td>
      </tr>
    </thead>
    @php
      $canBulkEdit = auth()->check() && in_array(auth()->user()->rank, [1,2]);
    @endphp
    <tbody id="taskTableBody">
      @include('account.task.partials._rows', ['tasks' => $tasks])
    </tbody>
  </table>
</div>

  </div>
</div>

<div class="modal fade" id="newProjectModal" tabindex="-1" role="dialog" aria-labelledby="editCardModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h4 id="editCardModalTitle" class="modal-title">Thêm mới</h4>
        <button type="button" class="btn btn-icon btn-sm btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
          <i class="tio-clear tio-lg"></i>
        </button>
      </div>

      {{-- FORM --}}
      <form id="createTaskForm" method="POST" action="{{ route('account.task.store') }}">
        @csrf
        <input type="hidden" name="addreport_id" value="{{ (string)$selectedReportId }}">
        {{-- URL hiện tại (có cả filter + page) để redirect về đúng chỗ --}}
        <input type="hidden" name="redirect_url" value="{{ url()->full() }}">

        <div class="modal-body">
          <div class="row">

            <div class="col-sm-3">
              <div class="form-group">
                <label class="input-label">Họ tên nhân viên</label>
                <select name="user_id" required class="custom-select select2">
                  <option value="">...</option>
                  @foreach($users as $val)
                    <option value="{{ $val->id }}">{{ $val->yourname }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="input-label">Dự án</label>
                <select name="post_id" required class="custom-select select2">
                  <option value="">...</option>
                  @foreach($posts as $val)
                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="input-label">Kênh chạy</label>
                <select name="channel_id" required class="custom-select select2">
                  <option value="">...</option>
                  @foreach($channels as $val)
                    <option value="{{ $val->id }}" {{ $val->name == 'Facebook' ? 'selected' : '' }}>
                      {{ $val->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label for="expected_costs" class="input-label">Số tiền</label>
                <div class="input-group input-group-merge">
                  <input
                    type="text"
                    class="form-control actual-cost-input"
                    name="expected_costs"
                    id="expected_costs"
                    value="500000"
                    placeholder="Số tiền"
                  >
                </div>
              </div>
            </div>

          </div>

          <div class="d-flex align-items-center">
            <div class="ml-auto">
              <button type="submit" class="btn btn-primary" id="btnCreateTask">
                <i class="tio-save"></i> Lưu lại
              </button>
            </div>
          </div>
        </div>
      </form>
      {{-- END FORM --}}

    </div>
  </div>
</div>


@endsection


@section('js')
<script src="admin_asset/select2/js/select2.min.js"></script>
<script src="admin_asset/select2/js/select2-searchInputPlaceholder.js"></script>
<script src="account/js/account.js"></script>

<script>

</script>

@endsection