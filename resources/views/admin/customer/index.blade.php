@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<?php use App\Models\CategoryTranslation; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý khách hàng</h2>
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
                    @if(count($customer) > 0)
                    <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Link</th>
                                    <th>date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer as $val)
                                <tr>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->phone}}</td>
                                    <td>{{$val->email}}</td>
                                    <td>{{$val->title}}</td>
                                    <td>{{$val->created_at}}</td>
                                    <td style="display: flex;">
                                        <form action="{{route('customer.destroy', [$val->id])}}" method="POST">
                                          @method('DELETE')
                                          @csrf
                                          <button class="button_none" onclick="return confirm('Bạn muốn xóa bản ghi ?')"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
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