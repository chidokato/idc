@extends('account.layout.index')

@section('title') Thống kê chi phí @endsection

@section('css')
@endsection

@section('body') @endsection

@section('content')
<div class="content container-fluid">
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-sm mb-2 mb-sm-0">
        <h1 class="page-header-title">Thống kê chi phí</h1>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('task_cost_period.index') }}">
        <div class="row align-items-end">
          <div class="col-lg-8 mb-3">
            <label class="input-label">Chọn kỳ report</label>
            <select name="report_ids[]" class="form-control js-report-select" multiple>
              @foreach($reports as $rep)
                <option value="{{ $rep->id }}" {{ collect($selectedReportIds)->contains((int) $rep->id) ? 'selected' : '' }}>
                  {{ $rep->name }}
                  @if($rep->time_start || $rep->time_end)
                    ({{ $rep->time_start }} - {{ $rep->time_end }})
                  @endif
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-4 mb-3 d-flex">
            <button type="submit" class="btn btn-primary mr-2">Xem thống kê</button>
            <a href="{{ route('task_cost_period.index') }}" class="btn btn-white">Làm mới</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-4 mb-3">
      <div class="card card-body">
        <span class="text-muted text-uppercase font-size-xs">Tổng chi phí thực tế</span>
        <h3 class="mb-0">{{ number_format((float) ($summary->total_actual_costs ?? 0), 0, ',', '.') }}</h3>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card card-body">
        <span class="text-muted text-uppercase font-size-xs">Tổng bù thêm</span>
        <h3 class="mb-0 text-danger">{{ number_format((float) ($summary->total_extra_money ?? 0), 0, ',', '.') }}</h3>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card card-body">
        <span class="text-muted text-uppercase font-size-xs">Tổng hoàn lại</span>
        <h3 class="mb-0 text-success">{{ number_format((float) ($summary->total_refund_money ?? 0), 0, ',', '.') }}</h3>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6 mb-3">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h4 class="card-header-title mb-0">Chi phí theo dự án</h4>
            <h4 class="text-right mb-0">
              <strong>{{ number_format((float) ($projectTotalActualCosts ?? 0), 0, ',', '.') }}</strong>
            </h4>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-lg table-thead-bordered table-align-middle card-table">
            <thead class="thead-light">
              <tr>
                <th>Dự án</th>
                <th class="text-center">Task</th>
                <th class="text-center">Kỳ</th>
                <th class="text-right">Chi phí thực tế</th>
              </tr>
            </thead>
            <tbody>
              @forelse($projectSummaries as $row)
                <tr>
                  <td>{{ $row->post_name }}</td>
                  <td class="text-center">{{ $row->total_tasks }}</td>
                  <td class="text-center">{{ $row->total_reports }}</td>
                  <td class="text-right">{{ number_format((float) $row->total_actual_costs, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">Chưa có dữ liệu theo dự án.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-6 mb-3">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h4 class="card-header-title mb-0">Chi phí theo nhóm/phòng</h4>
            <h4 class="text-right mb-0">
              <strong>{{ number_format((float) ($departmentTotalActualCosts ?? 0), 0, ',', '.') }}</strong>
            </h4>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-lg table-thead-bordered table-align-middle card-table">
            <thead class="thead-light">
              <tr>
                <th>Nhóm / Phòng</th>
                <th class="text-center">Task</th>
                <th class="text-center">Kỳ</th>
                <th class="text-right">Chi phí thực tế</th>
              </tr>
            </thead>
            <tbody>
              @forelse($departmentSummaries as $row)
                <tr>
                  <td>
                    {{ $row->department_name }} / 
                    @if(!empty($row->parent_department_name))
                      <small class="text-muted">{{ $row->parent_department_name }}</small>
                    @endif
                  </td>
                  <td class="text-center">{{ $row->total_tasks }}</td>
                  <td class="text-center">{{ $row->total_reports }}</td>
                  <td class="text-right">{{ number_format((float) $row->total_actual_costs, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">Chưa có dữ liệu theo nhóm/phòng.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-6 mb-3">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h4 class="card-header-title mb-0">Chi phí Công ty</h4>
            <h4 class="text-right mb-0">
              <strong>{{ number_format((float) ($companyTotalActualCosts ?? 0), 0, ',', '.') }}</strong>
            </h4>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-lg table-thead-bordered table-align-middle card-table">
            <thead class="thead-light">
              <tr>
                <th>Công ty</th>
                <th class="text-center">Task</th>
                <th class="text-center">Kỳ</th>
                <th class="text-right">Chi phí thực tế</th>
              </tr>
            </thead>
            <tbody>
              @forelse($companySummaries as $row)
                <tr>
                  <td>{{ $row->company_name }}</td>
                  <td class="text-center">{{ $row->total_tasks }}</td>
                  <td class="text-center">{{ $row->total_reports }}</td>
                  <td class="text-right">{{ number_format((float) $row->total_actual_costs, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">Chưa có dữ liệu theo Công ty.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-6 mb-3">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h4 class="card-header-title mb-0">Chi phí theo sàn / chi nhánh</h4>
            <h4 class="text-right mb-0">
              <strong>{{ number_format((float) ($floorTotalActualCosts ?? 0), 0, ',', '.') }}</strong>
            </h4>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-lg table-thead-bordered table-align-middle card-table">
            <thead class="thead-light">
              <tr>
                <th>Sàn</th>
                <th class="text-center">Task</th>
                <th class="text-center">Kỳ</th>
                <th class="text-right">Chi phí thực tế</th>
              </tr>
            </thead>
            <tbody>
              @forelse($floorSummaries as $row)
                <tr>
                  <td>{{ $row->floor_name }}</td>
                  <td class="text-center">{{ $row->total_tasks }}</td>
                  <td class="text-center">{{ $row->total_reports }}</td>
                  <td class="text-right">{{ number_format((float) $row->total_actual_costs, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">Chưa có dữ liệu theo sàn.</td>
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

@section('js')
<script src="admin_asset/select2/js/select2.min.js"></script>
<script>
$(document).ready(function () {
  $('.js-report-select').select2({
    width: '100%',
    placeholder: 'Chọn một hoặc nhiều kỳ report'
  });
});
</script>
@endsection
