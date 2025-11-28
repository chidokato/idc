@extends('admin.layout.main')
@section('content')
@include('admin.alert')
<?php use App\Models\Images; use App\Models\Option; ?>
<form id="validateForm" method="POST" action="{{route('post.update', [$data->id])}}" enctype="multipart/form-data">
@csrf
@method('PUT')
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
        <li class="nav-item mobile-hide mr-2">
            <button onclick="window.open('{{ url($data->category?->slug . '/' . $data->slug) }}', '_blank');" type="reset" class="btn-info btn"><i class="fas fa-eye"></i> View </button>
        </li>
        <li class="nav-item mobile-hide">
            <button type="reset" class="btn-danger btn"><i class="fas fa-sync"></i> Làm mới</button>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item">
            <button type="submit" class="btn-success btn"><i class="far fa-save"></i> Lưu lại</button>
        </li>
    </ul>
</nav>

<div class="d-sm-flex align-items-center justify-content-between mb-3 flex" style="height: 38px;">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Chỉnh sửa</h2>
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
                        <div class="col-sm-10 input-group">
                            <input value="{{$data->name}}" name="name" placeholder="Tên dự án" type="text" class="form-control">
                            <input value="{{$data->slug}}" name="slug" placeholder="" type="text" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Danh mục</label>
                        <div class="col-sm-10">
                            <select name='category_id' class="form-control select2" id="category">
                                <?php addeditcat ($category,0,$str='',$data['category_id']); ?>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <label class="col-sm-2 col-form-label">Giá bán</label>
                        <div class="col-sm-5">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input value="{{$data->price}}" class="form-control" name="price" type="text" placeholder="Từ (mặc định)">
                                        <select class="form-control" name="unit">
                                            <option {{$data->unit=='Tỷ' ? 'selected':''}} value="Tỷ">Tỷ</option>
                                            <option {{$data->unit=='Triệu' ? 'selected':''}} value="Triệu">Triệu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input value="{{$data->price_max}}" class="form-control" name="price_max" type="text" placeholder="Đến">
                                        <select class="form-control" name="unit_max">
                                            <option {{$data->unit_max=='Tỷ' ? 'selected':''}} value="Tỷ">Tỷ</option>
                                            <option {{$data->unit_max=='Triệu' ? 'selected':''}} value="Triệu">Triệu</option>
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

                                <input value="{{ $data->price?number_format($data->price, 0, ',', '.'):'' }}" type="text" name="price" class="form-control price-input" placeholder="Từ (mặc định)">
                                <!-- <div class="viewprice"></div> -->

                                <input value="{{ $data->price_max?number_format($data->price_max, 0, ',', '.'):'' }}" type="text" name="price_max" class="form-control price-input" placeholder="Đến">
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
                                <input value="{{$data->acreage}}" type="text" name="acreage" class="form-control tab" placeholder="Từ (mặc định)">
                                <input value="{{$data->acreage_max}}" type="text" name="acreage_max" class="form-control tab" placeholder="Đến">
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
                                <input value="{{$data->bedroom}}" type="text" name="bedroom" class="form-control" placeholder="Từ (mặc định)">
                                <input value="{{$data->bedroom_max}}" type="text" name="bedroom_max" class="form-control" placeholder="Đến">
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
                                <input value="{{$data->wc}}" type="text" name="wc" class="form-control" placeholder="Từ (mặc định)">
                                <input value="{{$data->wc_max}}" type="text" name="wc_max" class="form-control" placeholder="Đến">
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
                                <input value="{{$data->total_product}}" type="text" name="total_product" class="form-control">
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
                                    <option {{ $val->id == $data->province_id ? 'selected':'' }} value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>
                                <select class="form-control select2" name="district" id="district">
                                    <option value="">- Quận/huyện -</option>
                                    @foreach($district as $val)
                                    <option {{ $val->id == $data->district_id ? 'selected':'' }} value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>
                                <select class="form-control select2" name="ward" id="ward">
                                    <option value="">- Phường/xã -</option>
                                    @foreach($ward as $val)
                                    <option {{ $val->id == $data->ward_id ? 'selected':'' }} value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>
                                <select class="form-control select2" name="street" id="street">
                                    <option value="">- Đường -</option>
                                    @foreach($street as $val)
                                    <option {{ $val->id == $data->street_id ? 'selected':'' }} value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input value="{{$data->address}}" name="address" type="text" class="form-control" placeholder="Địa chỉ chi tiết ...">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2">Tùy chọn khác</label>
                        <div class="col-sm-10">
                            <div class="form-group">
                                <div class="col-auto my-1">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input {{ $data->monopoly == '1' ? "checked":"" }} name="monopoly" type="checkbox" class="custom-control-input" id="customControlAutosizing1">
                                        <label class="custom-control-label" for="customControlAutosizing1">Độc quyền</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-auto my-1">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input {{ $data->for_sale == '1' ? "checked":"" }} name="for_sale" type="checkbox" class="custom-control-input" id="customControlAutosizing2">
                                        <label class="custom-control-label" for="customControlAutosizing2">Đang mở bán</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-auto my-1">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input {{ $data->new_product == '1' ? "checked":"" }} name="new_product" type="checkbox" class="custom-control-input" id="customControlAutosizing3">
                                        <label class="custom-control-label" for="customControlAutosizing3">Mới ra mắt</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="linkneo" id="section2">
            <div class="card shadow mb-4" >
                <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin dự án</h6>
                </span>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea class="form-control editor" name="content0"> {!! $data->content !!} </textarea>
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
                                <div class="file-upload">
                                    <div class="file-upload-content" onclick="$('.file-upload-input').trigger( 'click' )">
                                        <img class="file-upload-image" src="{{ isset($data) ? 'data/images/'.$data->img : 'data/no_image.jpg' }}" />
                                    </div>
                                    <div class="image-upload-wrap">
                                        <input name="img" class="file-upload-input" type='file' onchange="readURL(this);" accept="image/*" />
                                    </div>
                                </div>
                                
                                <span>
                                    @foreach($images as $val)
                                    <span class="view-img-ditail" id="detail_img">
                                        <img src="data/images/{{$val->img}}">
                                        <button onClick="delete_row(this)" type="button" id="del_img_detail"> <i class="fa fa-times" aria-hidden="true"></i> </button>
                                        <input type="hidden"  name="id_img_detail" id="id_img_detail" value="{{$val->id}}" />
                                    </span>
                                    @endforeach
                                    <span class="image-preview" id="imagePreview"></span>
                                    <span class="file-input-wrapper">
                                        <input type="file" name="imgdetail[]" multiple class="file-input" id="imgInput">
                                        <img src="admin_asset/img/add-img.png" alt="Upload Image" class="custom-file-input1" id="customFileInput">
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="row detail-img">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @foreach($section as $key => $val)
        <input type="hidden" value="{{$val->id}}" name="id-edit[]">
        <div class="linkneo section" id="section-{{$val->id}}">
            <!-- <button class="btn btn-danger remove-section" type="button">Xóa Section</button> -->
            <div class="card shadow mb-4" >
                <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{$val->tab}}</h6>
                </span>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="">STT</label>
                                <input name="stt-edit[]" value="{{$val->stt}}" placeholder="..." type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="">Tab</label>
                                <input required name="tab-edit[]" value="{{$val->tab}}" placeholder="..." type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="">Heading</label>
                                <input name="heading-edit[]" value="{{$val->heading}}" placeholder="..." type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="">Hình ảnh</label>
                                <input multiple name="img_ss-edit{{$key}}[]" placeholder="..." type="file" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="">Tùy chọn</label>
                                <select class="form-control" name="status[]">
                                    <!-- <option {{ $val->status==0? 'selected':'' }} value="0">Mặc định</option> -->
                                    <option {{ $val->status==1? 'selected':'' }} value="1">Kiểu 1</option>
                                    <!-- <option {{ $val->status==2? 'selected':'' }} value="2">Kiểu 2</option> -->
                                    <option {{ $val->status==3? 'selected':'' }} value="3">Mặt bằng</option>
                                    <!-- <option {{ $val->status==4? 'selected':'' }} value="4">Căn hộ</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea rows="" name="content-edit[]" class="form-control editor"> {!! $val->content !!} </textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                @foreach($val->Images as $img)
                                <span class="view-img-ditail" id="detail_img">
                                    <img src="data/images/{{$img->img}}">
                                    <input value="{{$img->name}}" type="" name="" id="name_img_detail" name="form-control">
                                    <button onClick="delete_row(this)" type="button" id="del_img_detail"> <i class="fa fa-times" aria-hidden="true"></i> </button>
                                    <input type="hidden"  name="id_img_detail" id="id_img_detail" value="{{$img->id}}" />
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <div id="sectionContainer"></div>

        <button id="addSectionButton" class="btn-success btn" type="button">Thêm Section</button>

        <br>
        <br>

        <div class="linkneo" id="maps">
            <div class="card shadow mb-4" >
                <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Bản đồ định vị</h6>
                </span>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <textarea name="maps" id="maps" rows="5" class="form-control" oninput="loadMap()">{{$data->maps}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="load_maps" style="margin-top: 20px;"></div>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="">Title</label>
                                <input value="{{$data->title}}" name="title" placeholder="..." type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="">description</label>
                                <input value="{{$data->description}}" name="description" placeholder="..." type="text" class="form-control">
                            </div>
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
                                <li><a class="scroll-link" href="#section2">Thông tin dự án</a></li>
                                <li><a class="scroll-link" href="#section3">Hình ảnh</a></li>
                                @foreach($section as $val)
                                <li><a class="scroll-link" href="#section-{{$val->id}}">{{$val->tab}}</a></li>
                                @endforeach
                                <li><a class="scroll-link" href="#maps">Bản đồ định vị</a></li>
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

<style>
    #load_maps iframe {
        width: 100%;
        height: 450px;
        border: 0;
    }
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
<script>
    function delete_row(e) {
        e.parentElement.remove();
    }
</script>

<script type="text/javascript">
document.querySelectorAll('.price-input').forEach(function (input) {
    input.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // Loại bỏ ký tự không phải số
        value = new Intl.NumberFormat('vi-VN').format(value); // Định dạng số theo kiểu Việt Nam
        e.target.value = value;
    });
});
</script>


<script>
    document.querySelector('.price-input').addEventListener('input', function() {
        let price = this.value.replace(/\./g, ''); // Bỏ dấu chấm để lấy giá trị số
        if (price) {
            let priceInWords = numberToWords(Number(price));
            document.querySelector('.viewprice').textContent = priceInWords;
        } else {
            document.querySelector('.viewprice').textContent = '';
        }
    });
    function numberToWords(number) {
        const units = [
            { value: 1E9, suffix: " tỷ" },
            { value: 1E6, suffix: " triệu" },
            { value: 1E3, suffix: " nghìn" },
            { value: 1, suffix: " đồng" }
        ];
        for (let i = 0; i < units.length; i++) {
            if (number >= units[i].value) {
                let value = (number / units[i].value).toFixed(1).replace('.0', '');
                return value + units[i].suffix;
            }
        }
    }
</script>

@endsection