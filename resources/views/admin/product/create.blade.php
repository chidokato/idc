@extends('admin.layout.main')

@section('content')
@include('admin.alert')
<?php use App\Models\Category; ?>
<form method="post" action="{{route('product.store')}}" enctype="multipart/form-data">
@csrf
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed">
    <button type="button" id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
    <ul class="navbar-nav ">
        <li class="nav-item"> <a class="nav-link line-1" href="{{route('product.index')}}" ><i class="fa fa-chevron-left" aria-hidden="true"></i> <span class="mobile-hide">Quay lại</span> </a> </li>
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
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin</h6>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="vi">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Name</label>
                                  <input name="name" placeholder="..." type="text" class="form-control">
                                  <input name="slug" placeholder="..." type="text" class="form-control slug">
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-group">
                                  <label>Address</label>
                                  <input name="address" placeholder="..." type="text" class="form-control">
                              </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label class="">Tỉnh Thành</label>
                              <select name="province_id" class="form-control select2" id="province">
                                <option value="">...</option>
                                
                              </select>
                              <div id="list_province"></div>
                            </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-group">
                                <label class="">Quận/Huyện</label>
                                <select name="" class="form-control select2" id="">
                                  <option value="">...</option>
                                  
                                </select>
                                <div id="list_province"></div>
                            </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-group">
                                <label class="">Phường/Xã</label>
                                <select name="" class="form-control select2" id="">
                                  <option value="">...</option>
                                  
                                </select>
                                <div id="list_province"></div>
                            </div>
                          </div>

                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Tổng quan</label>
                                  <textarea name="content" class="form-control" id="ckeditor"></textarea>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">SEO</h6>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="vi">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Title</label>
                                  <input name="title" placeholder="..." type="text" class="form-control">
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Description</label>
                                  <input name="description" placeholder="..." type="text" class="form-control">
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="">Danh mục</label>
                    <select required="" name='parent' class="form-control select2" id="parent">
                      <option value="">--Chọn danh mục--</option>
                      <?php addeditcat ($category,0,$str='',old('parent')); ?>
                    </select>
                    <div id="list_parent"></div>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <div style="display: flex;">
                      <input name="price" placeholder="..." type="text" class="form-control">
                      <select class="form-control" name="unit">
                        <option value="Tỷ">Tỷ</option>
                        <option value="Triệu">Triệu</option>
                      </select>
                    </div>
                </div>
            </div>
          </div>


          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Images</h6>
            </div>
            <div class="card-body">
                <div class="file-upload">
                    <div class="file-upload-content" onclick="$('.file-upload-input').trigger( 'click' )">
                        <img class="file-upload-image" src="{{ isset($data) ? 'data/news/'.$data->img : 'data/no_image.jpg' }}" />
                    </div>
                    <div class="image-upload-wrap">
                        <input name="img" class="file-upload-input" type='file' onchange="readURL(this);" accept="image/*" />
                    </div>
                </div>
            </div>

        </div>
        <div class="card shadow mb-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Chọn nhiều ảnh</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <input type="file" name="imgdetail[]" multiple class="form-control">
                    <p>Nhấn giữ <i style="color: red">Ctrl</i> để chọn nhiều ảnh !</p>
                </div>
                
            </div>
        </div>
      </div>
</div>

<div>
  <input value="Tổng quan" type="hidden" name="name_section[]" >
  <input value="Overview" type="hidden" name="name_section:en[]">
  <input value="概述" type="hidden" name="name_section:cn[]">
</div>

<div>
  <input value="Vị trí" type="hidden" name="name_section[]" >
  <input value="Location" type="hidden" name="name_section:en[]">
  <input value="地點" type="hidden" name="name_section:cn[]">
</div>

<div>
  <input value="Liên kết vùng" type="hidden" name="name_section[]" >
  <input value="Regional link" type="hidden" name="name_section:en[]">
  <input value="區域鏈接" type="hidden" name="name_section:cn[]">
</div>

<div>
  <input value="Tiện ích" type="hidden" name="name_section[]" >
  <input value="Utilities" type="hidden" name="name_section:en[]">
  <input value="公用事業" type="hidden" name="name_section:cn[]">
</div>

<div>
  <input value="Mặt bằng" type="hidden" name="name_section[]" >
  <input value="Ground" type="hidden" name="name_section:en[]">
  <input value="地面" type="hidden" name="name_section:cn[]">
</div>


</form>

<style type="text/css">
  .slug{ border: none; border-radius: 0; padding: 0px; padding-left: 15px; height: 20px }
  .slug:focus{ box-shadow: none; }
</style>

<?php 
    function addeditcat ($data, $parent=0, $str='',$select=0)
    {
        foreach ($data as $value) {
            if ($value['parent'] == $parent) {
                if($select != 0 && $value['id'] == $select )
                { ?>
                    <option value="<?php echo $value['id']; ?>" selected> <?php echo $str.$value['name']; ?> </option>
                <?php } else { ?>
                    <option value="<?php echo $value['id']; ?>" > <?php echo $str.$value['name']; ?> </option>
                <?php }
                
                addeditcat ($data, $value['id'], $str.'___',$select);
            }
        }
    }
?>

@endsection