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
                <div class="table-responsive-mobile">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            
                            <th>Mã NV</th>
                            <th>Họ Tên</th>
                            <th>Phòng/Nhóm</th>
                            <th>Dự án</th>
                            <th>Hỗ trợ</th>
                            <th>Kênh</th>
                            <th>Số ngày</th>
                            <th>Chi phí</th>
                            <th>KPI</th>
                            <th>Ghi chú</th>
                            <th>Thời gian</th>
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
                                <td>
                                    @if($task->approved)
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @else
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @endif
                                </td>
                                
                                <td>{{ $task->User?->employee_code }}</td>
                                <td>{{ $task->User?->yourname }}</td>
                                <td>{{ $dep3?->name ?? '-' }}</td>
                                <td>{{ $task->Post?->name }}</td>
                                <td>{{ $task->Post?->rate }}%</td>
                                <td>{{ $task->Channel?->name }}</td>
                                <td>{{ $task->Report->days }}</td>
                                <td>{{ number_format($task->Report->days * $task->expected_costs,0,',','.') }}đ</td>
                                <td>{{ $task->content }}</td>
                                <td></td>
                                <td>{{date('d/m/Y',strtotime($task->Report->time_start))}} - {{date('d/m/Y',strtotime($task->Report->time_end))}}</td>
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