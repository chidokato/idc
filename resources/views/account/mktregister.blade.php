@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/news.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@include('account.layout.menu')
<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                @include('account.layout.sitebar')
            </div>
                <div class="col-lg-10 ">
                    <div class="widget-list">
                        
                    
                    <form action="{{ route('account.tasksstore') }}" method="POST">
                        @csrf
                        <input type="hidden" name="" value="">
                        <div class="table-responsive-mobile">
                            <table class="table" id="myTable">
                                <thead>
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
                                        <td class="flex">
                                            <button type="button" class="form-control del">xóa</button>
                                            <button type="button" class="form-control dupple">Nhân</button>
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
                                                <option value="100">100%</option>
                                                <option value="95">95%</option>
                                                <option value="90">90%</option>
                                                <option value="80">80%</option>
                                                <option value="70">70%</option>
                                                <option value="60">60%</option>
                                                <option value="50">50%</option>
                                                <option value="30">30%</option>
                                                <option value="0">0%</option>
                                            </select>
                                        </td>

                                        <td>
                                            <select name="channel_id[]" required class="form-control">
                                                <option value="">---</option>
                                                @foreach($channels as $val)
                                                <option value="{{$val->id}}">{{$val->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="expected_costs[]" required class="form-control">
                                                <option value="">---</option>
                                                <option value="1000000">1.000.000 đ</option>
                                                <option value="500000">500.000 đ</option>
                                                <option value="300000">300.000 đ</option>
                                            </select>
                                        </td>
                                        <td><input class="form-control" type="text" name="content[]" placeholder="Hỗ trợ, chạy chung ..."></td>
                                        <td>
                                            <select name="report_id[]" required class="form-control">
                                                @foreach($reports as $val)
                                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <p><button type="submit" class="">Gửi đăng ký MARKETING</button></p>
                        </div>
                    </form>
                    </div>
                    <hr>
                    @foreach($reports as $report)
                    @if($report->Task->isEmpty())
                    <p>Kỳ này bạn chưa đăng ký dự án nào</p>
                    @else
                    <?php
                        $tasks_all = $report->Task()->whereIn('department_id', $groupIds)->get();
                        $tasks = $report->Task()->where('user', Auth::id())->get();
                        $total_expected = 0;   // tổng tiền gốc
                        $total_pay = 0;        // tổng tiền phải nộp
                    ?>
                    <div class="row">
                        <div class="col-lg-9 widget mb-3">
                            <div class="widget-list">
                                
                            
                            <p class="required"><i>- Chú ý: Cổng đăng ký chi phí marketing sẽ <strong>TỰ ĐỘNG ĐÓNG</strong> vào <strong>00h00 ngày {{ \Carbon\Carbon::parse($report->time_start)->format('d/m/Y') }}</strong>. Có thể gửi nhiều lần trước khi cổng đăng ký đóng lại.</i></p>
                            <div>
                                <h3>{{$report->name}} ({{ \Carbon\Carbon::parse($report->time_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($report->time_end)->format('d/m/Y') }})</h3>
                                <div class="table-responsive-mobile">
                                <table class="table">
                                    <tr>
                                        <th></th>
                                        <th>Nhân viên</th>
                                        <th>Dự án</th>
                                        <th>Hỗ trợ</th>
                                        <th>Kênh</th>
                                        <th>Ngân sách</th>
                                        <th>Tổng tiền</th>
                                        <th>Tiền phải nộp</th>
                                        <th>Ghi chú</th>
                                        <th></th>
                                    </tr>
                                    @foreach($tasks as $val)
                                    <tr class="padding16" id="row-{{ $val->id }}">
                                        <td>
                                            @if($val->approved)
                                                <span class="badge bg-success">Đã duyệt</span>
                                            @else
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @endif
                                        </td>
                                        <td>{{$val->handler?->yourname ?? '-'}}</td>
                                        <td>{{$val->Post?->name}}</td>
                                        <td>{{$val->rate}}%</td>
                                        <td>{{$val->Channel?->name}}</td>
                                        <td>{{ number_format($val->expected_costs, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format(($report->days * $val->expected_costs), 0, ',', '.') }} đ</td>
                                        <td>{{ number_format(($report->days * $val->expected_costs * (1 - $val->rate/100)), 0, ',', '.') }} đ</td>
                                        <td>{{ $val->content }}</td>
                                        
                                        <td>
                                            <form action="{{ route('account.tasks.delete', $val) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="del-db btn btn-danger p-1" data-id="{{ $val->id }}">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>

                                    <?php
                                        $expected = $report->days * $val->expected_costs;
                                        $pay = $report->days * $val->expected_costs * (1 - ($val->rate ?? 0) / 100);

                                        $total_expected += $expected;
                                        $total_pay += $pay;
                                    ?>

                                    @endforeach
                                </table>
                            </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-3 widget ">
                            <div class=" mb-3 thongke widget-list">
                                <h4><span>Thống kê</span></h4>
                                <ul>
                                    <li class="mb-3">
                                        <div><span>Tổng số:</span> <span>{{ $tasks->pluck('post_id')->unique()->count() }} dự án</span></div>
                                    </li>
                                    <li class="mb-3">
                                        <div><span>Tổng tiền:</span> <span class="">{{ number_format($total_expected, 0, ',', '.') }} đ</span></div>
                                    </li>
                                    <li class="mb-3">
                                        <div><span>Tổng tiền phải nộp (dự kiến):</span> <span class="required">{{ number_format($total_pay, 0, ',', '.') }} đ</span></div>
                                    </li>
                                </ul>
                                <!-- <p class="required"><i>* Số tiền phải đóng phụ thuộc vào số lượng dự án được duyệt và tỷ lệ hỗ trợ mỗi dự án</i></p> -->
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-12 widget ">
                            <div class="widget-list">
                                <h3>Danh sách tác vụ: {{ Auth::user()->Department->Parent->name }}</h3>
                                <div class="table-responsive-mobile">
                                <table class="table">
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
                                    </tr>
                                    @foreach($tasks_all as $val)
                                    <tr class="padding16" id="row-{{ $val->id }}">
                                        <td>{{$val->handler?->yourname ?? '-'}}</td>
                                        <td>{{ $val->Department->name }}</td>
                                        <td>{{$val->Post?->name}}</td>
                                        <td>{{$val->rate}}%</td>
                                        <td>{{$val->Channel?->name}}</td>
                                        <td>{{ number_format($val->expected_costs, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format(($report->days * $val->expected_costs), 0, ',', '.') }} đ</td>
                                        <td>{{ number_format(($report->days * $val->expected_costs * (1 - $val->rate/100)), 0, ',', '.') }} đ</td>
                                        <td>{{ $val->content }}</td>
                                        <td>
                                            @if($val->approved)
                                                <span class="badge bg-success">Đã duyệt</span>
                                            @else
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            </div>
                        </div>
                        
                    </div>
                    @endif
                    @endforeach
                    
                </div>
            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

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

    // Lấy trạng thái duyệt của task trong cùng row
    let approved = row.find('td:nth-child(5) span').hasClass('bg-success'); 

    if (approved) {
        Swal.fire('Không thể xóa!', 'Tác vụ đã được duyệt, không thể xóa.', 'warning');
        return; // thoát, không xóa
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
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status) {
                        row.fadeOut(300, function() {
                            $(this).remove();
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




@endsection