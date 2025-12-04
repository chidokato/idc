@extends('layout.index')

@section('title'){{$setting->title ? $setting->title : $setting->name}}@endsection
@section('description') {{$setting->description}} @endsection
@section('robots'){{'index, follow'}}@endsection
@section('url'){{asset('')}}@endsection
@section('img'){{asset('')}}{{'data/images/logo_-01.png'}}@endsection

@section('css')
<link href="assets/css/home.css" rel="stylesheet">
@endsection

@section('content')
@include('layout.popup')
<!------------------- HERO ------------------->
<section class="sec-hero">
    <div class="hero-slider">
        <div class="swiper">
            <div class="swiper-wrapper">
                @foreach($slider as $val)
                <div class="swiper-slide"><span style='background-image: url("data/images/{{$val->img}}")' class="w-100 thumb"></span></div>
                @endforeach
            </div>
            <div class="swiper-navigator">
                <div class="swiper-pagination"></div>
                <div class="swiper-navigator-btn">
                    <div class="swiper-button-prev"><i class="icon-prev-thin"></i></div>
                    <div class="swiper-button-next"><i class="icon-next-thin"></i></div>
                </div>
            </div>
        </div>
    </div>
    
</section>


<!------------------- PRODUCT RENT ------------------->
<section class="sec-product-rent">
    <div class="container">
        <div class="cover-title-home">
            <div class="text-start">
                <div class="cover-title">
                    <h2><span class="cover-title-filled text-main">DỰ ÁN TRỌNG ĐIỂM</span></h2>
                </div>
                <div class="cover-descip">
                    <p>Những dự án mang dấu ấn INDOCHINE, được phát triển kinh doanh và phân phối độc quyền bởi INDOCHINE</p>
                </div>
            </div>
        </div>

        <div class="position-relative">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    @foreach($product as $val)
                    <div class="swiper-slide">
                        <div class="col">
                            @include('pages.iteam.product')
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination "></div>
            </div>
            <div class="swiper-button-next d-none d-lg-flex d-lg-none"></div>
            <div class="swiper-button-prev d-none d-lg-flex d-lg-none"></div>
        </div>

        <div class="load-more text-center mt-4 pt-2">
            <div class="cta-btn ir">
                <a class="" target="_blank" href="du-an"><span class="cta-text font-weight-semibold">Xem tất cả</span><span class="cta-ico"><i class="icon-next"></i></span></a>
            </div>
        </div>
    </div>
</section>

<!------------------- SERVICE ------------------->
<section class="sec-productcity-hp bg1">
    <div class="container position-relative">
        <div class="cover-title-home">
            <div class="text-start">
                <div class="cover-title">
                    <h2><span class="cover-title-filled text-main">DỰ ÁN TOÀN QUỐC</span></h2>
                </div>
            </div>
        </div>
        <div class="news-hightlight">
            <div class="row g-3">
                @foreach($provinces as $key => $val)
                @if($key % 2 == 0)
                <div class="col-lg-3">
                    <div class="card-overlay-flex">
                        <a class="card-overlay card-overlay-sm outline-effect" href="location/{{$provinces[$key]->slug}}">
                            <span class="card-overlay-img"><img src="assets/images/space-2.gif" alt="" class="w-100 thumb" style="background-image: url('data/images/{{$provinces[$key]->img}}');"></span>
                            <div class="card-overlay-body">
                                <h2><span>{{$provinces[$key]->name}}</span></h2>
                                <div>{{ count($provinces[$key]->Post) }} sản phẩm</div>
                            </div>
                        </a>
                    </div>
                    <div class="card-overlay-flex">
                        <a class="card-overlay card-overlay-sm outline-effect" href="location/{{$provinces[$key+1]->slug}}">
                            <span class="card-overlay-img"><img src="assets/images/space-2.gif" alt="" class="w-100 thumb" style="background-image: url('data/images/{{$provinces[$key+1]->img}}');"></span>
                            <div class="card-overlay-body">
                                <h2><span>{{$provinces[$key+1]->name}}</span></h2>
                                <div>{{ count($provinces[$key+1]->Post) }} sản phẩm</div>
                            </div>
                        </a>
                    </div>
                </div>
                @endif
                @endforeach               
            </div>
        </div>
    </div>
</section>
<!------------------- END SERVICE ------------------->

<section class="sec-productcity-hp tintuc">
    <div class="container position-relative">
        <div class="cover-title-home">
            <div class="text-start">
                <div class="cover-title">
                    <h2><span class="cover-title-filled text-main">TIN TỨC</span></h2>
                </div>
                <div class="cover-descip">
                    <p>Cập nhật những thông tin mới nhất từ INDOCHINE và thị trường bất động sản</p>
                </div>
            </div>
        </div>
        <div class="news-hightlight">
            <div class="row">
                @foreach($news as $key => $val)
                @if($key==0)
                <div class="col-md-6">
                    <div class="card-overlay-flex">
                        <a class="card-overlay card-overlay-sm outline-effect" href="{{$val->category->slug}}/{{$val->slug}}">
                            <span class="card-overlay-img"><img src="assets/images/space-3.gif" alt="" class="w-100 thumb" style="background-image: url('data/images/{{$val->img}}');"></span>
                            <div class="card-overlay-body">
                                <h3>{{$val->name}}</h3>
                                <div class="description text-truncate-set text-truncate-set-2">{{$val->detail}}</div>
                            </div>
                        </a>
                    </div>
                </div>
                @endif
                @endforeach
                <div class="col-md-6">
                    @foreach($news as $key => $val)
                    @if($key>0)
                    <a href="{{$val->category->slug}}/{{$val->slug}}" class="news-item">
                        <span><img src="assets/images/space-1.gif" style="background-image: url('data/images/{{$val->img}}')" alt="" class="w-100 thumb"></span>
                        <div class="news-item-body">
                            <p class="mb-0 text-truncate-set text-truncate-set-3">{{$val->name}} </p>
                        </div>
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="load-more text-center mt-4 pt-2">
            <div class="cta-btn ir">
                <a class="" href="tin-tuc"><span class="cta-text font-weight-semibold">Xem tất cả</span><span class="cta-ico"><i class="icon-next"></i></span></a>
            </div>
        </div>
    </div>
</section>

@endsection

@section('js')
<script>
    var swiper8 = new Swiper(".hero-slider .swiper", {
        spaceBetween: 0,
        effect: "fade",
        lazy: true,
        loop: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".hero-slider .swiper-pagination",
            type: "fraction",
        },
        navigation: {
            nextEl: ".hero-slider .swiper-button-next",
            prevEl: ".hero-slider .swiper-button-prev",
        },
    });


    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 4,
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            // khi màn hình có chiều rộng từ 0px trở lên
            0: {
                slidesPerView: 1,
                spaceBetween: 10,
            },
            // khi màn hình có chiều rộng từ 640px trở lên
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            // khi màn hình có chiều rộng từ 768px trở lên
            768: {
                slidesPerView: 3,
                spaceBetween: 25,
            },
            // khi màn hình có chiều rộng từ 1024px trở lên
            1024: {
                slidesPerView: 4,
                spaceBetween: 30,
            },
        }
    });

</script>

@endsection
