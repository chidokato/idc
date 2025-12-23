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

        <!-- Stats -->
        <div class="row gx-2 gx-lg-3">
          <div class="col-sm-4 col-lg-2 mb-lg-4">
            <!-- Card -->
            <a class="card card-hover-shadow h-80" href="#">
              <div class="card-body">
                <h6 class="card-subtitle">Tổng tiền thực tế</h6>
                <div class="row align-items-center gx-2 mb-1">
                  <div class="col-6">
                    <span class="card-title h2">100</span>
                  </div>
                </div>
              </div>
            </a>
            <!-- End Card -->
          </div>

          <div class="col-sm-4 col-lg-2 mb-lg-4">
            <!-- Card -->
            <a class="card card-hover-shadow h-80" href="#">
              <div class="card-body">
                <h6 class="card-subtitle">Tổng số dự án</h6>
                <div class="row align-items-center gx-2 mb-1">
                  <div class="col-6">
                    <span class="card-title h2">100</span>
                  </div>
                </div>
              </div>
            </a>
            <!-- End Card -->
          </div>

          <div class="col-sm-4 col-lg-2 mb-lg-4">
            <!-- Card -->
            <a class="card card-hover-shadow h-80" href="#">
              <div class="card-body">
                <h6 class="card-subtitle">Tổng số người dùng</h6>
                <div class="row align-items-center gx-2 mb-1">
                  <div class="col-6">
                    <span class="card-title h2">100</span>
                  </div>
                </div>
              </div>
            </a>
            <!-- End Card -->
          </div>

        </div>
          

        <div class="row gx-2 gx-lg-3">
          <div class="col-lg-12 mb-3 mb-lg-12">
            <!-- Card -->
            <div class="card h-100">
              <!-- Header -->
              <div class="card-header">
                <h5 class="card-header-title">Chi phí theo dự án</h5>

                <!-- Nav -->
                <ul class="nav nav-segment" id="expensesTab" role="tablist">
                  <li class="nav-item" data-toggle="chart-bar" data-datasets="thisWeek" data-trigger="click" data-action="toggle">
                    <a class="nav-link active" href="javascript:;" data-toggle="tab">Tháng 12.20205</a>
                  </li>
                  <li class="nav-item" data-toggle="chart-bar" data-datasets="lastWeek" data-trigger="click" data-action="toggle">
                    <a class="nav-link" href="javascript:;" data-toggle="tab">Tháng 01.20206</a>
                  </li>
                </ul>
                <!-- End Nav -->
              </div>
              <!-- End Header -->

              <!-- Body -->
              <div class="card-body">
                <div class="row mb-4">
                  <!-- <div class="col-sm mb-2 mb-sm-0">
                    <div class="d-flex align-items-center">
                      <span class="h1 mb-0">35%</span>
                      <span class="text-success ml-2">
                        <i class="tio-trending-up"></i> 25.3%
                      </span>
                    </div>
                  </div> -->

                  <div class="col-sm-auto align-self-sm-end">
                    <!-- Legend Indicators -->
                    <div class="row font-size-sm">
                      <div class="col-auto">
                        <span class="legend-indicator bg-primary"></span> Chi phí thực tế
                      </div>
                      <div class="col-auto">
                        <span class="legend-indicator"></span> Chi phí dự kiến
                      </div>
                    </div>
                    <!-- End Legend Indicators -->
                  </div>
                </div>
                <!-- End Row -->

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
      },
      "options": {
        "scales": {
          "yAxes": [{
            "gridLines": { "color": "#e7eaf3", "drawBorder": false, "zeroLineColor": "#e7eaf3" },
            "ticks": {
              "beginAtZero": true,
              "fontSize": 12,
              "fontColor": "#97a4af",
              "fontFamily": "Open Sans, sans-serif",
              "padding": 10
            }
          }],
          "xAxes": [{
            "gridLines": { "display": false, "drawBorder": false },
            "ticks": {
              "fontSize": 12,
              "fontColor": "#97a4af",
              "fontFamily": "Open Sans, sans-serif",
              "padding": 5
            },
            "categoryPercentage": 0.6,
            "barPercentage": 0.9,
            "maxBarThickness": 14
          }]
        },
        "tooltips": { "hasIndicator": true, "mode": "index", "intersect": false },
        "hover": { "mode": "nearest", "intersect": true }
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

@endsection