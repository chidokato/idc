@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/news.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
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
                
            </div>
        </div>
        
    </div>
</section>


<section class="card-grid news-sec">
    <div class="container">
        
        <div class="row">
            <div class="col-lg-3 d-none d-lg-block">
                <div >
                    
                </div>
                <div class="widget widget-list mb-3">
                    <h4><span>DANH MỤC</span></h4>
                    <ul>
                        <li><a href="tin-thi-truong"><i class="icon-next me-2"></i>Facebook</a></li>
                        <li><a href="tin-noi-bo"><i class="icon-next me-2"></i>Tin nội bộ</a></li>
                    </ul>
                </div>

            </div>

            <div class="col-lg-9">
                <h3 class="text-uppercase title-subpage">{{ Auth::user()->name }}</h3>
                <hr>
                <div class="row-btn">
                    <button class="btn btn-dangky">ĐĂNG KÝ MARKETING</button>
                    <button class="btn btn-quanly">QUẢN LÝ CHI PHÍ MARKETING</button>
                </div>
            </div>
            
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('script')

@endsection