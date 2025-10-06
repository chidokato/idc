@extends('admin.layout.main')

@section('content')

@include('admin.alert')
<form id="validateForm" method="post" action="{{route('news.store')}}" enctype="multipart/form-data">
@csrf
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed">
    <button type="button" id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
    <ul class="navbar-nav ">
        <li class="nav-item"> <a class="nav-link line-1" href="{{route('news.index')}}" ><i class="fa fa-chevron-left" aria-hidden="true"></i> <span class="mobile-hide">Quay lại</span> </a> </li>
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
                        <label class="col-sm-2 col-form-label">Tên tin tức</label>
                        <div class="col-sm-10">
                            <input name="name" placeholder="Tên tin tức" type="text" class="form-control">
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
                                <!-- <span>
                                    <span class="image-preview" id="imagePreview"></span>
                                    <span class="file-input-wrapper">
                                        <input type="file" name="imgdetail[]" multiple class="file-input" id="imgInput">
                                        <img src="admin_asset/img/add-img.png" alt="Upload Image" class="custom-file-input1" id="customFileInput">
                                    </span>
                                </span> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="linkneo section" >
            <div class="card shadow mb-4" >
                <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Mô tả ngắn</h6>
                </span>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea rows="5" name="detail" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="linkneo section" >
            <div class="card shadow mb-4" >
                <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Nội dung</h6>
                </span>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea rows="" name="content" class="form-control editor"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>



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

@endsection