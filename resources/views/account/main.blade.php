@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')

@endsection

@section('body') @endsection

@section('content')

<div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
          <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
              <h1 class="page-header-title">Trang chủ</h1>
            </div>

          </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
          <div class="col-lg-12 mb-3 mb-lg-12">
            <!-- Card -->
            <div class="card h-100">
              <!-- Header -->
              <div class="card-header">
                <h5 class="card-header-title">Chi phí theo dự án</h5>

                <!-- Nav -->
                <ul class="nav nav-segment" id="expensesTab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="" data-toggle="tab">Tháng 12.20205</a>
                  </li>
                  
                </ul>
                <!-- End Nav -->
              </div>
              <!-- End Header -->

              <!-- Body -->
              <div class="card-body">
                <!-- Bar Chart -->
                <div class="chartjs-custom">
                  <canvas id="updatingData" style="height: 20rem;"
                    data-hs-chartjs-options='{
                      "type": "bar",
                      "data": {
                        "labels": {!! json_encode($chartLabels, JSON_UNESCAPED_UNICODE) !!},
                        "datasets": [{
                          "label": "Chi phí dự kiến",
                          "data": {!! json_encode($dataExpected) !!},
                          "backgroundColor": "#377dff",
                          "hoverBackgroundColor": "#377dff",
                          "borderColor": "#377dff"
                        },{
                          "label": "Chi phí thực tế",
                          "data": {!! json_encode($dataActual) !!},
                          "backgroundColor": "#e7eaf3",
                          "hoverBackgroundColor": "#e7eaf3",
                          "borderColor": "#e7eaf3"
                        }]
                      }
                    }'>
                  </canvas>
                </div>
                <!-- End Bar Chart -->
              </div>
              <!-- End Body -->
            </div>
            <!-- End Card -->
          </div>
        </div>
        <!-- End Row -->

        <!-- Card -->

        
      </div>

@endsection


@section('js')

<script>
(function () {
  function fmtVND(v) {
    const n = Number(v) || 0;
    return n.toLocaleString('vi-VN') + ' ₫';
  }

  // Chart.js v2
  if (window.Chart && Chart.pluginService) {
    Chart.pluginService.register({
      beforeInit: function(chart) {
        // Y ticks
        if (chart.options?.scales?.yAxes) {
          chart.options.scales.yAxes.forEach(ax => {
            ax.ticks = ax.ticks || {};
            ax.ticks.callback = function(value){ return fmtVND(value); };
          });
        }

        // Tooltip
        chart.options.tooltips = chart.options.tooltips || {};
        chart.options.tooltips.callbacks = chart.options.tooltips.callbacks || {};
        chart.options.tooltips.callbacks.label = function(tooltipItem, data) {
          const label = (data.datasets[tooltipItem.datasetIndex]?.label || '');
          return label + ': ' + fmtVND(tooltipItem.yLabel);
        };
      }
    });
    return;
  }

  // Chart.js v3+
  if (window.Chart && Chart.register) {
    Chart.register({
      id: 'vndFormatter',
      beforeInit(chart) {
        // Y ticks (v3)
        const y = chart.options?.scales?.y;
        if (y?.ticks) {
          y.ticks.callback = (value) => fmtVND(value);
        }

        // Tooltip (v3)
        const tt = chart.options?.plugins?.tooltip;
        if (tt) {
          tt.callbacks = tt.callbacks || {};
          tt.callbacks.label = (ctx) => {
            const label = ctx.dataset?.label || '';
            return label + ': ' + fmtVND(ctx.parsed.y);
          };
        }
      }
    });
  }
})();
</script>


@endsection