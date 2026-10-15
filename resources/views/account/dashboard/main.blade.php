@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')
@endsection

@section('body') @endsection

@section('content')
<div class="content container-fluid">
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-sm mb-2 mb-sm-0">
        <h1 class="page-header-title">Trang chủ</h1>
      </div>
      <div class="col-sm-auto d-none d-sm-block">
        <form method="GET" action="{{ route('account.dashboard.main') }}">
          <select name="month" class="form-control" onchange="this.form.submit()">
            <option value="" {{ $selectedMonth === null ? 'selected' : '' }}>Tất cả các tháng</option>
            @foreach($monthOptions as $monthValue => $monthLabel)
              <option value="{{ $monthValue }}" {{ (int) $selectedMonth === (int) $monthValue ? 'selected' : '' }}>
                {{ $monthLabel }}/{{ $statisticalYear }}
              </option>
            @endforeach
          </select>
      </form>
      </div>
      
    </div>

  </div>

  <div class="row mb-3">
    <div class="col-md-4 mb-3">
      <div class="card card-body">
        <span class="text-muted text-uppercase font-size-xs">
          {{ $selectedMonth === null ? 'Tổng chi phí thực tế từ tháng 1 đến tháng ' . count($monthOptions) . '/' . $statisticalYear : 'Tổng chi phí thực tế tháng ' . $selectedMonth . '/' . $statisticalYear }}
        </span>
        <h3 class="mb-0">{{ number_format((float) ($summary->total_actual_costs ?? 0), 0, ',', '.') }}</h3>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card card-body">
        <span class="text-muted text-uppercase font-size-xs">
          {{ $selectedMonth === null ? 'Tổng bù thêm từ tháng 1 đến tháng ' . count($monthOptions) . '/' . $statisticalYear : 'Tổng bù thêm tháng ' . $selectedMonth . '/' . $statisticalYear }}
        </span>
        <h3 class="mb-0 text-danger">{{ number_format((float) ($summary->total_extra_money ?? 0), 0, ',', '.') }}</h3>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card card-body">
        <span class="text-muted text-uppercase font-size-xs">
          {{ $selectedMonth === null ? 'Tổng hoàn lại từ tháng 1 đến tháng ' . count($monthOptions) . '/' . $statisticalYear : 'Tổng hoàn lại tháng ' . $selectedMonth . '/' . $statisticalYear }}
        </span>
        <h3 class="mb-0 text-success">{{ number_format((float) ($summary->total_refund_money ?? 0), 0, ',', '.') }}</h3>
      </div>
    </div>
  </div>

  <!-- Biểu đồ chi phí các tháng -->
  <div class="card mb-3">
    <div class="card-header">
      <div class="row align-items-center flex-grow-1">
        <div class="col">
          <h4 class="card-header-title mb-0">Biểu đồ chi phí các tháng năm {{ $statisticalYear }}</h4>
          <small class="text-muted">
            @if($selectedMonth !== null)
              Đang lọc: Tháng {{ $selectedMonth }}/{{ $statisticalYear }} (nhấp vào cột để chuyển tháng hoặc bỏ lọc)
            @else
              Mỗi cột gồm 2 phần: Chi phí Sale trả (Tiền nộp + Đóng thêm - Trả lại) & Chi phí Công ty chi
            @endif
          </small>
        </div>
        <div class="col-auto">
          <div class="d-flex align-items-center font-size-sm">
            <span class="d-inline-block rounded-circle mr-1" style="width: 10px; height: 10px; background-color: #377dff;"></span>
            <span class="mr-3">Chi phí Công ty chi</span>
            <span class="d-inline-block rounded-circle mr-1" style="width: 10px; height: 10px; background-color: #00c9a7;"></span>
            <span>Chi phí Sale trả</span>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div style="position: relative; height: 320px;">
        <canvas id="monthlyExpenseChart"></canvas>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-4 mb-3">
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
                <th class="text-right">Chi phí thực tế</th>
              </tr>
            </thead>
            <tbody>
              @forelse($projectSummaries as $row)
                <tr>
                  <td>{{ $row->post_name }}</td>
                  <td class="text-right">{{ number_format((float) $row->total_actual_costs, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="text-center text-muted">Chưa có dữ liệu theo dự án.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-4 mb-3">
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
                <th class="text-right">Chi phí thực tế</th>
              </tr>
            </thead>
            <tbody>
              @forelse($departmentSummaries as $row)
                <tr>
                  <td>
                    {{ $row->department_name }}
                    @if(!empty($row->parent_department_name))
                      <small class="text-muted">/ {{ $row->parent_department_name }}</small>
                    @endif
                  </td>
                  <td class="text-right">{{ number_format((float) $row->total_actual_costs, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="text-center text-muted">Chưa có dữ liệu theo nhóm/phòng.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-4 mb-3">
      <div class="card mb-3">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h4 class="card-header-title mb-0">Chi phí công ty</h4>
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
                <th class="text-right">Chi phí thực tế</th>
              </tr>
            </thead>
            <tbody>
              @forelse($companySummaries as $row)
                <tr>
                  <td>{{ $row->company_name }}</td>
                  <td class="text-right">{{ number_format((float) $row->total_actual_costs, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="text-center text-muted">Chưa có dữ liệu theo công ty.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

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
                <th class="text-right">Chi phí thực tế</th>
              </tr>
            </thead>
            <tbody>
              @forelse($floorSummaries as $row)
                <tr>
                  <td>{{ $row->floor_name }}</td>
                  <td class="text-right">{{ number_format((float) $row->total_actual_costs, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="text-center text-muted">Chưa có dữ liệu theo sàn.</td>
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
<script>
  $(document).ready(function () {
    var canvas = document.getElementById('monthlyExpenseChart');
    if (!canvas || typeof Chart === 'undefined') return;

    var chartLabels = @json($chartMonthlyLabels);
    var dataActual = @json($chartMonthlyActual);
    var dataSale = @json($chartMonthlySale);
    var dataCompany = @json($chartMonthlyCompany);
    var selectedMonth = @json($selectedMonth);

    // Dynamic bar colors: highlight selected month if filtered
    var bgSale = dataSale.map(function (val, idx) {
      var monthNum = idx + 1;
      if (!selectedMonth) return '#00c9a7';
      return (monthNum === parseInt(selectedMonth)) ? '#00c9a7' : '#86efac';
    });

    var bgCompany = dataCompany.map(function (val, idx) {
      var monthNum = idx + 1;
      if (!selectedMonth) return '#377dff';
      return (monthNum === parseInt(selectedMonth)) ? '#377dff' : '#93c5fd';
    });

    var maxVal = Math.max.apply(null, dataActual.concat([0]));
    var yMax = maxVal > 0 ? Math.ceil(maxVal * 1.15) : 1000000;

    var ctx = canvas.getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: chartLabels,
        datasets: [
          {
            label: 'Chi phí Sale trả',
            data: dataSale,
            backgroundColor: bgSale,
            hoverBackgroundColor: '#00a386',
            borderColor: '#00c9a7',
            borderWidth: 1,
            maxBarThickness: 36
          },
          {
            label: 'Chi phí Công ty chi',
            data: dataCompany,
            backgroundColor: bgCompany,
            hoverBackgroundColor: '#2768e9',
            borderColor: '#377dff',
            borderWidth: 1,
            maxBarThickness: 36
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: false
        },
        scales: {
          xAxes: [{
            stacked: true,
            gridLines: {
              display: false,
              drawBorder: false
            },
            ticks: {
              fontSize: 12,
              fontColor: '#677788',
              fontFamily: 'Open Sans, sans-serif',
              padding: 5
            },
            categoryPercentage: 0.6,
            barPercentage: 0.8
          }],
          yAxes: [{
            stacked: true,
            gridLines: {
              color: '#e7eaf3',
              drawBorder: false,
              zeroLineColor: '#e7eaf3'
            },
            ticks: {
              beginAtZero: true,
              max: yMax,
              fontSize: 12,
              fontColor: '#97a4af',
              fontFamily: 'Open Sans, sans-serif',
              padding: 10,
              callback: function (value) {
                if (value >= 1e9) {
                  return (value / 1e9).toFixed(1) + ' tỷ';
                }
                if (value >= 1e6) {
                  return (value / 1e6).toFixed(0) + ' tr';
                }
                return Number(value).toLocaleString('vi-VN');
              }
            }
          }]
        },
        tooltips: {
          mode: 'index',
          intersect: false,
          callbacks: {
            label: function (tooltipItem, data) {
              var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
              var val = tooltipItem.yLabel || 0;
              return datasetLabel + ': ' + Number(val).toLocaleString('vi-VN') + ' đ';
            },
            footer: function (tooltipItems, data) {
              var total = 0;
              tooltipItems.forEach(function (item) {
                total += Number(item.yLabel || 0);
              });
              return 'Tổng thực tế: ' + Number(total).toLocaleString('vi-VN') + ' đ';
            }
          }
        },
        onClick: function (evt, elements) {
          if (elements && elements.length > 0) {
            var index = elements[0]._index;
            var clickedMonth = index + 1;
            var url = new URL(window.location.href);
            if (parseInt(selectedMonth) === clickedMonth) {
              url.searchParams.delete('month');
            } else {
              url.searchParams.set('month', clickedMonth);
            }
            window.location.href = url.toString();
          }
        }
      }
    });
  });
</script>
@endsection
