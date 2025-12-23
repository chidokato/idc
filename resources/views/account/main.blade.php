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

                </div>
                <!-- End Row -->

                <!-- Bar Chart -->
                <div class="chartjs-custom">
                  <canvas id="updatingData" style="height: 10rem;"></canvas>
                  <script>
                  document.addEventListener('DOMContentLoaded', function () {
                    const labels       = @json($chartLabels, JSON_UNESCAPED_UNICODE);
                    const dataExpected = @json($dataExpected);
                    const dataActual   = @json($dataActual);

                    const canvas = document.getElementById('updatingData');
                    if (!canvas || typeof Chart === 'undefined') return;

                    // Tính trần Y để cột không chạm nóc (chừa 20%)
                    const maxVal = Math.max(...dataExpected, ...dataActual, 0);
                    const yMax   = Math.ceil(maxVal * 1.2);

                    const ctx = canvas.getContext('2d');

                    new Chart(ctx, {
                      type: 'bar',
                      data: {
                        labels: labels,
                        datasets: [
                          {
                            label: 'Chi phí dự kiến',
                            data: dataExpected,
                            backgroundColor: '#377dff',
                            hoverBackgroundColor: '#377dff',
                            borderColor: '#377dff'
                          },
                          {
                            label: 'Chi phí thực tế',
                            data: dataActual,
                            backgroundColor: '#e7eaf3',
                            hoverBackgroundColor: '#e7eaf3',
                            borderColor: '#e7eaf3'
                          }
                        ]
                      },
                      options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        scales: {
                          yAxes: [{
                            gridLines: { color: '#e7eaf3', drawBorder: false, zeroLineColor: '#e7eaf3' },
                            ticks: {
                              beginAtZero: true,
                              max: yMax,

                              // ✅ hạ thấp khoảng cách giữa các ô (dày hơn)
                              stepSize: 1000000,
                              maxTicksLimit: 60,

                              fontSize: 12,
                              fontColor: '#97a4af',
                              fontFamily: 'Open Sans, sans-serif',
                              padding: 10,
                              callback: function(value) {
                                return Number(value).toLocaleString('vi-VN');
                              }
                            }
                          }],
                          xAxes: [{
                            gridLines: { display: false, drawBorder: false },
                            ticks: {
                              fontSize: 12,
                              fontColor: '#97a4af',
                              fontFamily: 'Open Sans, sans-serif',
                              padding: 5,

                              // (tuỳ chọn) cắt ngắn tên dự án nếu quá dài
                              callback: function(value) {
                                const s = String(value ?? '');
                                return s.length > 18 ? (s.slice(0, 18) + '…') : s;
                              }
                            },
                            categoryPercentage: 0.6,
                            barPercentage: 0.9,
                            maxBarThickness: 14
                          }]
                        },

                        tooltips: {
                          mode: 'index',
                          intersect: false,
                          callbacks: {
                            label: function(tooltipItem, data) {
                              const label = data.datasets[tooltipItem.datasetIndex].label || '';
                              const val = tooltipItem.yLabel || 0; // ChartJS v2
                              return label + ': ' + Number(val).toLocaleString('vi-VN');
                            }
                          }
                        },

                        hover: { mode: 'nearest', intersect: true }
                      }
                    });
                  });
                  </script>
    
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
<!-- <script src="account/js/chartjs.js"></script> -->
@endsection