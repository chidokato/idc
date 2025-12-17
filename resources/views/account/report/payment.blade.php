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
                                <th>Name</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-end">Tỷ lệ hộ trợ</th>
                                <th class="text-end">Tiền nộp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ctys as $cty)
                            @php
                                $total = $lv1Totals->get($cty->id);
                                $gross = $total->gross_cost ?? 0;
                                $net = $total->net_cost ?? 0;
                                $support = $gross - $net;
                            @endphp
                            <tr>
                                <td>{{ $cty->name }}</td>
                                <td class="text-end">{{ number_format($gross) }}</td>
                                <td class="text-end text-success">{{ number_format($support) }}</td>
                                <td class="text-end">{{ number_format($net) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
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