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
                    <div class="col-md-1">
                        <select name="approved" class="form-control select2">
                            <option value="" {{ request()->filled('approved') ? '' : 'selected' }}>
                                -- Duyệt ?? --
                            </option>
                            <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>
                                Đã duyệt
                            </option>
                            <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>
                                Chưa duyệt
                            </option>
                        </select>
                    </div>


                    <div class="col-md-2">
                        <button class="btn button-search form-control bg-success">Lọc</button>
                    </div>

                </form>

                <table class="table table-hover">
                    <thead class="thead">
                        <tr >
                            <th>Họ Tên</th>
                            <th>Sàn</th>
                            <th>Nhóm</th>
                            <th>Dự án</th>
                            <th class="text-center">Kênh</th>
                            <!-- <th>Chi phí</th> -->
                            <!-- <th>Số ngày</th> -->
                            <th class="text-end">Tổng tiền </th>
                            <th>Hỗ trợ</th>
                            <th>Ghi chú</th>
                            <th>KPI</th>
                            <!-- <th></th> -->
                            <th>Duyệt</th>
                            <th></th>
                        </tr>
                        
                    </thead>

                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end">{{ number_format($tongTien, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>   
                        </tr>
                        <?php
                            $task = $task->sortBy([
                                fn($a, $b) => strcmp($a->department?->hierarchy_levels['level2'] ?? '', $b->department?->hierarchy_levels['level2'] ?? ''),
                                fn($a, $b) => strcmp($a->department?->hierarchy_levels['level3'] ?? '', $b->department?->hierarchy_levels['level3'] ?? ''),
                            ]);
                        ?>
                        @foreach($task as $val)
                        <?php $levels = $val->department?->hierarchy_levels ?? []; ?>
                        <tr class="padding16" id="row-{{ $val->id }}">
                            <td>{{ $val->handler?->yourname ?? '---' }}</td>
                            <td>{{ $levels['level2'] ?? '-' }}</td>
                            <td>{{ $levels['level3'] ?? '-' }}</td>
                            <td>{{ $val->Post?->name }}</td>
                            <td class="text-center">{{ $val->Channel?->name }}</td>
                            <!-- <td>{{ number_format($val->expected_costs, 0, ',', '.') }} đ</td> -->
                            <!-- <td>{{ $val->days }}</td> -->
                            <td class="text-end">{{ number_format($val->total_costs ?? $val->days*$val->expected_costs, 0, ',', '.') }} <span title="{{ number_format($val->expected_costs, 0, ',', '.') }}đ * {{ $val->days }} ngày" class="note">?</span></td>
                            <td>
                                <select name="rate" class="rate-select form-select form-select-sm" data-id="{{ $val->id }}">
                                    @foreach(config('rates') as $value => $label)
                                        <option value="{{ $value }}" {{ $val->rate == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- <td>{{ number_format($val->support_money ?? 0, 0, ',', '.') }} đ</td> -->
                            <td class="ghichu" title="{{ $val->content }}">
                                <span class="tooltip-wrapper">
                                    <span class="text-truncate-set-1 text-truncate-set">
                                        {{ $val->content }}
                                    </span>
                                    <span class="tooltip">
                                        {{ $val->content }}
                                    </span>
                                </span>
                            </td>

                            <td>
                                <input type="text" class="task-kpi form-control form-select-sm" value="{{ $val->kpi ?? '' }}" data-id="{{ $val->id }}" placeholder="..." >
                            </td>
                            <!-- <td>
                                <form action="{{ route('account.tasks.delete', $val) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="del-db btn btn-danger p-1" data-id="{{ $val->id }}">Xóa</button>
                                </form>
                            </td> -->
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

<script>
$(document).on('click', '.del-db', function (e) {
    e.preventDefault();

    let id = $(this).data('id');
    let row = $("#row-" + id);

    let approved = row.find('td:nth-child(5) span').hasClass('bg-success'); 

    if (approved) {
        Swal.fire('Không thể xóa!', 'Tác vụ đã được duyệt, không thể xóa.', 'warning');
        return;
    }

    let url = "{{ url('account/tasks/delete') }}/" + id;

    Swal.fire({
        title: 'Bạn có chắc muốn xóa?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Có',
        cancelButtonText: 'Không'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: url,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    if (res.status) {

                        row.fadeOut(300, function() {
                            $(this).remove();

                            // Cập nhật giao diện số liệu
                            $("#tongduan").text(res.stats.total_project + " dự án");
                            $("#tongtien").text(res.stats.total_expected + " đ");
                            $("#tongphainop").text(res.stats.total_pay + " đ");
                        });

                    } else {
                        Swal.fire('Lỗi!', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi!', 'Không thể kết nối server.', 'error');
                }
            });

        }
    });
});
</script>

<script>
$(document).on('change', '.rate-select', function () {
    let rate = $(this).val();
    let taskId = $(this).data('id');

    $.ajax({
        url: "{{ route('tasks.updateRate') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: taskId,
            rate: rate
        },
        success: function (res) {
            if (res.success) {
                showToast('success', 'Đã cập nhật rate ' + rate + '%');
            } else {
                showToast('warning', 'Cập nhật rate không thành công');
            }
        },
        error: function () {
            showToast('error', 'Lỗi khi cập nhật rate');
        }
    });
});
</script>

<script>
$(document).on('change', '.task-kpi', function () {
    let input = $(this);
    let kpi = input.val();
    let taskId = input.data('id');

    $.ajax({
        url: "{{ route('task.updateKpi') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            task_id: taskId,
            kpi: kpi
        },
        success: function (res) {
            if (res.status) {
                input.css('border', '1px solid #28a745');
            }
        },
        error: function () {
            alert('Lỗi khi lưu KPI');
            input.css('border', '1px solid red');
        }
    });
});
</script>



@endsection