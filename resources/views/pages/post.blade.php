@extends('layout.index')

@section('title'){{$post->title ? $post->title : $post->name}}@endsection
@section('description'){{$post->description ? $post->description : $post->name.$post->name}}@endsection
@section('robots'){{'index, follow'}}@endsection
@section('url'){{asset('')}}{{$post->category->slug}}/{{$post->slug}}@endsection
@section('img'){{asset('')}}data/images/{{$post->img}}@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/news.css" rel="stylesheet">
@endsection

@section('content')
<!------------------- BREADCRUMB ------------------->
<section class="sec-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{asset('')}}">Indochine</a></li>
            <li class="breadcrumb-item"><a href="{{$post->category->slug}}">{{$post->category->name}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$post->name}}</li>
            </ol>
        </nav>
    </div>
</section>
<!------------------- END: BREADCRUMB ------------------->
<section class="card-grid news-sec">
    <div class="container">
        
        <div class="row">
            <div class="col-lg-9">
                <h1 class="title-subpage">{{ $post->name }}</h1>
                <div class="news-detail">
                    <div class="description">
                        {!! $post->detail !!}
                    </div>
                    {!! $post->content !!}
                    <div class="row all-images">
                        @foreach($post->Images as $val)
                        <div class="col-lg-6 img"><img src="data/images/{{$val->img}}"></div>
                        @endforeach
                    </div>
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
                    @foreach($related_post as $key => $val)
                    @if($key == 0)
                    <a href="{{$val->category->slug}}/{{$val->slug}}" class="news-item-captain">
                        <div class="news-item-captain-img">
                            <div class="news-item-captain-img-wrap">
                                <img src="assets/images/space-3.gif" style="background-image: url('data/images/{{$val->img}}');" alt="" class="w-100">
                                <span class="date"><i class="icon-time me-1"></i>2 ngày trước</span>
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
                            <span class="date"><i class="icon-time me-1"></i>2 ngày trước</span>
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