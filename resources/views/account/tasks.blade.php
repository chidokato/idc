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
                <div class="table-responsive-mobile">
                    <table class="table table-task">
                        @php
                            $totalGrossDepartment = 0; // tổng tiền gốc cả phòng
                            $totalNetDepartment   = 0; // tổng tiền sau hỗ trợ cả phòng
                        @endphp

                        @foreach($user_department as $user)
                            @foreach($user->tasks as $task)
                                @php
                                    $totalGrossDepartment += $task->gross_cost;
                                    $totalNetDepartment   += $task->net_cost;
                                @endphp
                            @endforeach
                        @endforeach
                        <thead class="thead1">
                            <tr class="text-white bg-secondary">
                                <th>Duyệt?</th>
                                <th>Mã NV</th>
                                <th>Họ Tên</th>
                                <th>Phòng/Nhóm</th>
                                <th>Dự án</th>
                                <th class="text-center">Kênh</th>
                                <th class="text-end">Tổng tiền (đ)</th>
                                <th class="text-end">Hỗ trợ</th>
                                <th class="text-end">Tiền nộp (đ)</th>
                                <th>Ghi chú</th>
                                <th>Thời gian</th>
                            </tr>
                            <tr class="bg-light.bg-gradient totall">
                                <td colspan="6">TỔNG CHI PHÍ CẢ SÀN (CHI NHÁNH)</td>
                                <td class="text-end">{{ number_format($totalGrossDepartment, 0, ',', '.') }}</td>
                                <td></td>
                                <td class="text-end">{{ number_format($totalNetDepartment, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($user_department as $user)
                            @php
                                $totalGrossUser = 0;
                                $totalNetUser   = 0;
                            @endphp
                            @foreach($user->tasks as $task)
                                @php
                                    $totalGrossUser += $task->gross_cost;
                                    $totalNetUser   += $task->net_cost;
                                @endphp
                                <tr>
                                    <td><span class="badge bg-success">Duyệt</span></td>
                                    <td>{{ $user->employee_code }}</td>
                                    <td>{{ $user->yourname }}</td>
                                    <td>{{ $task->department?->name }}</td>
                                    <td>{{ $task->Post?->name }}</td>
                                    <td class="text-center">{{ $task->Channel?->name }}</td>
                                    <td class="text-end">{{ number_format($task->gross_cost, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ $task->rate }}%</td>
                                    <td class="text-end">{{ number_format($task->net_cost, 0, ',', '.') }}</td>
                                    <td>{{ $task->content }}</td>
                                    <td>
                                        {{ date('d/m/Y', strtotime($task->Report->time_start)) }}
                                        -
                                        {{ date('d/m/Y', strtotime($task->Report->time_end)) }}
                                    </td>
                                </tr>
                            @endforeach
                            {{-- TỔNG THEO USER --}}
                            @if($totalGrossUser > 0)
                            <tr class="totall bg-light">
                                <td colspan="6">
                                   {{ $user->yourname }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($totalGrossUser, 0, ',', '.') }}
                                </td>
                                <td></td>
                                <td class="text-end">
                                    {{ number_format($totalNetUser, 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>


                <!-- <div class="table-responsive-mobile widget-list">
                    <table class="table table-task">
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
                        @foreach($tasks as $t)
                            <tr>
                                <td><span class="badge bg-success">Duyệt</span></td>
                                <td>{{ $t->handler?->employee_code }}</td>
                                <td>{{ $t->handler?->yourname }}</td>
                                <td>{{ $t->department?->name }}</td>
                                <td>{{ $t->Post?->name }}</td>
                                <td>{{ $t->Channel?->name }}</td>
                                <td>{{ number_format($t->gross_cost, 0, ',', '.') }}</td>
                                <td>{{ $t->rate }}%</td>
                                <td>{{ number_format($t->net_cost, 0, ',', '.') }}</td>
                                <td>{{ $t->content }}</td>
                                <td>
                                    {{ date('d/m/Y', strtotime($t->Report->time_start)) }}
                                    -
                                    {{ date('d/m/Y', strtotime($t->Report->time_end)) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div> -->
            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('script')

@endsection