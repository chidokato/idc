@extends('admin.layout.main')

@section('css')
<link href="admin_asset/css/custom.css" rel="stylesheet">
<style>
    #post-data td:last-child {
        position: relative;
        z-index: 2;
    }
</style>
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
                    <input type="text" id="quickCreateProjectKeyword" class="form-control" placeholder="Nhập tên dự án hoặc chọn dự án có sẵn" autocomplete="off">
                    <div id="quickCreateProjectSuggestions" class="list-group mt-2" style="max-height: 260px; overflow-y: auto;"></div>
                    <select id="quickCreateProjectSelect" class="form-control d-none" style="width: 100%;">
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
                            <option value="20" {{ request()->per_page == 20 || !request()->has('per_page') ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request()->per_page == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request()->per_page == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <div> Từ {{ $posts->firstItem() }} đến {{ $posts->lastItem() }} trên tổng {{ $posts->total() }} </div>
                        {{ $posts->appends(request()->all())->links() }}
                    </div>
                    </form>
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
                            <tr id="post" data-id="{{$val->id}}">
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
        const addNewButton = document.querySelector('a.add-iteam button');
        const quickCreateModal = document.getElementById('quickCreatePostModal');
        const quickCreateProjectId = document.getElementById('quick_create_project_id');
        const quickCreateProjectName = document.getElementById('quick_create_project_name');
        const quickCreateForm = document.getElementById('quickCreatePostForm');
        const quickCreateKeyword = document.getElementById('quickCreateProjectKeyword');
        const quickCreateSuggestions = document.getElementById('quickCreateProjectSuggestions');
        const projectOptions = @json($projectSelect2Data);
        let quickCreateBackdrop = null;

        if (!quickCreateModal || !quickCreateProjectId || !quickCreateProjectName || !quickCreateForm) {
            return;
        }

        function openQuickCreateModal() {
            if (!quickCreateModal) {
                return;
            }

            quickCreateModal.style.display = 'block';
            quickCreateModal.classList.add('show');
            quickCreateModal.setAttribute('aria-modal', 'true');
            quickCreateModal.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');

            if (!quickCreateBackdrop) {
                quickCreateBackdrop = document.createElement('div');
                quickCreateBackdrop.className = 'modal-backdrop fade show';
            }

            document.body.appendChild(quickCreateBackdrop);
        }

        function closeQuickCreateModal() {
            if (!quickCreateModal) {
                return;
            }

            quickCreateModal.style.display = 'none';
            quickCreateModal.classList.remove('show');
            quickCreateModal.setAttribute('aria-hidden', 'true');
            quickCreateModal.removeAttribute('aria-modal');
            document.body.classList.remove('modal-open');

            if (quickCreateBackdrop && quickCreateBackdrop.parentNode) {
                quickCreateBackdrop.parentNode.removeChild(quickCreateBackdrop);
            }
        }

        if (addNewLink) {
            addNewLink.setAttribute('href', '#');
            addNewLink.setAttribute('onclick', "return false;");
            addNewLink.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                openQuickCreateModal();
            });
        }

        if (addNewButton) {
            addNewButton.setAttribute('onclick', "return false;");
            addNewButton.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                openQuickCreateModal();
            });
        }

        if (false) {
            const projectSelect2Data = @json($projectSelect2Data);
            quickCreateSelect.empty();
            quickCreateSelect.append(new Option('', '', false, false));
            projectSelect2Data.forEach(function (project) {
                quickCreateSelect.append(new Option(project.text, project.id, false, false));
            });
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
                },
                language: {
                    searching: function () {
                        return 'Dang tim kiem...';
                    },
                    noResults: function () {
                        return 'Khong co du an phu hop';
                    },
                    inputTooShort: function () {
                        return 'Nhap ten du an de tim';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            quickCreateSelect.on('change', function () {
                const selectedValue = $(this).val();
                if (!selectedValue) {
                    quickCreateProjectId.value = '';
                    quickCreateProjectName.value = '';
                    return;
                }

                if (/^\d+$/.test(String(selectedValue))) {
                    quickCreateProjectId.value = selectedValue;
                    quickCreateProjectName.value = '';
                    return;
                }

                quickCreateProjectId.value = '';
                quickCreateProjectName.value = selectedValue;
            });
        }

        function renderProjectSuggestions(keyword) {
            if (!quickCreateSuggestions) {
                return;
            }

            const normalizedKeyword = (keyword || '').trim().toLowerCase();
            quickCreateSuggestions.innerHTML = '';

            const matchedProjects = projectOptions.filter(function (project) {
                if (normalizedKeyword === '') {
                    return true;
                }

                return (project.text || '').toLowerCase().includes(normalizedKeyword);
            }).slice(0, 20);

            if (!matchedProjects.length) {
                quickCreateSuggestions.innerHTML = '<div class="list-group-item text-muted">Khong co du an phu hop</div>';
                return;
            }

            matchedProjects.forEach(function (project) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'list-group-item list-group-item-action';
                button.textContent = project.text;
                button.addEventListener('click', function () {
                    quickCreateProjectId.value = project.id;
                    quickCreateProjectName.value = '';
                    if (quickCreateKeyword) {
                        quickCreateKeyword.value = project.text;
                    }
                    quickCreateSuggestions.innerHTML = '';
                });
                quickCreateSuggestions.appendChild(button);
            });
        }

        if (quickCreateKeyword) {
            quickCreateKeyword.addEventListener('focus', function () {
                renderProjectSuggestions(this.value);
            });

            quickCreateKeyword.addEventListener('input', function () {
                quickCreateProjectId.value = '';
                quickCreateProjectName.value = this.value.trim();
                renderProjectSuggestions(this.value);
            });
        }

        if (quickCreateForm) {
            quickCreateForm.addEventListener('submit', function (event) {
                if (quickCreateKeyword && !quickCreateProjectId.value) {
                    quickCreateProjectName.value = quickCreateKeyword.value.trim();
                }

                if (!quickCreateProjectId.value && !quickCreateProjectName.value) {
                    event.preventDefault();
                    alert('Vui lòng chọn hoặc nhập tên dự án');
                }
            });
        }

        quickCreateModal.querySelectorAll('[data-dismiss="modal"], .close').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                closeQuickCreateModal();
            });
        });

        quickCreateModal.addEventListener('click', function (event) {
            if (event.target === quickCreateModal) {
                closeQuickCreateModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeQuickCreateModal();
            }
        });

        function resetQuickCreateModal() {
            quickCreateProjectId.value = '';
            quickCreateProjectName.value = '';
            if (quickCreateKeyword) {
                quickCreateKeyword.value = '';
            }
            if (quickCreateSuggestions) {
                quickCreateSuggestions.innerHTML = '';
            }
        }

        if (quickCreateModal) {
            const observer = new MutationObserver(function () {
                if (!quickCreateModal.classList.contains('show')) {
                    resetQuickCreateModal();
                }
            });

            observer.observe(quickCreateModal, { attributes: true, attributeFilter: ['class'] });
        }
    });
</script>

<script>
    $(document).on('click', 'a.add-iteam, a.add-iteam button', function (event) {
        event.preventDefault();
        event.stopPropagation();
        const quickCreateModal = document.getElementById('quickCreatePostModal');
        if (quickCreateModal) {
            quickCreateModal.style.display = 'block';
            quickCreateModal.classList.add('show');
            quickCreateModal.setAttribute('aria-modal', 'true');
            quickCreateModal.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
            if (!document.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }
    });
</script>

@endsection
