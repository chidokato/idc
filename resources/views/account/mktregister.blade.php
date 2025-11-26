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
                <table>
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Dự án</th>
                                <th>Kênh</th>
                                <th>Ngân sách/ngày</th>
                                <th>Ghi chú</th>
                                <th>Thời gian chạy</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-control">
                                        <option>---</option>
                                        <option>Happy One Mori</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control">
                                        <option>---</option>
                                        <option>Facebook</option>
                                        <option>Google</option>
                                    </select>
                                </td>
                                <td><select class="form-control">
                                        <option>---</option>
                                        <option>500.000 đ</option>
                                        <option>1.000.000 đ</option>
                                    </select></td>
                                <td><input class="form-control" type="text" name="" placeholder="Hỗ trợ, chạy chung ..."></td>
                                <td><input class="form-control" readonly type="text" value="1 - 15/12/2025"></td>
                                <td class="flex">
                                    <button class="form-control del">xóa</button>
                                    <button class="form-control dupple">Nhân</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </table>
            </div>
            
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('js')
<script>
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
    let clone = row.clone(true); // copy cả event

    // Giữ nguyên dữ liệu trong input + select
    clone.find('select').each(function(i) {
        $(this).val(row.find('select').eq(i).val());
    });

    clone.find('input').each(function(i) {
        $(this).val(row.find('input').eq(i).val());
    });

    // Gắn clone ngay sau row gốc
    row.after(clone);
});
</script>

@endsection