@extends('layout.index')

@section('title') {{$data->title ? $data->title : $data->name}} @endsection
@section('description') {{$data->description}} @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
@endsection

@section('content')
<form action="{{ url()->current() }}" method="GET">
<section class="sec-fiter-search floating-label">
    <div class="container">
        <div data-bs-toggle="button" class="d-md-none"><button type="button" class="btn btn-circle btn-toggle"><span class="icon-search"></span></button></div>
        
            <div class="row g-3 justify-content-lg-end">
                <div class="col-lg-4">
                    <div class="input-group search-input">
                        <span class="input-group-text border100"><i class="icon-search"></i></span>
                        <input value="{{ request()->key ?? '' }}" name="key" type="text" class="form-control" placeholder="Nhập địa chỉ, dự án">
                    </div>
                    <button type="submit" class="btn btn-circle">Tìm kiếm</button>
                </div>
            </div>
        
    </div>
</section>

<!------------------- CARD ------------------->
<section class="card-grid sales-sec list-tindang">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 d-none d-lg-block">
                <div class="widget widget-list widget-hightlight mb-3">
                    <h4><span>Loại hình</span></h4>
                    @foreach($cats as $val)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categories[]" value="{{$val->id}}" id="flexCheck{{$val->id}}" {{ in_array($val->id, request()->input('categories', [])) ? 'checked' : '' }}>
                            <label class="form-check-label aa22" for="flexCheck{{$val->id}}">
                                <span>{{$val->name}}</span> <span>({{ count($val->Post) }})</span>
                            </label>
                        </div>
                    @endforeach
                    <hr>
                    <h4><span>Tình thành</span></h4>
                    @foreach($provinces as $val)
                        @if(count($val->Post) > 0)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="provinces[]" value="{{$val->id}}" id="flexCheck{{$val->id}}">
                            <label class="form-check-label aa22" for="flexCheck{{$val->id}}">
                                <span>{{$val->name}}</span> <span>({{ count($val->Post) }})</span>
                            </label>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-lg-9">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{asset('')}}">Indochine</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$data->name}}</li>
                    </ol>
                </nav>
                <h1 class="text-uppercase title-cat">Các dự án bất động sản trên toàn quốc</h1>
                <div class="option-cat">
                    <div class="iteam"><a href="">Đang mở bán</a></div>
                    <div class="iteam"><a href="">Indochine phân phối độc quyền</a></div>
                </div>
                <div class="sort-box">
                    <span>có <span class="text-main font-weight-semibold">{{ $posts->total() }}</span> sản phẩm</span>
                    <div class="sort-ct">
                        <div class="dropdown">
                            <a class="btn ripple-effect dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <span>Sắp xếp theo: giá tăng dần <i class="icon-down ms-2"></i></span>
                            </a>
                            <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item checked" href="#">Giá tăng dần</a></li>
                            <li><a class="dropdown-item" href="#">Giá giảm dần</a></li>
                            <li><a class="dropdown-item" href="#">Mới nhất</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row" id="load-product">
                    @foreach($posts as $key => $val)
                        <div class="col-md-4 mb-3">
                            @include('pages.iteam.product')
                        </div>
                    @endforeach
                </div>

                <div class="paginate-search">
                    <div>Hiển thị: </div>
                    <select class="paginate" name="per_page" onchange="this.form.submit()">
                        <option value="12" {{ request()->per_page == 12 ? 'selected' : '' }}>12</option>
                        <option value="24" {{ request()->per_page == 24 ? 'selected' : '' }}>24</option>
                        <option value="48" {{ request()->per_page == 48 ? 'selected' : '' }}>48</option>
                        <option value="96" {{ request()->per_page == 96 ? 'selected' : '' }}>96</option>
                    </select>
                    <div> Từ {{ $posts->firstItem() }} đến {{ $posts->lastItem() }} trên tổng {{ $posts->total() }} </div>
                    {{ $posts->appends(request()->all())->links() }}
                </div>
                
            </div>
            
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->
</form>
@endsection


@section('js')

@endsection