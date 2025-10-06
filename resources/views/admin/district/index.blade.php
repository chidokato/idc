@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Danh sách</h2>
    <a class="add-iteam" href="{{route('district.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> add</button></a>
</div>

<style type="text/css">
    .search{ display:flex; margin-bottom:15px }
    .search input, .search select{ width:200px; margin-right:15px }
</style>

<div class="row">
    <form action="" >
    <div class="col-xl-12 col-lg-12 search">
        <input type="name" value="{{request()->key? request()->key:''}}" placeholder="..." class="form-control" name="key">
        <select class="form-control" name="province_id">
            <option value="">...</option>
            @foreach($province as $val)
            <option {{isset(request()->province_id) && request()->province_id== $val->id ? 'selected':''}} value="{{$val->id}}">{{$val->name}}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-success">Tìm kiếm</button>
    </div>
    </form>
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#tab1">Tất cả</a></li>
                    <!-- <li><a data-toggle="tab" class="nav-link " href="#tab2">Hiển thị</a></li> -->
                    <!-- <li><a data-toggle="tab" class="nav-link" href="#tab3">Ẩn</a></li> -->
                </ul>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="tab2">
                    @if(count($district) > 0)
                    <table class="table">
                        <form method="post" action="admin/district/delete_all"><input type="hidden" name="_token" value="{{csrf_token()}}" />
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Prefix</th>
                                    <th>Province</th>
                                    <th>date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($district as $val)
                                <tr>
                                    <td><a href="{{route('district.edit',[$val->id])}}" class="mr-2">{{$val->name}}</a></td>
                                    <td>{{$val->prefix}}</td>
                                    <td>{{$val->Province->name ? $val->Province->name : ''}}</td>
                                    <td>{{$val->created_at}}</td>
                                    <td>
                                        <a href="{{route('district.edit',[$val->id])}}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                        <!-- <form action="{{route('district.destroy', [$val->id])}}" method="POST">
                                          @method('DELETE')
                                          @csrf
                                          <button onclick="return confirm('xác nhận')">Delete</button>
                                        </form> -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </form>
                    </table>
                    {{ $district->appends(request()->all())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection