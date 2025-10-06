@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý người dùng</h2>
    <a class="add-iteam" href="{{route('users.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> {{__('lang.add')}}</button></a>
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
                    @if(count($users) > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Quyền</th>
                                <th>date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $val)
                            <tr>
                                <td>{{$val->id}}</td>
                                <td><a href="{{route('users.edit',[$val->id])}}">{{$val->name}}</a></td>
                                <td>{{$val->email}}</td>
                                <td>{{$val->permission}}</td>
                                <td>{{$val->created_at}}</td>
                                <td style="display: flex;">
                                    <a href="{{route('users.edit',[$val->id])}}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    <form action="{{route('users.destroy', [$val->id])}}" method="POST">
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