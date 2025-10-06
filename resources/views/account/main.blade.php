@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('content')

<section class="card-grid sales-sec list-tindang">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="widget widget-list widget-hightlight mb-3">
                    <h4><span>Dạng mục</span></h4>
                	<div class="form-check">
                        <input class="form-check-input" type="checkbox" name="categories[]">
                        <label class="form-check-label aa22" for="flexCheck1">
                            <span>Facebook</span> <span>(5)</span>
                        </label>
                    </div>
                    
                </div>
            </div>
            <div class="col-lg-9">
                
                
            </div>
            
        </div>
    </div>
</section>

@endsection


@section('script')

@endsection