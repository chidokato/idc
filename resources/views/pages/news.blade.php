@extends('layout.index')

@section('title') {{$data->title ? $data->title : $data->name}} @endsection
@section('description') {{$data->description}} @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection
@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/news.css" rel="stylesheet">
@endsection
@section('content')

<section class="floating-label sec-fiter-search">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <!------------------- BREADCRUMB ------------------->
                <section class="sec-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{asset('')}}">Indochine</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tin tức</li>
                        </ol>
                    </nav>
                </section>
                <!------------------- END: BREADCRUMB ------------------->
            </div>
            <div class="col-md-6">
                <form>
                    <div class="row g-3 justify-content-lg-end">
                        <div class="col-lg-6">
                            <div class="input-group search-input">
                                <span class="input-group-text border100"><i class="icon-search"></i></span>
                                <input type="text" class="form-control" placeholder="Nhập địa chỉ, dự án">
                            </div>
                            <button type="submit" class="btn btn-circle">Tìm kiếm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</section>


<section class="card-grid news-sec">
    <div class="container">
        <h3 class="text-uppercase title-subpage">{{ $data->name }}</h3>
        <div class="row">
            <div class="col-lg-9">
                <div class="news-hightlight">
                    <div class="row">
                        <div class="col-md-12">
                            @foreach($posts as $key => $val)
                            @if($key < 3)
                            <div class="iteam">
                                <a class="card-overlay outline-effect {{ $key>0 ? 'card-overlay-sm':'' }} " href="{{$val->category->slug}}/{{$val->slug}}">
                                    <span class="card-overlay-img"><img src="assets/images/space-4.gif" alt="" class="w-100" style="background-image: url('data/images/{{$val->img}}');"></span>
                                    <div class="card-overlay-body">
                                        <div class="card-overlay-body-wrap">
                                            <div class="time-box">
                                                <span>{{date_format($val->updated_at,"d/m")}}</span>
                                                <span><i class="icon-time me-1"></i>{{date_format($val->updated_at,"Y")}}</span>
                                            </div>
                                            <p class="text-truncate-set text-truncate-set-2">{{$val->name}}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="row row-cols-1 g-4 horizontal-view" id="show-setting">
                    @foreach($posts as $key => $val)
                    @if($key > 2)
                    <div class="col">
                        <div class="card card-s card-s4">
                            <a href="{{$val->category->slug}}/{{$val->slug}}">
                                <span><img src="assets/images/space-3.gif" class="card-img-top" style="background-image: url('data/images/{{$val->img}}');" alt="..."></span>
                            </a>
                            <div class="card-body">
                                <div class="card-body-wrap">
                                    <h5 class="card-title"><a href="{{$val->category->slug}}/{{$val->slug}}">{{$val->name}}</a></h5>
                                    <div class="card-info">
                                        <span><i class="icon-time me-2"></i>{{date_format($val->updated_at,"d/m/Y")}}</span>
                                        <span><i class="icon-user me-2"></i>{{$val->User?->yourname}}</span>
                                    </div>
                                    <p class="mb-0 text-truncate-set text-truncate-set-2">Chính chủ cần chuyển nhượng gấp căn 2 ngủ diện tích thông thủy 78m2 full đồ, khách mua chỉ cần dọn quần áo đến có thể ở ngay</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
            <div class="col-lg-3 d-none d-lg-block">

                <div class="widget widget-list mb-3">
                    <!-- <h4><span>Tin tức</span></h4> -->
                    <ul>
                        <li><a href="tin-thi-truong"><i class="icon-next me-2"></i>Tin thị trường</a></li>
                        <li><a href="tin-noi-bo"><i class="icon-next me-2"></i>Tin nội bộ</a></li>
                    </ul>
                </div>

                
                <div class="widget widget-list widget-news mb-3">
                    <h4><span>Tin mới nhất</span></h4>
                    @foreach($posts as $key => $val)
                    @if($key == 0)
                    <a href="{{$val->category->slug}}/{{$val->slug}}" class="news-item-captain">
                        <div class="news-item-captain-img">
                            <div class="news-item-captain-img-wrap">
                                <img src="assets/images/space-3.gif" style="background-image: url('data/images/{{$val->img}}');" alt="" class="w-100">
                                <span class="date"><i class="icon-time me-1"></i> <span>{{date_format($val->updated_at,"d/m/Y")}}</span> </span>
                            </div>
                        </div>
                        <div class="news-item-captain-body">
                            <h5>{{$val->name}}</h5>
                            <p class="mb-0  text-truncate-set text-truncate-set-2">Chính chủ cần chuyển nhượng gấp căn 2 ngủ diện tích thông thủy 78m2 full đồ, khách mua chỉ cần dọn quần áo đến có thể ở ngay</p>
                        </div>
                    </a>
                    @else
                    <a href="{{$val->category->slug}}/{{$val->slug}}" class="news-item">
                        <span><img src="assets/images/space-3.gif" style="background-image: url('data/images/{{$val->img}}');" alt="" class="w-100"></span>
                        <div class="news-item-body">
                            <span class="date"><i class="icon-time me-1"></i> <span>{{date_format($val->updated_at,"d/m/Y")}}</span> </span>
                            <p class="mb-0 text-truncate-set text-truncate-set-2">{{$val->name}}</p>
                        </div>
                    </a>
                    @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->


@endsection

@section('script')

@endsection