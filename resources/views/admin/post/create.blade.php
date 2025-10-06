@extends('admin.layout.main')

@section('content')

@include('admin.alert')
<form id="validateForm" method="post" action="{{route('post.store')}}" enctype="multipart/form-data">
@csrf
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed">
    <button type="button" id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
    <ul class="navbar-nav ">
        <li class="nav-item"> <a class="nav-link line-1" href="{{route('post.index')}}" ><i class="fa fa-chevron-left" aria-hidden="true"></i> <span class="mobile-hide">Quay lại</span> </a> </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link" target="_blank" href="{{asset('')}}" >
                <i class="fas fa-external-link-alt mr-2"></i> {{__('lang.home')}}
            </a>
        </li>
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
        <div class="linkneo" id="section1">
            <div class="card shadow mb-4" >
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chung</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Tên dự án</label>
                        <div class="col-sm-10">
                            <input name="name" placeholder="Tên dự án" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Danh mục</label>
                        <div class="col-sm-10">
                            <select name='category_id' class="form-control select2" id="category">
                                <?php addeditcat ($category,0,$str='',old('parent')); ?>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <label class="col-sm-2 col-form-label">Giá bán</label>
                        <div class="col-sm-5">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input class="form-control" name="price" type="text" placeholder="Từ (mặc định)">
                                        <select class="form-control" name="unit">
                                            <option value="Tỷ">Tỷ</option>
                                            <option value="Triệu">Triệu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input class="form-control" name="price_max" type="text" placeholder="Đến">
                                        <select class="form-control" name="unit_max">
                                            <option value="Tỷ">Tỷ</option>
                                            <option value="Triệu">Triệu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Giá bán</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">đ</span>
                                </div>
                                <input type="text" name="price" class="form-control price-input" placeholder="Từ (mặc định)">
                                <input type="text" name="price_max" class="form-control price-input" placeholder="Đến">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Diện tích</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">m2</span>
                                </div>
                                <input type="text" name="acreage" class="form-control" placeholder="Từ (mặc định)">
                                <input type="text" name="acreage_max" class="form-control" placeholder="Đến">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Phòng ngủ</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">PN</span>
                                </div>
                                <input type="text" name="bedroom" class="form-control" placeholder="Từ (mặc định)">
                                <input type="text" name="bedroom_max" class="form-control" placeholder="Đến">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Phòng vệ sinh</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">WC</span>
                                </div>
                                <input type="text" name="wc" class="form-control" placeholder="Từ (mặc định)">
                                <input type="text" name="wc_max" class="form-control" placeholder="Đến">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Tổng số căn</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">Căn</span>
                                </div> 
                                <input type="text" name="total_product" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Vị trí</label>
                        <div class="col-sm-10">
                            <div class="input-group form-group">
                                <select class="form-control select2" name="province" id="province">
                                    <option value="">- Tỉnh/thành -</option>
                                    @foreach($province as $val)
                                    <option value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>
                                <select class="form-control select2" name="district" id="district">
                                    <option value="">- Quận/huyện -</option>
                                </select>
                                <select class="form-control select2" name="ward" id="ward">
                                    <option value="">- Phường/xã -</option>
                                </select>
                                <select class="form-control select2" name="street" id="street">
                                    <option value="">- Đường -</option>
                                </select>
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input name="address" type="text" class="form-control" placeholder="Địa chỉ chi tiết ...">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2">Tùy chọn khác</label>
                        <div class="col-sm-10">
                            <div class="form-group">
                                <div class="col-auto my-1">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input name="monopoly" type="checkbox" class="custom-control-input" id="customControlAutosizing1">
                                        <label class="custom-control-label" for="customControlAutosizing1">Độc quyền</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-auto my-1">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input name="for_sale" type="checkbox" class="custom-control-input" id="customControlAutosizing2">
                                        <label class="custom-control-label" for="customControlAutosizing2">Đang mở bán</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-auto my-1">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input name="new_product" type="checkbox" class="custom-control-input" id="customControlAutosizing3">
                                        <label class="custom-control-label" for="customControlAutosizing3">Mới ra mắt</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div class="linkneo" id="section3">
            <div class="card shadow mb-4" >
                <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh</h6>
                </span>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="display: flex;">
                                <span class="file-upload">
                                    <span class="file-upload-content" onclick="$('.file-upload-input').trigger( 'click' )">
                                        <img class="file-upload-image" src="data/no_image.jpg" />
                                    </span>
                                    <span class="image-upload-wrap">
                                        <input name="img" class="file-upload-input" type='file' onchange="readURL(this);" accept="image/*" />
                                    </span>
                                </span>
                                <span>
                                    <span class="image-preview" id="imagePreview"></span>
                                    <span class="file-input-wrapper">
                                        <input type="file" name="imgdetail[]" multiple class="file-input" id="imgInput">
                                        <img src="admin_asset/img/add-img.png" alt="Upload Image" class="custom-file-input1" id="customFileInput">
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="linkneo section" >
            <button class="btn btn-danger remove-section" type="button">Xóa Section</button>
            <div class="card shadow mb-4" >
                <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Section mới</h6>
                </span>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="">STT</label>
                                <input name="stt[]" placeholder="..." type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="">Tab</label>
                                <input name="tab[]" placeholder="..." type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="">Heading</label>
                                <input name="heading[]" placeholder="..." type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="">Hình ảnh</label>
                                <input multiple name="img_ss[]" placeholder="..." type="file" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <textarea rows="" name="content[]" class="form-control editor"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <div id="sectionContainer"></div>

        <button id="addSectionButton" class="btn-success btn" type="button">Thêm Section</button>

        <br>
        <br>
        <div class="linkneo" id="seo">
            <div class="card shadow mb-4" >
                <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Cấu hình SEO</h6>
                </span>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Title</label>
                        <div class="col-sm-10">
                            <input name="title" placeholder="..." type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <input name="description" placeholder="..." type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Hiển thị</label>
                        <div class="col-sm-10">
                            asdasd ádasd
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
    </div>
    <div class="col-xl-3 col-lg-3" >
        <div class="card shadow mb-4" style="position: sticky; top: 60px;">
            <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Menu</h6>
            </span>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="menu-product">
                            <ul>
                                <li><a class="scroll-link" href="#section1">Thông tin chung</a></li>
                                <li><a class="scroll-link" href="#section3">Hình ảnh</a></li>
                                <li><a class="scroll-link" href="#seo">Cấu hình SEO</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</form>

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

<script type="text/javascript">
document.querySelectorAll('.price-input').forEach(function (input) {
    input.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // Loại bỏ ký tự không phải số
        value = new Intl.NumberFormat('vi-VN').format(value); // Định dạng số theo kiểu Việt Nam
        e.target.value = value;
    });
});


</script>

@endsection