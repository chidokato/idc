@extends('admin.layout.main')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
@endsection
@section('js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script type="text/javascript">
    let table = new DataTable('#myTable');
</script>
@endsection

@section('content')
@include('admin.alert')

<form method="post" action="{{route('promotion.store')}}">
@csrf
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed">
    <button type="button" id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
    <ul class="navbar-nav ">
        <li class="nav-item"> <a class="nav-link line-1" href="{{route('promotion.index')}}" ><i class="fa fa-chevron-left" aria-hidden="true"></i> <span class="mobile-hide">Quay lại</span> </a> </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item mobile-hide">
            <button type="reset" class="btn-danger mr-2 form-control"><i class="fas fa-sync"></i> Làm mới</button>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item">
            <button type="submit" class="btn-success form-control"><i class="far fa-save"></i> Lưu lại</button>
        </li>
    </ul>
</nav>

<div class="d-sm-flex align-items-center justify-content-between mb-3 flex" style="height: 38px;">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Thêm mới</h2>
</div>

<div class="row">
  <div class="col-xl-9 col-lg-9">
        <div class="card shadow mb-2">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#setup">Cấu hình</a></li>
                    <li><a data-toggle="tab" class="nav-link" href="#select_pro">Chọn sản phẩm</a></li>
                </ul>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="setup">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label>Name</label>
                                  <input name="name" placeholder="Name" type="text" class="form-control">
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Content</label>
                                  <textarea name="content" rows="3" class="form-control" ></textarea>
                              </div>
                          </div>
                          
                      </div>
                  </div>
                </div>
                <div class="tab-pane " id="select_pro">
                    <div class="card-body">
                        <table id="myTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                </tr>   
                            </thead>
                            <tbody>
                                @foreach($posts as $val)
                                <tr>
                                    <td>
                                        <img class="admin-thumbnail" src="data/news/{{$val->img}}">
                                    </td>
                                    <td>{{$val->id}}</td>
                                    <td>
                                        <div class="" style="max-width: 600px">
                                            <label class="text-truncate-set text-truncate-set-1"><input type="checkbox" value="{{$val->id}}" name="p_id[]"> {{$val->name}}</label>
                                        </div>
                                    </td>
                                    <td>{{$val->category->name}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">SEO option</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                          <label>Title</label>
                          <input name="title" placeholder="Tiêu đề SEO" type="text" class="form-control">
                      </div>
                  </div>
                  <div class="col-md-12">
                      <div class="form-group">
                          <label>Description</label>
                          <input name="description" placeholder="Mô tả SEO" type="text" class="form-control">
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Images</h6>
            </div>
            <div class="card-body">
                <div class="file-upload">
                    <div class="file-upload-content" onclick="$('.file-upload-input').trigger( 'click' )">
                        <img class="file-upload-image" src="data/no_image.jpg" />
                    </div>
                    <div class="image-upload-wrap">
                        <input name="img" class="file-upload-input" type='file' onchange="readURL(this);" accept="image/*" />
                    </div>
                </div>
            </div>
        </div>
      </div>
</div>
</form>
@endsection