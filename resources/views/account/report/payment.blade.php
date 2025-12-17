@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/news.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
@endsection

@section('content')
@include('account.layout.menu')
<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                @include('account.layout.sitebar')
            </div>
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Sàn</th>
                                <th>Phòng</th>
                                <th>Nhóm</th>
                                <th>Người</th>
                                <th class="text-end">Chi phí dự kiến</th>
                                <th class="text-end">Tiền hỗ trợ</th>
                                <th class="text-end">Chi phí ròng</th>
                                <th class="text-end">Chi phí thực tế</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalGross = 0;
                                $totalNet = 0;
                                $totalSupport = 0;
                                $totalActual = 0;
                            @endphp

                            @foreach($summary as $row)
                                @php
                                    $totalGross += $row->gross_cost;
                                    $totalNet += $row->net_cost;
                                    $totalSupport += $row->support_cost;
                                    $totalActual += $row->actual_cost;
                                @endphp
                                <tr>
                                    <td>{{ $row->department_lv1 }}</td>
                                    <td>{{ $row->department_lv2 }}</td>
                                    <td>{{ $row->department->name ?? '-' }}</td>
                                    <td>{{ $row->user->name ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($row->gross_cost) }}</td>
                                    <td class="text-end text-success">{{ number_format($row->support_cost) }}</td>
                                    <td class="text-end">{{ number_format($row->net_cost) }}</td>
                                    <td class="text-end text-danger">{{ number_format($row->actual_cost) }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TỔNG</td>
                                <td class="text-end">{{ number_format($totalGross) }}</td>
                                <td class="text-end text-success">{{ number_format($totalSupport) }}</td>
                                <td class="text-end">{{ number_format($totalNet) }}</td>
                                <td class="text-end text-danger">{{ number_format($totalActual) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('script')

@endsection