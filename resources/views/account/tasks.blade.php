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

<section class="floating-label sec-fiter-search">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <!------------------- BREADCRUMB ------------------->
                <section class="sec-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{asset('')}}">Indochine</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Account</li>
                        </ol>
                    </nav>
                </section>
                <!------------------- END: BREADCRUMB ------------------->
            </div>
            <div class="col-md-6">
                
            </div>
        </div>
        
    </div>
</section>


<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 d-none d-lg-block">
                @include('account.layout.sitebar')
            </div>
            <div class="col-lg-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Họ Tên</th>
                            <th>Sàn/Nhóm</th>
                            <th>Dự án</th>
                            <th>Kênh</th>
                            <th>Chi phí</th>
                            <th>Hỗ trợ</th>
                            <th>Ghi chú</th>
                            <th>KPI</th>
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
                                <td>{{ $task->User?->name }}</td>
                                <!-- <td>{{ $dep1?->name ?? '-' }}</td> -->
                                <td>{{ $dep3?->name ?? '-' }}</td>
                                <td>{{ $task->Post?->name }}</td>
                                <td>{{ $task->Channel?->name }}</td>
                                <td>{{ number_format($task->expected_costs,0,',','.') }}đ</td>
                                <td>{{ $task->content }}</td>
                                <td></td>
                                <td></td>
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