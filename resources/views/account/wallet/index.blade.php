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
                <a class="btn btn-primary" href="account/wallet/deposit">
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
                    <h5 class="card-header-title">Lịch sử</h5>
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
                    @forelse($transactions as $item)
                    <tr>
                    <td class="table-column-pr-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="usersDataCheck1">
                            <label class="custom-control-label" for="usersDataCheck1"></label>
                        </div>
                    </td>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    @php
                        // Map type => [label, badgeClass, sign, textClass]
                        $typeMap = [
                            'deposit'  => ['Nạp tiền',        'bg-success', '+', 'text-success'],
                            'withdraw' => ['Trừ tiền',        'bg-danger',  '-', 'text-danger'],
                            'rollback' => ['Hoàn/rollback',   'bg-warning', '+', 'text-warning'],

                            // NEW
                            'hold'     => ['Giữ tiền (Hold)', 'bg-info',    '-', 'text-info'],
                            'release'  => ['Nhả giữ (Release)','bg-secondary','+','text-secondary'],
                            'capture'  => ['Nghiệm thu (Trừ)', 'bg-primary','-', 'text-primary'],
                            'refund'   => ['Hoàn tiền',        'bg-warning','+','text-warning'],
                        ];

                        $t = $typeMap[$item->type] ?? ['Khác', 'bg-dark', '', 'text-dark'];
                    @endphp
                    <td>
                        <span class="badge {{ $t[1] }}">{{ $t[0] }}</span>
                    </td>

                    <td class="{{ $t[3] }}">
                        {{ $t[2] }}
                        {{ number_format($item->amount) }} đ
                    </td>

                                                   @php
                        $amountUi = [
                            'deposit'  => ['text-success', '+'],
                            'withdraw' => ['text-danger',  '-'],
                            'rollback' => ['text-warning', '+'],

                            // NEW
                            'hold'     => ['text-info',    '-'], // giữ tiền: giảm available
                            'release'  => ['text-secondary','+'],// nhả giữ: tăng available
                            'capture'  => ['text-primary', '-'], // nghiệm thu: trừ thật từ held
                            'refund'   => ['text-warning', '+'], // hoàn tiền
                        ];

                        [$cls, $sign] = $amountUi[$item->type] ?? ['text-dark', ''];
                    @endphp

                    <td class="{{ $cls }}">
                        {{ $sign }} {{ number_format($item->amount) }} đ
                    </td>

                        <td>{{ $item->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Chưa có giao dịch
                        </td>
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