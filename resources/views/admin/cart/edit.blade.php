@extends('admin.layout.main')

@section('content')
@include('admin.alert')
<form method="post" action="{{route('cart.update', [$cart->id])}}" enctype="multipart/form-data">
@csrf
@method('PUT')
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed">
    <button type="button" id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
    <ul class="navbar-nav ">
        <li class="nav-item"> <a class="nav-link line-1" href="{{route('cart.index')}}" ><i class="fa fa-chevron-left" aria-hidden="true"></i> <span class="mobile-hide">Quay lại</span> </a> </li>
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
  <div class="col-xl-7 col-lg-7">
        <div class="card shadow mb-2">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link " href="#en">Nội dung</a></li>
                </ul>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="en">
                    <div class="card-body">
                        <div class="carts">
                            <?php $total = 0; ?>
                            @foreach($order as $id => $val)
                                <table class="cart">
                                    <tr>
                                        <td class="img"><img style="width: 100px" src="data/news/{{$val->Post->img}}"></td>
                                        <td class="info">
                                            <div class="name"><a href="{{$val->Post->Category->slug}}/{{$val->Post->slug}}">{{$val->Post->name}}</a></div>
                                            <div class="price" style="display: flex;">
                                                <div class="red"><span>{{$val['unit']}}</span>{{ number_format($val['price']) }} &nbsp;</div>
                                                @if($val['unit']=='¥')
                                                <div class="vnd">(~ <span>₫</span>{{ number_format($val['price']*$setting->exchange) }})</div>
                                                @endif
                                                <div>&nbsp; x {{ $val['quantity'] }}</div>
                                            </div>

                                        </td>
                                    </tr>
                                </table>
                                <?php
                                    if($val['unit']=='¥'){
                                        $total = $total + ($val['price'] * $val['quantity'] * $setting->exchange);
                                    }else{
                                        $total = $total + ($val['price'] * $val['quantity']);
                                    }
                                ?>
                            @endforeach

                            <hr>
                            <table class="cart_1">
                                <tr>
                                    <td>Khách hàng:</td>
                                    <td>{{$cart->User->yourname}}</td>
                                    <input type="hidden" name="uid" value="{{$cart->User->id}}">
                                </tr>
                                <tr>
                                    <td>Mã đơn hàng:</td>
                                    <td>ĐH00{{$cart->id}}</td>
                                </tr>
                                <tr>
                                    <td>Tổng tiền:</td>
                                    <td class="red">{{number_format($cart->all_price)}}đ</td>
                                    <input type="hidden" name="total" value="{{$cart->all_price}}">
                                </tr>
                                <tr>
                                    <td>Ngày đặt hàng:</td>
                                    <td>{{$cart->created_at}}</td>
                                </tr>
                                <tr>
                                    <td>Tình trạng:</td>
                                    <td>
                                        <select name="status" class="form-control">
                                            <option <?php if($cart->status=='Chờ xác nhận'){echo "selected";} ?> value="Chờ xác nhận">Chờ xác nhận</option>
                                            <option <?php if($cart->status=='Đã xác nhận'){echo "selected";} ?> value="Đã xác nhận">Đã xác nhận</option>
                                            <option <?php if($cart->status=='Chờ thanh toán'){echo "selected";} ?> value="Chờ thanh toán">Chờ thanh toán</option>
                                            <option <?php if($cart->status=='Đã thanh toán'){echo "selected";} ?> value="Đã thanh toán">Đã thanh toán</option>
                                            <option <?php if($cart->status=='Đang vận chuyển'){echo "selected";} ?> value="Đang vận chuyển">Đang vận chuyển</option>
                                            <option <?php if($cart->status=='Thành công'){echo "selected";} ?> value="Thành công">Thành công</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <!-- <div class="col-xl-3 col-lg-3">
      <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Images</h6>
            </div>
            <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                      
                  </div>
              </div>
            </div>
        </div>
      </div> -->
</div>
</form>


<style type="text/css">
.carts .cart_1{ width:100% }
.carts .cart_1 td{ padding:10px; text-align:right;}
.carts .cart_1 td, .carts .cart_1 tr, .carts .cart_1{ border-right:none; border-left: none }
.carts .cart_1 td:first-child{ width:70%; border-right:1px solid #ddd; }
</style>

@endsection