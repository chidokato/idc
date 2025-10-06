@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<?php use App\Models\CategoryTranslation; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý đơn hàng</h2>
</div>

<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#tab1">{{__('lang.all')}}</a></li>
                    <!-- <li><a data-toggle="tab" class="nav-link " href="#tab2">Hiển thị</a></li> -->
                    <!-- <li><a data-toggle="tab" class="nav-link" href="#tab3">Ẩn</a></li> -->
                </ul>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="tab2">
                    @if(count($cart) > 0)
                    <table class="table">
                            <thead>
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày đặt hàng</th>
                                    <th>Tên khách hàng</th>
                                    <th>Rank</th>
                                    <th>Số điện thoại</th>
                                    <th>Trạng thái</th>
                                    <!-- <th></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $val)
                                <tr>
                                    <td><a href="{{route('cart.edit',[$val->id])}}">ĐH00{{$val->id}}</a></td>
                                    <td>{{number_format($val->all_price)}}đ</td>
                                    <td>{{$val->created_at}}</td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->User->rank}}</td>
                                    <td>{{$val->phone}}</td>
                                    <td>{{$val->status}}</td>
                                    <!-- <td style="display: flex;">
                                        <form action="{{route('cart.destroy', [$val->id])}}" method="POST">
                                          @method('DELETE')
                                          @csrf
                                          <button class="button_none" onclick="return confirm('Bạn muốn xóa bản ghi ?')"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td> -->
                                </tr>
                                @endforeach
                            </tbody>
                    </table>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</div>

@endsection