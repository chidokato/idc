@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection



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
            <div class="col-lg-12">
                <h3 class="mb-4">{{ $report->name }} ({{ \Carbon\Carbon::parse($report->time_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($report->time_end)->format('d/m/Y') }}) <button type="button" onclick="window.location.href='{{route('report.index')}}'" class="btn btn-primary">Thoát trình duyệt MKT</button></h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Họ Tên</th>
                            <th>Sàn/Nhóm</th>
                            <th>Dự án</th>
                            <th>Kênh</th>
                            <th>Chi phí</th>
                            <th>Số ngày</th>
                            <th>Tổng tiền</th>
                            <th>Hỗ trợ</th>
                            <th>Ghi chú</th>
                            <th>KPI</th>
                            <th>Duyệt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task as $val)
                        <?php $levels = $val->User?->department?->hierarchy_levels ?? []; ?>
                        <tr>
                            <td>{{ $val->User?->name }}</td>
                            <td>{{ $levels['level3'] ?? '-' }} <br> <small>{{ $levels['level2'] ?? '-' }}</small></td>
                            <td>{{ $val->Post?->name }}</td>
                            <td>{{ $val->Channel?->name }}</td>
                            <td>{{ number_format($val->expected_costs, 0, ',', '.') }}đ</td>
                            <td>{{ $days }}</td>
                            <td>{{ number_format($val->total_costs ?? $days*$val->expected_costs, 0, ',', '.') }} đ</td>
                            <td>{{ number_format($val->support_money ?? 0, 0, ',', '.') }}đ</td>
                            <td>{{ $val->content }}</td>
                            <td>{{ $val->kpi ?? '-' }}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="active-toggle" data-id="{{ $val->id }}" {{ $val->approved ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                @if($val->approved)
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

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->

@endsection
@section('js')
<script>
$(document).ready(function() {
    $('.active-toggle').on('change', function() {
        let checkbox = $(this);
        let taskId = checkbox.data('id');
        let approved = checkbox.is(':checked'); // true/false

        $.ajax({
            url: 'account/task/toggle-approved/' + taskId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                approved: approved
            },
            success: function(res) {
                if(res.success) {
                    let badge = checkbox.closest('tr').find('td:last span');
                    if(res.approved) {
                        badge.removeClass('bg-warning').addClass('bg-success').text('Đã duyệt');
                    } else {
                        badge.removeClass('bg-success').addClass('bg-warning').text('Chờ duyệt');
                    }
                }
            },
            error: function(err) {
                alert('Cập nhật thất bại!');
                // revert checkbox
                checkbox.prop('checked', !approved);
            }
        });
    });
});
</script>
@endsection