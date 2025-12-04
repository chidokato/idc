@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection



@section('content')
@include('account.layout.menu')
<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="mb-4 flex space-between"><button type="button" onclick="window.location.href='{{route('report.index')}}'" class="btn btn-primary">Trở về trang trước</button> {{ $report->name }} ({{ \Carbon\Carbon::parse($report->time_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($report->time_end)->format('d/m/Y') }}) </h3>
                <hr>
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-2">
                        <select name="department_id" class="form-control select2">
                            <option value="">-- Sàn / Nhóm --</option>
                            {!! $departmentOptions !!}
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="post_id" class="form-control select2">
                            <option value="">-- Dự án --</option>
                            @foreach($posts as $p)
                                <option value="{{ $p->id }}" {{ request('post_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="channel_id" class="form-control select2">
                            <option value="">-- Kênh --</option>
                            {!! $channelsOptions !!}
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn button-search form-control bg-success">Lọc</button>
                    </div>

                </form>

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
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($task as $val)
                        <?php $levels = $val->department?->hierarchy_levels ?? []; ?>
                        <tr>
                            <td>{{ $val->handler?->yourname ?? '---' }} <br> <small>{{ $val->handler?->email }}</small> </td>
                            <td>{{ $levels['level3'] ?? '-' }} <br>
                                <small>{{ $levels['level2'] ?? '-' }}</small>
                            </td>

                            <td>{{ $val->Post?->name }}</td>
                            <td>{{ $val->Channel?->name }}</td>
                            <td>{{ number_format($val->expected_costs, 0, ',', '.') }} đ</td>
                            <td>{{ $days }}</td>
                            <td>{{ number_format($val->total_costs ?? $days*$val->expected_costs, 0, ',', '.') }} đ</td>
                            <td>{{ number_format($val->support_money ?? 0, 0, ',', '.') }} đ</td>
                            <td>{{ $val->content }}</td>
                            <td>{{ $val->kpi ?? '-' }}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="active-toggle" data-id="{{ $val->id }}" {{ $val->approved ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td> @if($val->approved) <span class="badge bg-success">Đã duyệt</span> @else <span class="badge bg-warning">Chờ duyệt</span> @endif </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $task->links() }}
                </div>


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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
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