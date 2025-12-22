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
                <li class="breadcrumb-item"><a class="breadcrumb-link" href="account">Account</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ví tiền</li>
                </ol>
                </nav>
                <h1 class="page-header-title">Ví tiền</h1>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn-primary" href="wallet/deposit">
                    <i class="tio-money mr-1"></i> Nạp tiền
                </a>
            </div>
        </div>
    <!-- End Row -->
    </div>

    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
        <!-- Card -->
        <div class="card h-100">
        <div class="card-body">
        <h6 class="card-subtitle mb-2">Tổng tiền hiện có</h6>

        <div class="row align-items-center gx-2">
        <div class="col">
        <span class="js-counter display-4 text-dark" data-value="{{ number_format($wallet->balance) }}">{{ number_format($wallet->balance) }}</span>
        <span class="text-body font-size-sm ml-1">VNĐ</span>
        </div>
        </div>
        <!-- End Row -->
        </div>
        </div>
        <!-- End Card -->
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
        <!-- Card -->
        <div class="card h-100">
        <div class="card-body">
        <h6 class="card-subtitle mb-2">Tiền tạm giữ</h6>

        <div class="row align-items-center gx-2">
        <div class="col">
        <span class="js-counter display-4 text-dark" data-value="{{ number_format($wallet->held_balance) }}">{{ number_format($wallet->held_balance) }}</span>
        <span class="text-body font-size-sm ml-1">VNĐ</span>
        </div>
        </div>
        <!-- End Row -->
        </div>
        </div>
        <!-- End Card -->
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
        <!-- Card -->
        <div class="card h-100">
        <div class="card-body">
        <h6 class="card-subtitle mb-2">Tiền có thể dùng</h6>

        <div class="row align-items-center gx-2">
        <div class="col">
        <span class="js-counter display-4 text-dark" data-value="0">0</span>
        <span class="text-body font-size-sm ml-1">VNĐ</span>
        </div>
        </div>
        <!-- End Row -->
        </div>
        </div>
        <!-- End Card -->
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
        <!-- Card -->
        <div class="card h-100">
        <div class="card-body">
        <h6 class="card-subtitle mb-2">Tổng tiền đã chi</h6>

        <div class="row align-items-center gx-2">
        <div class="col">
        <span class="js-counter display-4 text-dark" data-value="0">0</span>
        <span class="text-body font-size-sm ml-1">VNĐ</span>
        </div>
        </div>
        <!-- End Row -->
        </div>
        </div>
        <!-- End Card -->
        </div>
    </div>

    <div class="card">
          <!-- Header -->
          <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">
              <div class="col-sm-6 col-md-4 mb-3 mb-sm-0">
                <form>
                  <!-- Search -->
                  <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <i class="tio-search"></i>
                      </div>
                    </div>
                    <input id="datatableSearch" type="search" class="form-control" placeholder="Search users" aria-label="Search users">
                  </div>
                  <!-- End Search -->
                </form>
              </div>

              <div class="col-sm-6">
                <div class="d-sm-flex justify-content-sm-end align-items-sm-center">
                  <!-- Datatable Info -->
                  <div id="datatableCounterInfo" class="mr-2 mb-2 mb-sm-0" style="display: none;">
                    <div class="d-flex align-items-center">
                      <span class="font-size-sm mr-3">
                        <span id="datatableCounter">0</span>
                        Selected
                      </span>
                      <a class="btn btn-sm btn-outline-danger" href="javascript:;">
                        <i class="tio-delete-outlined"></i> Delete
                      </a>
                    </div>
                  </div>
                  <!-- End Datatable Info -->

                  
                </div>
              </div>
            </div>
            <!-- End Row -->
          </div>
          <!-- End Header -->

          <!-- Table -->
          <div class="table-responsive datatable-custom">
            <table class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
              <thead class="thead-light">
                <tr>
                  <th class="table-column-pr-0">
                    <div class="custom-control custom-checkbox">
                      <input id="datatableCheckAll" type="checkbox" class="custom-control-input">
                      <label class="custom-control-label" for="datatableCheckAll"></label>
                    </div>
                  </th>
                  <th>#</th>
                            <th>Thời gian</th>
                            <th>Loại</th>
                            <th>Số tiền</th>
                            <th></th>
                            <th>Ghi chú</th>
                </tr>
              </thead>

                <tbody>
                    

               
              </tbody>
            </table>
          </div>
          <!-- End Table -->
        </div>
</div>

@endsection


@section('js')

@endsection