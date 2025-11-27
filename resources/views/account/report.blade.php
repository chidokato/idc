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
            <div class="col-lg-3 d-none d-lg-block">
                @include('account.layout.sitebar')
            </div>

            <div class="col-lg-9">

    <form id="report-form">
        @csrf
        <input type="hidden" name="id" id="report_id">

        <table class="table">
            <tr>
                <td><input class="form-control" name="name" type="text" placeholder="Tên báo cáo"></td>
                <td><input class="form-control" name="date" id="date-range" type="text" placeholder="Thời gian"></td>
                <td>
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                </td>
            </tr>
        </table>
    </form>

    <div id="load_report"></div>

</div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(function() {
    $('#date-range').daterangepicker({
        autoUpdateInput: false, // không tự điền trước
        locale: {
            cancelLabel: 'Xóa',
            format: 'DD/MM/YYYY',
            applyLabel: 'Chọn',
        },
        opens: 'left'
    });

    // Khi người dùng chọn xong
    $('#date-range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    // Khi cancel
    $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});
</script>

<script>
$(document).ready(function () {

    // LOAD REPORT LIST
    function loadReport() {
        $.get("{{ route('account.loadReport') }}", function (data) {
            $('#load_report').html(data);
        });
    }
    loadReport();


    // ========================= SUBMIT FORM =========================
    $('#report-form').on('submit', function(e){
        e.preventDefault();

        let id = $('#report_id').val();

        let url = id
            ? "{{ route('account.report.update') }}"   // UPDATE
            : "{{ route('account.report.store') }}";  // STORE

        $.ajax({
            url: url,
            type: "POST",
            data: $(this).serialize(),
            success: function(response){
                showToast(
                    'success',
                    response.message ? response.message : "Đã thực hiện thành công!"
                );
                $('#report-form')[0].reset();
                $('#report_id').val('');
                loadReport();
            },
            error: function(xhr){
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMsg = Object.values(xhr.responseJSON.errors).join("<br>");
                    showCenterError(errorMsg);
                    return;
                }
                showCenterError("Có lỗi xảy ra, vui lòng thử lại!");
            }

        });
    });


    // ========================= DELETE =========================
    $(document).on('click', '.del', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: "Bạn có chắc muốn xóa?",
            text: "Thao tác này không thể hoàn tác!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Xóa",
            cancelButtonText: "Hủy",
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {

            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('account.report.delete') }}",
                    method: "POST",
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        showToast('success', "Đã xóa báo cáo");
                        loadReport();
                    },
                    error: function () {
                        showCenterError("Không thể xóa báo cáo, vui lòng thử lại!");
                    }
                });
            }
        });
    });



    // ========================= EDIT =========================
    $(document).on('click', '.edit', function(){

        let tr = $(this).closest('tr');

        let id   = tr.data('id');
        let name = tr.find('.r-name').text();
        let date = tr.find('.r-date').text(); // "01/11/2025 - 15/11/2025"

        $('#report_id').val(id);
        $('input[name="name"]').val(name);
        $('input[name="date"]').val(date);
    });

});
</script>


<script>
    $(document).on('change', '.active-toggle', function() {
        let id = $(this).data('id');
        let active = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('account.report.active') }}",
            method: "POST",
            data: {
                id: id,
                active: active
            },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response){
                showToast('success', response.message ?? "Đã thực hiện thành công");
                $('#report-form')[0].reset();
                $('#report_id').val('');
                loadReport();
            },
            error: function(xhr){
                showCenterError("Có lỗi xảy ra, vui lòng thử lại!");
            }

        });
    });

</script>


@endsection