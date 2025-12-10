@extends('admin.layout.main')

@section('css')
<link href="admin_asset/css/custom.css" rel="stylesheet">
@endsection
@section('content')
@include('admin.layout.header')
@include('admin.alert')
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý sản phẩm</h2>
    <div class="flex">
        <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-upload" aria-hidden="true"></i> Upload Excel</button>
        <a class="add-iteam" href="{{route('post.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> Thêm mới</button></a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('post.upfile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nhập file dữ liệu tác vụ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="chuy">
                        <p>Bạn vui lòng kiểm tra và tải lại file mới để tránh sai lệch dữ liệu</p>
                    </div>
                    <ul>
                        <li>File có dung lượng tối đa là 3MB và 5000 dòng</li>
                    </ul>
                    <label for="excel-file" id="custom-file-label" class="custom-file-upload">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24" font-size="24" color="#747C87" style="margin-right: 10px;">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 4a8.003 8.003 0 0 1 7.763 6.058A5.001 5.001 0 0 1 19 20H6a6 6 0 0 1-.975-11.921A7.997 7.997 0 0 1 12 4Zm-6.652 6.053.948-.155.472-.837a6.003 6.003 0 0 1 11.054 1.481l.322 1.291 1.316.202A3.001 3.001 0 0 1 19 18H6a4 4 0 0 1-.652-7.947Z" fill="#747C87"></path>
                            <path d="M13.45 12H16l-4 4-4-4h2.55V9h2.9v3Z" fill="#747C87"></path>
                        </svg>
                        <span id="file-label-text">Kéo thả file vào đây hoặc tải lên từ thiết bị</span>
                    </label>
                    <input id="excel-file" type="file" name="excel_file" accept=".xls,.xlsx,.csv" required style="display: none;">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
<form class="width100" action="{{ url()->current() }}" method="GET">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#tab1">Tất cả</a></li>
                    <li style="padding-left:15px;display: flex; align-items: center;">
                        <input type="text" id="quickSearch" class="form-control" placeholder="Tìm kiếm nhanh..." style="width:200px;">
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane overflow active" id="tab2">
                    @if(count($posts) > 0)
                    <div class="search paginate-search">
                        <div>Hiển thị: </div>
                        <select class="form-control paginate" name="per_page" onchange="this.form.submit()">
                            <option value="10" {{ request()->per_page == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request()->per_page == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request()->per_page == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request()->per_page == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <div> Từ {{ $posts->firstItem() }} đến {{ $posts->lastItem() }} trên tổng {{ $posts->total() }} </div>
                        {{ $posts->appends(request()->all())->links() }}
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Hỗ trợ MKT</th>
                                <th>Status</th>
                                <th>User</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $val)
                            <tr>
                                <td><input style="width: 100%;" class="change-input" type="text" name="name" value="{{ $val->name }}" data-id="{{ $val->id }}"></td>
                                <td>
                                    <select class="rate-select" data-id="{{ $val->id }}">
                                        <option value="">...</option>
                                        <option value="100" {{ $val->rate == '100' ? 'selected' : '' }}>100%</option>
                                        <option value="95" {{ $val->rate == '95' ? 'selected' : '' }}>95%</option>
                                        <option value="90"  {{ $val->rate == '90' ? 'selected' : '' }}>90%</option>
                                        <option value="80"  {{ $val->rate == '80' ? 'selected' : '' }}>80%</option>
                                        <option value="70"  {{ $val->rate == '70' ? 'selected' : '' }}>70%</option>
                                        <option value="60"  {{ $val->rate == '60' ? 'selected' : '' }}>60%</option>
                                        <option value="50"  {{ $val->rate == '50' ? 'selected' : '' }}>50%</option>
                                        <option value="30"  {{ $val->rate == '30' ? 'selected' : '' }}>30%</option>
                                        <option value="0"  {{ $val->rate == '0' ? 'selected' : '' }}>0%</option>
                                    </select>
                                </td>
                                <td>{{date_format($val->updated_at,"d/m/Y")}}</td>
                                <td>{{$val->User?->yourname}}</td>
                                <td style="display: flex;">
                                    <!-- <a href="{{route('post.edit',[$val->id])}}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a> -->
                                    <form action="{{route('post.destroy', [$val->id])}}" method="POST">
                                      @method('DELETE')
                                      @csrf
                                      <button class="button_none" onclick="return confirm('Bạn muốn xóa bản ghi ?')"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</form>

@endsection

@section('js')
<script>
$(document).on('change', '.rate-select', function() {

    let id = $(this).data('id');
    let rate = $(this).val();

    $.ajax({
        url: "{{ route('duan.updateRate') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            rate: rate
        },
        success: function(response){
            if(response.success){
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        },
        error: function(xhr){
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: xhr.responseJSON?.message || 'Có lỗi xảy ra!',
                confirmButtonText: 'Đã hiểu'
            });
        }
    });

});
</script>
<script>
    $(document).ready(function(){
        // Chặn Enter ở tất cả input.change-input
        $(document).on('keydown', '.change-input', function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                return false;
            }
        });
        
        $('.change-input').on('blur', function(){
            var id = $(this).data('id');
            var name = $(this).val();

            $.ajax({
                url: 'admin/duan/' + id + '/update-name', // route mình sẽ tạo
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name
                },
                success: function(response){
                    if(response.success){
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: xhr.responseJSON?.message || 'Có lỗi xảy ra!',
                        confirmButtonText: 'Đã hiểu'
                    });
                }
            });
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const search = document.getElementById("quickSearch");

    // Chặn Enter
    search.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault(); // chặn submit form
            return false;
        }
    });

    search.addEventListener("keyup", function () {
        const keyword = this.value.toLowerCase();

        document.querySelectorAll("#tab2 table tbody tr").forEach(row => {

            // Lấy text của toàn row (text, date, user...)
            let rowText = row.innerText.toLowerCase();

            // Lấy tất cả input trong row
            row.querySelectorAll("input").forEach(inp => {
                rowText += " " + inp.value.toLowerCase();
            });

            // Lấy tất cả select trong row
            row.querySelectorAll("select").forEach(sel => {
                rowText += " " + sel.options[sel.selectedIndex].text.toLowerCase();
            });

            // So sánh
            row.style.display = rowText.includes(keyword) ? "" : "none";
        });

    });

});
</script>




@endsection