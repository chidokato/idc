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
                    <form action="{{ route('account.tasksstore') }}" method="POST">
                        @csrf
                        <input type="hidden" name="" value="">
                        <table class="table" id="myTable">
                            <thead>
                                <tr>
                                    <th>Dự án <span class="required">(*)</span></th>
                                    <th>Kênh <span class="required">(*)</span></th>
                                    <th>Ngân sách/ngày <span class="required">(*)</span></th>
                                    <th>Ghi chú</th>
                                    <th>Thời gian chạy</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="post_id[]" required class="form-control">
                                            <option value="">---</option>
                                            @foreach($posts as $val)
                                            <option value="{{$val->id}}">{{$val->name}}</option>
                                            @endforeach
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
                                    <td class="flex">
                                        <button type="button" class="form-control del">xóa</button>
                                        <button type="button" class="form-control dupple">Nhân</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div>
                            <p><button type="submit" class="">Gửi đăng ký MARKETING</button></p>
                            <p><span class="required">- Chú ý: Cổng đăng ký chi phí marketing sẽ <strong>ĐÓNG</strong> vào <strong>0h00 ngày 20/12/2025</strong>.</span></p>
                            <p><span class="required">- Hãy kiểm tra thật kỹ trước khi gửi. Sau khi gửi đăng ký MARKETING bạn sẽ <strong>KHÔNG</strong> còn quyền sửa và xóa (liên hệ Admin để sửa/xóa) những nội dung đã gửi. </span></p>
                            <p class="required">- Có thể gửi nhiều lần trước khi cổng đăng ký đóng lại. </p>
                        </div>
                    </form>
                    <hr>
                    @foreach($reports as $report)
                    <div>
                        <h3>{{$report->name}} ({{ \Carbon\Carbon::parse($report->time_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($report->time_end)->format('d/m/Y') }})</h3>
                        <table class="table">
                            <tr>
                                <th>Dự án</th>
                                <th>Kênh</th>
                                <th>Ngân sách</th>
                                <th>Ghi chú</th>
                            </tr>
                            @foreach($report->Task as $val)
                            <tr>
                                <td>{{$val->Post?->name}}</td>
                                <td>{{$val->Channel?->name}}</td>
                                <td>{{ number_format($val->expected_costs, 0, ',', '.') }} đ</td>
                                <td>{{ $val->content }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
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



@endsection