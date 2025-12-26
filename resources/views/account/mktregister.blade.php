@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')

@endsection

@section('body')  @endsection

@section('content')

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="account/main">Account</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Đăng ký marketing</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">Đăng ký marketing</h1>
            </div>
        </div>
    </div>


    <div class="card mb-3 mb-lg-5">
          <!-- Header -->
        <div class="card-header">
            <h4 class="card-header-title">Thêm link marketing</h4>
        </div>
          <!-- End Header -->
          <!-- Body -->
        <form action="{{ route('account.tasksstore') }}" method="POST">
            @csrf
            <input type="hidden" name="" value="">
            <div class="table-responsive">
                <table class="table table-borderless table-thead-bordered table-nowrap table-text-center table-align-middle card-table custon-table" id="myTable">
                    <thead class="thead-light">
                        <tr>
                            <th></th>
                            @if(Auth::user()->rank < 3)
                            <th>Nhân viên</th>
                            @endif
                            <th>Dự án <span class="required">(*)</span></th>
                            <th>Hỗ trợ</th>
                            <th>Kênh <span class="required">(*)</span></th>
                            <th>Ngân sách/ngày <span class="required">(*)</span></th>
                            <th>Ghi chú</th>
                            <th>Thời gian chạy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="btn-group" role="group">
                                  <a class="btn btn-sm btn-white dupple">
                                    <i class="tio-copy"></i> Nhân
                                  </a>
                                  <a class="btn btn-sm btn-white del">
                                    <i class="tio-delete-outlined"></i>
                                  </a>
                                </div>
                              </td>
                            @if(Auth::user()->rank < 3)
                            <td>
                                <select name="user_id[]" required class="form-control user-select">
                                    <option value="">---</option>
                                    @foreach($users as $val)
                                    <option <?php if(Auth::user()->id == $val->id){ echo "selected"; } ?> value="{{ $val->id }}" data-department="{{ $val->department_id }}">
                                        {{ $val->yourname }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="department_id[]" class="department-id" value="{{ Auth::user()->department_id }}">
                            </td>
                            @else
                            <input type="hidden" name="user_id[]" class="department-id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="department_id[]" class="department-id" value="{{ Auth::user()->department_id }}">
                            @endif
                            <td>
                                <select name="post_id[]" required class="form-control post-select">
                                    <option value="">---</option>
                                    @foreach($posts as $val)
                                    <option value="{{ $val->id }}" data-rate="{{ $val->rate }}">
                                        {{ $val->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <select name="rate[]" required class="form-control rate-select">
                                    <option value="">-</option>
                                    @foreach(config('datas.rates') as $key => $label)
                                        <option value="{{ $key }}">
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <select name="channel_id[]" required  class="form-control channel_id">
                                    <option value="">---</option>
                                    @foreach($channels as $val)
                                    <option value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="days[]" class="days" value="">
                            </td>
                            <td>
                                <select name="expected_costs[]" required class="form-control">
                                    <option value="">---</option>
                                    @foreach(config('datas.costs') as $key => $label)
                                        <option value="{{ $key }}">
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input class="form-control" type="text" name="content[]" placeholder="Hỗ trợ, chạy chung ..."></td>
                            <td>
                                <select name="report_id[]" class="form-control report_id" required>
                                    @foreach($reports as $val)
                                    <option value="{{ $val->id }}" data-days="{{ $val->days }}">{{ $val->name }}</option>
                                    @endforeach
                                </select>
                                
                            </td>
                            
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <p><button type="submit" class="btn btn-sm btn-primary">Gửi đăng ký MARKETING</button></p>
                <p>Nếu không đăng ký được, thiếu dự án, thiếu trường nhập dữ liệu. Liên hệ ngay <a href="https://zalo.me/0977572947" target="_blank"><strong>Nguyễn Tuấn</strong></a> để được hỗ trợ kỹ thuật</p>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header" data-select2-id="8">
            <div class="row align-items-center flex-grow-1" data-select2-id="7">
                <div class="col-sm mb-2 mb-sm-0">
                    <h4 class="card-header-title">Danh sách link đăng ký marketing của phòng/sàn <i class="tio-help-outlined text-body ml-1" data-toggle="tooltip" data-placement="top" title="" data-original-title="Net sales (gross sales minus discounts and returns) plus taxes and shipping. Includes orders from all sales channels."></i></h4>
                </div>
                <!-- <div class="col-sm-auto" data-select2-id="6">
                    <select class="custom-select-sm form-control">
                        <option value="" data-select2-id="3">Online store</option>
                        <option value="in-store" data-select2-id="4">In-store</option>
                    </select>
                </div> -->
            </div>
        </div>
        @foreach($reports as $report)
        @if($report->Task->isEmpty())
        <p>Kỳ này bạn chưa đăng ký dự án nào</p>
        @else
        <?php
            $tasks_all = $report->Task()->whereIn('department_id', $groupIds)->get();
            $tasks = $report->Task()->where('user', Auth::id())->get();
        ?>
        <div class="card-body">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-borderless table-thead-bordered card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Nhân viên</th>
                            <th>Phòng/Nhóm</th>
                            <th>Dự án</th>
                            <th>Hỗ trợ</th>
                            <th>Kênh</th>
                            <th>Ngân sách</th>
                            <th>Tổng tiền</th>
                            <th>Tiền phải nộp</th>
                            <th>Ghi chú</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $val)
                        <tr class="padding16" id="row-{{ $val->id }}">
                            <td>{{$val->handler?->yourname ?? '-'}}</td>
                            <td>{{ $val->Department->name }}</td>
                            <td>{{$val->Post?->name}}</td>
                            <td>{{$val->rate}}%</td>
                            <td>{{$val->Channel?->name}}</td>
                            <td>{{ number_format($val->expected_costs, 0, ',', '.') }} đ</td>
                            <td>{{ number_format(($val->days * $val->expected_costs), 0, ',', '.') }} đ</td>
                            <td>{{ number_format(($val->days * $val->expected_costs * (1 - $val->rate/100)), 0, ',', '.') }} đ</td>
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
                                @if($val->approved)
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('account.tasks.delete', $val) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="button-none btn-white del-db" data-id="{{ $val->id }}"> <i class="tio-delete-outlined"></i> Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @foreach($tasks_all as $val)
                        <tr class="padding16" id="row-{{ $val->id }}">
                            <td>{{$val->handler?->yourname ?? '-'}}</td>
                            <td>{{ $val->Department->name }}</td>
                            <td>{{$val->Post?->name}}</td>
                            <td>{{$val->rate}}%</td>
                            <td>{{$val->Channel?->name}}</td>
                            <td>{{ number_format($val->expected_costs, 0, ',', '.') }} đ</td>
                            <td>{{ number_format(($val->days * $val->expected_costs), 0, ',', '.') }} đ</td>
                            <td>{{ number_format(($val->days * $val->expected_costs * (1 - $val->rate/100)), 0, ',', '.') }} đ</td>
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
                                @if($val->approved)
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @endif
                            </td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>

@endsection


@section('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();
    });

    $(document).on('click', '.del', function() {
        let row = $(this).closest('tr');
        let totalRows = $('#myTable tr').length; // đếm số hàng

        if (totalRows <= 2) {
            Swal.fire({
                icon: 'error',
                title: 'Không thể xóa!',
                text: 'Bạn phải có ít nhất 1 hàng.',
            });
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Bạn có chắc muốn xóa?',
            showCancelButton: true,
            confirmButtonText: 'Có',
            cancelButtonText: 'Không'
        }).then(result => {
            if (result.isConfirmed) {
                row.remove();
            }
        });
    });

    $(document).on('click', '.dupple', function() {
        let row = $(this).closest('tr');
        let clone = row.clone(true);

        // Lấy tất cả select2 trong clone và hủy select2 cũ
        clone.find('select.select2').each(function() {
            $(this).select2('destroy');
        });

        // Giữ nguyên dữ liệu
        clone.find('select').each(function(i) {
            $(this).val(row.find('select').eq(i).val());
        });

        clone.find('input').each(function(i) {
            $(this).val(row.find('input').eq(i).val());
        });

        // Chèn clone
        row.after(clone);

        // Khởi tạo lại select2 cho clone
        clone.find('select.select2').select2();
    });


    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="{{ route('account.tasksstore') }}"]');

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn submit mặc định

            Swal.fire({
                title: 'Xác nhận',
                text: "Bạn đã chắc chắn gửi đăng ký chi phí marketing",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Có, gửi ngay!',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit form nếu xác nhận
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
    $(document).on('change', '.user-select', function() {
        let departmentId = $(this).find(':selected').data('department'); 
        $(this).closest('td').find('.department-id').val(departmentId);
    }); // gán id phòng thuộc người dùng

    $(document).on('change', '.post-select', function() {
        let rate = $(this).find(':selected').data('rate'); // Lấy rate từ post
        let rateSelect = $(this).closest('tr').find('.rate-select');
        rateSelect.val(rate); // Set selected option phù hợp
    }); // Gán tỷ lệ hỗ trợ theo dự án

</script>


<script>
$(document).on('change', '.channel_id', function () {
    const row = $(this).closest('tr');
    const channelId = parseInt($(this).val());
    let days = 1; // mặc định

    // Nếu channel_id = 2,3,4 -> lấy days theo report_id
    if ([2, 3, 4].includes(channelId)) {
        const selectedReport = row.find('.report_id').find(':selected');
        const reportDays = parseInt(selectedReport.data('days'));

        if (!Number.isNaN(reportDays)) {
            days = reportDays;
        }
    }

    row.find('.days').val(days);
});


</script>


@endsection