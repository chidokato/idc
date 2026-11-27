@extends('admin.layout.main')

@section('content')
@include('admin.alert')

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Cấu hình Link Phần mềm SSO</h1>
            <p class="mb-0 text-muted small">
                Quản lý đường dẫn (URL), tên hiển thị, icon và trạng thái các phần mềm trên trang chọn phần mềm (<code>/sso/apps</code>).
            </p>
        </div>
        <div>
            <a href="{{ route('sso.apps') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-external-link-alt mr-1"></i> Xem trang /sso/apps
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách phần mềm hiện có</h6>
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addSsoAppModal">
                <i class="fas fa-plus mr-1"></i> Thêm phần mềm
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sso-apps.update-all') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 70px;">Thứ tự</th>
                                <th style="width: 90px;">Mã (Key)</th>
                                <th style="width: 210px;">Tên phần mềm &amp; Nhãn</th>
                                <th style="min-width: 260px;">Link URL gốc &amp; Callback</th>
                                <th style="min-width: 220px;">Mô tả</th>
                                <th style="width: 150px;">Icon &amp; Màu</th>
                                <th style="width: 80px;" class="text-center">Hiển thị</th>
                                <th style="width: 60px;" class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apps as $app)
                                <tr>
                                    <td>
                                        <input type="number" name="apps[{{ $app->id }}][sort_order]" class="form-control form-control-sm" value="{{ $app->sort_order }}">
                                    </td>
                                    <td>
                                        <span class="badge badge-dark">{{ $app->app_key }}</span>
                                    </td>
                                    <td>
                                        <input type="text" name="apps[{{ $app->id }}][name]" class="form-control form-control-sm mb-1 font-weight-bold" value="{{ $app->name }}" placeholder="Tên hiển thị" required>
                                        <input type="text" name="apps[{{ $app->id }}][short]" class="form-control form-control-sm" value="{{ $app->short }}" placeholder="Tên ngắn" required>
                                    </td>
                                    <td>
                                        <input type="url" name="apps[{{ $app->id }}][url]" class="form-control form-control-sm mb-1" value="{{ $app->url }}" placeholder="https://..." required>
                                        <input type="text" name="apps[{{ $app->id }}][callback_path]" class="form-control form-control-sm text-muted" value="{{ $app->callback_path }}" placeholder="/sso/callback">
                                    </td>
                                    <td>
                                        <textarea name="apps[{{ $app->id }}][description]" class="form-control form-control-sm" rows="2">{{ $app->description }}</textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="apps[{{ $app->id }}][icon]" class="form-control form-control-sm mb-1" value="{{ $app->icon }}" placeholder="fa-solid fa-cube">
                                        <div class="d-flex align-items-center">
                                            <input type="color" name="apps[{{ $app->id }}][color]" class="form-control form-control-sm p-0 mr-1" style="width: 42px; height: 28px;" value="{{ $app->color }}">
                                            <small class="text-muted">{{ $app->color }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="apps[{{ $app->id }}][is_active]" value="1" {{ $app->is_active ? 'checked' : '' }} style="width: 18px; height: 18px;">
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="if(confirm('Xóa phần mềm {{ $app->name }}?')) document.getElementById('del-app-{{ $app->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="far fa-save mr-1"></i> Lưu cấu hình tất cả phần mềm
                    </button>
                </div>
            </form>

            @foreach($apps as $app)
                <form id="del-app-{{ $app->id }}" action="{{ route('admin.sso-apps.destroy', $app->id) }}" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        </div>
    </div>
</div>

{{-- Modal thêm phần mềm mới --}}
<div class="modal fade" id="addSsoAppModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.sso-apps.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Thêm phần mềm vào cổng SSO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="small font-weight-bold">Mã định danh (app_key) <span class="text-danger">*</span></label>
                    <input type="text" name="app_key" class="form-control" placeholder="VD: voice, hrm, erp..." required>
                </div>
                <div class="row">
                    <div class="col-md-7 form-group">
                        <label class="small font-weight-bold">Tên phần mềm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Indochine Voice" required>
                    </div>
                    <div class="col-md-5 form-group">
                        <label class="small font-weight-bold">Tên ngắn <span class="text-danger">*</span></label>
                        <input type="text" name="short" class="form-control" placeholder="VD: Voice" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="small font-weight-bold">URL gốc <span class="text-danger">*</span></label>
                    <input type="url" name="url" class="form-control" placeholder="https://voice.indochinerealestate.vn" required>
                </div>
                <div class="form-group">
                    <label class="small font-weight-bold">Callback Path</label>
                    <input type="text" name="callback_path" class="form-control" value="/sso/callback">
                </div>
                <div class="form-group">
                    <label class="small font-weight-bold">Mô tả</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-8 form-group">
                        <label class="small font-weight-bold">Icon (FontAwesome)</label>
                        <input type="text" name="icon" class="form-control" value="fa-solid fa-cube">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold">Màu sắc</label>
                        <input type="color" name="color" class="form-control p-1" value="#1a2c5e">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Thêm phần mềm</button>
            </div>
        </form>
    </div>
</div>
@endsection
