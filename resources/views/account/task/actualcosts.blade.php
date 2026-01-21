@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')
<style>
  .select2-selection--multiple{ height:41px }
  .select2-search__field{ height:30px }
  .s2-user{ display:flex; gap:10px; }
  .s2-user__name{
    font-weight: 600;
    line-height: 1.1;
  }
  .s2-user__pos{
    font-size: 12px;
    opacity: .7;
    margin-top: 2px;
    line-height: 1.1;
  }
</style>
@endsection

@section('body') @endsection

@section('content')
<?php $rank = (int)(auth()->user()->rank ?? 0); ?>
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
        @if($rank === 1)
          <div id="addtask" data-toggle="popover-dark">
            <a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#newProjectModal">
              <i class="tio-add mr-1"></i> New project
            </a>
          </div>
          @endif
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
                <div class="form-group">
                  <select name="report_id" class="form-control">
                  <option value="">-- Thời gian --</option>
                  @foreach($reports as $val)
                    <option value="{{ $val->id }}" {{ (string)$selectedReportId === (string)$val->id ? 'selected' : '' }}>
                      {{ \Carbon\Carbon::parse($val->time_start)->format('d/m') }} - {{ \Carbon\Carbon::parse($val->time_end)->format('d/m') }} _ {{ \Carbon\Carbon::parse($val->time_start)->format('Y') }}
                    </option>
                  @endforeach
                </select>
                </div>
              </div>

              <div class="col-sm-4 col-md-4">
                <div class="form-group">
                  <select name="handler_ids[]" class="form-control yourname2" multiple>
                    @foreach($users as $us)
                      <option value="{{ $us->id }}"
                        data-department="{{ $us->department?->name }}"
                        {{ in_array($us->id, (array) request('handler_ids', [])) ? 'selected' : '' }}>
                        {{ $us->yourname }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>


              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="approved" class="form-control select2">
                    <option value="">-- Duyệt ??</option>
                    <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>
                        Đã duyệt
                    </option>
                    <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>
                        Chưa duyệt
                    </option>
                    
                  </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="paid" class="form-control select2">
                  <option value="">-- Đóng tiền ??</option>
                  <option value="1" {{ request('paid') === '1' ? 'selected' : '' }}>
                      Đã đóng
                  </option>
                  <option value="0" {{ request('paid') === '0' ? 'selected' : '' }}>
                      Chưa đóng
                  </option>
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="settled" class="form-control select2">
                  <option value="">-- Tất toán ??</option>
                  <option value="1" {{ request('settled') === '1' ? 'selected' : '' }}>
                      Đã tất toán
                  </option>
                  <option value="0" {{ request('settled') === '0' ? 'selected' : '' }}>
                      Chưa tất toán
                  </option>
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="post_id" class="form-control select2">
                  <option value="">-- Dự án --</option>
                  @foreach($posts as $p)
                  <option value="{{ $p->id }}" {{ request('post_id') == $p->id ? 'selected' : '' }}>
                      {{ $p->name }}
                  </option>
                  @endforeach
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="department_id" class="form-control select2">
                  <option value="">-- Phòng/nhóm --</option>
                  {!! $departmentOptions !!}
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="channel_id" class="form-control select2">
                  <option value="">-- Kênh ??</option>
                  {!! $channelsOptions !!}
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="outstanding" class="form-control select2">
                  <option value="">-- Đối soát ??</option>
                  <option {{ request('outstanding') === '1' ? 'selected' : '' }} value="1">-- Đóng thêm ??</option>
                  <option {{ request('outstanding') === '0' ? 'selected' : '' }} value="0">-- Trả lại ??</option>

                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <button type="submit" class="btn btn-primary" id="btnSearch">Lọc</button>
                  <a href="{{ url()->current() }}" class="btn btn-warning" id="btnReset">Reset</a>
                </div>
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
        <th class="text-center">Họ & Tên</th>
        <th class="text-center">Phòng / nhóm</th>
        <th class="text-center">Dự án</th>
        <th class="text-center">Kênh</th>
        <th class="text-right">Tổng tiền</th>
        <th class="text-right">Tiền nộp</th>
        <th class="text-center">Đóng tiền</th>
        <th>Thực tế</th>
        <th class="text-right">Trả lại</th>
        <th class="text-right">Đóng thêm</th>
        <th class="text-center">Tất toán</th>
        <th>Ghi chú</th>
        <th colspan="2"></th>
      </tr>

      <tr id="sumRow" class="font-weight-bold bg-light" style="{{ $tasks->count() ? '' : 'display:none' }}">
        <td colspan="5"></td>
        <td class="text-right" id="sumTotalText">{{ number_format($sumTotal, 0, ',', '.') }}</td>
        <td class="text-right" id="sumPaidText">{{ number_format($sumPaid, 0, ',', '.') }}</td>
        <td class="text-center" id="">{{ number_format($sum_expected, 0, ',', '.') }}</td>
        <td class="" id="">{{ number_format($sum_actual_costs, 0, ',', '.') }}</td>
        <td class="text-right" id="">{{ number_format($sum_refund_money, 0, ',', '.') }}</td>
        <td class="text-right" id="">{{ number_format($sum_extra_money, 0, ',', '.') }}</td>
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
                <select name="post_id" class="form-control select2">
                  <option value="">-- Dự án --</option>
                  @foreach($posts as $p)
                  <option value="{{ $p->id }}" {{ request('post_id') == $p->id ? 'selected' : '' }}>
                      {{ $p->name }}
                  </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="input-label">Kênh chạy</label>
                <select name="channel_id" required class="custom-select select2">
                  <option value="">...</option>
                  {!! $channelsOptions !!}
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
$(document).ready(function () {
  $('.yourname2').select2({
    placeholder: 'Nhập Họ và Tên',
    allowClear: true,
    matcher: function (params, data) {
      if ($.trim(params.term) === '') {
        return data;
      }

      if (typeof data.text === 'undefined') {
        return null;
      }

      let term = params.term.toLowerCase();
      let text = data.text.toLowerCase();
      let department = $(data.element).data('department')?.toLowerCase() || '';

      if (text.includes(term) || department.includes(term)) {
        return data;
      }

      return null;
    }
  });
});
</script>

@endsection