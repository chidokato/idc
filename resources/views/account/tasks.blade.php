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

<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                @include('account.layout.sitebar')
            </div>
            <div class="col-lg-10">
                <div class="text-uppercase title-cat flex space-between">
                    <div>{{ $depLv2?->name }} <small>({{ $depLv1?->name }})</small></div>
                    <div>
                        <select class="form-control">
                            <option value="">Tất cả</option>
                            @foreach($reports as $key => $val)
                            <option <?php if($key==0){echo "selected";} ?> value="{{$val->id}}">{{$val->name}} ({{date('d/m/Y',strtotime($val->time_start))}} - {{date('d/m/Y',strtotime($val->time_end))}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Họ Tên</th>
                            <th>Nhóm</th>
                            <th>Dự án</th>
                            <th>Kênh</th>
                            <th>Chi phí</th>
                            <th>Hỗ trợ</th>
                            <th>Thành tiền</th>
                            <th>KPI</th>
                            <th>Ghi chú</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            @php
                                $dep3 = $task->User?->department;
                                $dep2 = $dep3?->parentDepartment;
                                $dep1 = $dep2?->parentDepartment;
                            @endphp
                            <tr>
                                <td>{{ $task->User?->yourname }}</td>
                                <td>{{ $dep3?->name ?? '-' }}</td>
                                <td>{{ $task->Post?->name }}</td>
                                <td>{{ $task->Channel?->name }}</td>
                                <td>{{ number_format($task->expected_costs,0,',','.') }}đ</td>
                                <td>{{ $task->Post?->rate }}</td>
                                <td>{{ number_format(((float) ($task->Post->rate ?? 0)) * ((float) ($task->expected_costs ?? 0)), 0, ',', '.') }}đ</td>
                                <td>{{ $task->content }}</td>
                                <td></td>
                                <td>
                                    @if($task->approved)
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @else
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('script')

@endsection