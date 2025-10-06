@extends('admin.layout.main')

@section('content')
@include('admin.alert')

<form method="POST" action="{{route('setting.update', [$data->id])}}" enctype="multipart/form-data">
@csrf
@method('PUT')
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed">
    <button type="button" id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
    <ul class="navbar-nav ">
        <!-- <li class="nav-item"> <a class="nav-link line-1" href="{{route('setting.index')}}" ><i class="fa fa-chevron-left" aria-hidden="true"></i> <span class="mobile-hide">Quay lại</span> </a> </li> -->
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
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Cấu hình website</h2>
</div>

<div class="row">
  <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Cấu hình</h6>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Name</label>
                                  <input value="{{$data->name}}" name="name" placeholder="..." type="text" class="form-control">
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>address</label>
                                  <input value="{{$data->address}}" name="address" placeholder="..." type="text" class="form-control">
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>footer</label>
                                  <textarea class="form-control" rows="5" name="footer">{!! $data->footer !!}</textarea>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>footer</label>
                                  <textarea class="form-control" rows="5" name="header">{!! $data->footer !!}</textarea>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">SEO</h6>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>title</label>
                                  <input value="{{$data->title}}" name="title" placeholder="..." type="text" class="form-control">
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>description</label>
                                  <input value="{{$data->description}}" name="description" placeholder="..." type="text" class="form-control">
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>

        <!-- <div class="card shadow mb-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ghi chú khi đăng ký</h6>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <textarea name="register" class="form-control" id="ckeditor">{{$data->register}}</textarea>
                              </div>
                          </div>
                          
                      </div>
                  </div>
                </div>
            </div>
        </div> -->

        <!-- <div class="card shadow mb-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ghi chú đặt đơn hàng</h6>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <textarea name="content_cart" class="form-control" id="ckeditor1">{{$data->content_cart}}</textarea>
                              </div>
                          </div>
                          
                      </div>
                  </div>
                </div>
            </div>
        </div> -->

        
    </div>
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin chung</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- <div class="col-md-12">
                        <div class="form-group">
                            <label>Tỉ giá Yên Nhật (¥1 = ₫...)</label>
                            <input value="{{$data->exchange}}" name="exchange" placeholder="..." type="text" class="form-control">
                        </div>
                    </div> -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>hotline</label>
                            <input value="{{$data->hotline}}" name="hotline" placeholder="..." type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>email</label>
                            <input value="{{$data->email}}" name="email" placeholder="..." type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>facebook</label>
                            <input value="{{$data->facebook}}" name="facebook" placeholder="..." type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>youtube</label>
                            <input value="{{$data->youtube}}" name="youtube" placeholder="..." type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                      <label>maps</label>
                        <div class="form-group">
                            <textarea class="form-control" rows="6" name="maps">{!! $data->maps !!}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                      <label>img</label>
                        <div class="form-group">
                            <input name="img" placeholder="..." type="file" >
                        </div>
                    </div>
                    <div class="col-md-6">
                      <label>favicon</label>
                        <div class="form-group">
                            <input name="favicon" placeholder="..." type="file" >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img style="height: 100px; max-width: 100%" src="data/images/{{$data->img}}">
                    </div>
                    <div class="col-md-6">
                        <img style="height: 30px; max-width: 100%" src="data/images/{{$data->favicon}}">
                    </div>
                </div>
            </div>

        </div>

        

        

      </div>
</div>
</form>
@endsection