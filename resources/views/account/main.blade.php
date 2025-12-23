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
<!-- <script src="account/js/chartjs.js"></script> -->
@endsection