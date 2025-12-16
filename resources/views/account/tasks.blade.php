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
                <div class="text-uppercase title-cat flex gap1">
                    <div>
                        <select class="form-control" name="department_id">
                            <option value="">Tất cả</option>
                            {!! $departmentOptions !!}
                        </select>
                    </div>
                    <div>
                        <select class="form-control">
                            <!-- <option value="">Tất cả</option> -->
                            @foreach($reports as $key => $val)
                            <option <?php if($key==0){echo "selected";} ?> value="{{$val->id}}">{{$val->name}} ({{date('d/m/Y',strtotime($val->time_start))}} - {{date('d/m/Y',strtotime($val->time_end))}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="table-responsive-mobile widget-list">
                    <table class="table">
                        <thead class="thead1">
                            <tr>
                                <th>Duyệt?</th>
                                <th>Mã NV</th>
                                <th>Họ Tên</th>
                                <th>Phòng/Nhóm</th>
                                <th>Dự án</th>
                                <th>Kênh</th>
                                <th>Tổng tiền (đ)</th>
                                <th>Hỗ trợ</th>
                                <th>Tiền nộp (đ)</th>
                                <th>Ghi chú</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>

                        @php $totalDepartment = 0; @endphp

                        @foreach($user_department as $user)

                            @php $totalUser = 0; @endphp

                            @foreach($user->tasks as $task)

                                @php
                                    $money = $task->days
                                        * $task->expected_costs
                                        * (1 - $task->rate / 100);

                                    $totalUser += $money;
                                    $totalDepartment += $money;
                                @endphp

                                <tr>
                                    <td><span class="badge bg-success">Duyệt</span></td>
                                    <td>{{ $user->employee_code }}</td>
                                    <td>{{ $user->yourname }}</td>
                                    <td>{{ $task->department?->name }}</td>
                                    <td>{{ $task->Post?->name }}</td>
                                    <td>{{ $task->Channel?->name }}</td>
                                    <td>
                                        {{ number_format($task->days * $task->expected_costs, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $task->rate }}%</td>
                                    <td>{{ number_format($money, 0, ',', '.') }}</td>
                                    <td>{{ $task->content }}</td>
                                    <td>
                                        {{ date('d/m/Y',strtotime($task->Report->time_start)) }} -
                                        {{ date('d/m/Y',strtotime($task->Report->time_end)) }}
                                    </td>
                                </tr>

                            @endforeach

                            {{-- TỔNG THEO USER --}}
                            @if($totalUser > 0)
                            <tr class="totall bg-light">
                                <td colspan="8">
                                    <strong>Tổng chi phí: {{ $user->yourname }}</strong>
                                </td>
                                <td>
                                    <strong>{{ number_format($totalUser, 0, ',', '.') }}</strong>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            @endif

                        @endforeach

                        {{-- TỔNG CẢ PHÒNG --}}
                        <tr class="totall bg-dark text-white">
                            <td colspan="8"><strong>TỔNG CHI PHÍ CẢ PHÒNG</strong></td>
                            <td>
                                <strong>{{ number_format($totalDepartment, 0, ',', '.') }}</strong>
                            </td>
                            <td colspan="2"></td>
                        </tr>

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