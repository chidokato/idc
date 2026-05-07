@extends('admin.layout.main')

@section('css')
<link href="admin_asset/css/custom.css" rel="stylesheet">
@endsection
@section('content')
@include('admin.layout.header')
@include('admin.alert')
<?php use App\Models\Category; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý sản phẩm</h2>
    <div class="flex">
        <a class="add-iteam" href="{{route('post.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> Thêm mới</button></a>
    </div>
</div>

<div class="modal fade" id="quickCreatePostModal" tabindex="-1" role="dialog" aria-labelledby="quickCreatePostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="quickCreatePostForm" action="{{ route('post.quickCreate') }}" method="POST">
                @csrf
                <input type="hidden" name="project_id" id="quick_create_project_id">
                <input type="hidden" name="project_name" id="quick_create_project_name">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickCreatePostModalLabel">Thêm mới dự án</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Nếu muốn thêm mới một dự án, hãy tìm kiếm xem dự án đó đã có chưa.</p>
                    <select id="quickCreateProjectSelect" class="form-control" style="width: 100%;">
                        <option value="">Nhập tên dự án hoặc chọn dự án có sẵn</option>
                        @foreach($projectOptions as $projectOption)
                        <option value="{{ $projectOption->id }}" data-name="{{ $projectOption->name }}">{{ $projectOption->name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted mt-2">Nếu chưa có trong danh sách, chỉ cần nhập tên mới rồi bấm tiếp tục.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Tiếp tục</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <!-- Modal -->
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
</div> --}}

<div class="row">
<form class="width100" action="{{ url()->current() }}" method="GET">
    <input type="hidden" name="status_filter" value="{{ request('status_filter', 'public') }}">
    <div class="col-xl-12 col-lg-12 search flex-start">
        <input type="text" value="{{ request()->key ?? '' }}" placeholder="Tìm kiếm..." class="form-control" name="key" onchange="this.form.submit()">
        <select class="form-control" name="category_id">
            <option value="">...</option>
            @foreach($category as $val)
            <option {{isset(request()->category_id) && request()->category_id== $val->id ? 'selected':''}} value="{{$val->id}}">{{$val->name}}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-success mr-2">Tìm kiếm</button>
        <a href="{{ url()->current() }}" class="btn btn-warning">
            Reset
        </a>
    </div>
    
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#tab1">Tất cả</a></li>
                    <!-- <li><a data-toggle="tab" class="nav-link " href="#tab2">Hiển thị</a></li> -->
                    <!-- <li><a data-toggle="tab" class="nav-link" href="#tab3">Ẩn</a></li> -->
                    <li><a class="nav-link {{ request('status_filter') === 'public' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status_filter' => 'public', 'page' => 1]) }}">Public</a></li>
                    <li><a class="nav-link {{ request('status_filter') === 'hidden' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status_filter' => 'hidden', 'page' => 1]) }}">Hidden</a></li>
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
                                <th></th>
                                <th>Name</th>
                                <th></th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Hot</th>
                                <th>Status</th>
                                <th>date</th>
                                <th>User</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="post-data">
                            @foreach($posts as $val)
                            <tr id="post">
                                <input type="hidden" name="id" id="id" value="{{$val->id}}" >
                                <td class="thumb"><img src="data/images/{{$val->img}}"></td>
                                <td>
                                    <div class="name"><a href="{{route('post.edit',[$val->id])}}" >{{$val->name}}</a></div>
                                    
                                </td>
                                <td><div class="slug">{{$val->slug}}</div></td>
                                <td>{{ $val->price ? number_format($val->price) : ''}} 
                                    <div class="slug" style="color:red">{{$val->sale?'sale: '.$val->sale.'%':''}}</div>
                                </td>
                                <td>{{$val->category?->name}}</td>
                                <td>
                                    <label class="container"><input <?php if($val->hot == 'true'){echo "checked";} ?> type="checkbox" id='hot_post' ><span class="checkmark"></span></label>
                                </td>
                                <td>
                                    <label class="container"><input <?php if($val->status == 'true'){echo "checked";} ?> type="checkbox" id='status_post' ><span class="checkmark"></span></label>
                                </td>
                                <td>{{date_format($val->updated_at,"d/m/Y")}}</td>
                                <td>{{ $val->user?->yourname }}</td>
                                <td style="display: flex;">
                                    <!-- <a href="{{route('post_up', [$val->id])}}" class="mr-3"><i class="fas fa-arrow-up" aria-hidden="true"></i></a>  -->
                                    <a href="{{route('post.edit',[$val->id])}}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>
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
    const excelFileInput = document.getElementById("excel-file"); if (excelFileInput) excelFileInput.addEventListener("change", function () {
        let fileLabel = document.getElementById("file-label-text");
        fileLabel.textContent = this.files.length > 0 ? this.files[0].name : "Kéo thả file vào đây hoặc tải lên từ thiết bị";
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const currentUrl = new URL(window.location.href);
        const statusFilter = currentUrl.searchParams.get('status_filter') || 'public';
        const navLinks = document.querySelectorAll('.nav.nav-pills .nav-link');
        const allTab = navLinks[0];

        if (allTab) {
            currentUrl.searchParams.set('status_filter', 'all');
            currentUrl.searchParams.set('page', '1');
            allTab.removeAttribute('data-toggle');
            allTab.setAttribute('href', currentUrl.toString());
            allTab.classList.toggle('active', statusFilter === 'all');
        }

        navLinks.forEach(function (link, index) {
            if (index > 0) {
                link.classList.toggle('active', link.textContent.trim().toLowerCase() === statusFilter);
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addNewLink = document.querySelector('a.add-iteam');
        const quickCreateProjectId = document.getElementById('quick_create_project_id');
        const quickCreateProjectName = document.getElementById('quick_create_project_name');
        const quickCreateForm = document.getElementById('quickCreatePostForm');
        const quickCreateSelect = $('#quickCreateProjectSelect');

        if (addNewLink) {
            addNewLink.addEventListener('click', function (event) {
                event.preventDefault();
                $('#quickCreatePostModal').modal('show');
            });
        }

        if (quickCreateSelect.length) {
            quickCreateSelect.select2({
                tags: true,
                width: '100%',
                dropdownParent: $('#quickCreatePostModal'),
                placeholder: 'Nhập tên dự án hoặc chọn dự án có sẵn',
                createTag: function (params) {
                    const term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            quickCreateSelect.on('change', function () {
                const selectedValue = $(this).val();
                const selectedOption = this.options[this.selectedIndex];

                if (!selectedValue) {
                    quickCreateProjectId.value = '';
                    quickCreateProjectName.value = '';
                    return;
                }

                if (selectedOption && selectedOption.dataset && selectedOption.dataset.name) {
                    quickCreateProjectId.value = selectedValue;
                    quickCreateProjectName.value = '';
                    return;
                }

                quickCreateProjectId.value = '';
                quickCreateProjectName.value = selectedValue;
            });
        }

        if (quickCreateForm) {
            quickCreateForm.addEventListener('submit', function (event) {
                if (!quickCreateProjectId.value && !quickCreateProjectName.value) {
                    event.preventDefault();
                    alert('Vui lòng chọn hoặc nhập tên dự án');
                }
            });
        }

        $('#quickCreatePostModal').on('hidden.bs.modal', function () {
            if (quickCreateSelect.length) {
                quickCreateSelect.val(null).trigger('change');
            }

            quickCreateProjectId.value = '';
            quickCreateProjectName.value = '';
        });
    });
</script>

@endsection
