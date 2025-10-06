@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Danh sách</h2>
    <a class="add-iteam" href="{{route('province.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> add</button></a>
</div>

<div class="row">
<form class="width100" action="{{ url()->current() }}" method="GET">
    <div class="col-xl-12 col-lg-12 search flex-start">
        <input type="text" value="{{ request()->key ?? '' }}" placeholder="Tìm kiếm..." class="form-control" name="key" onchange="this.form.submit()">
        <button type="submit" class="btn btn-success mr-2">Tìm kiếm</button>
        <a href="{{ url()->current() }}" class="btn btn-warning">
            Reset
        </a>
    </div>

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
                    @if(count($provinces) > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Home</th>
                                <th>Status</th>
                                <th>date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($provinces as $val)
                            <tr id="province">
                                <input type="hidden" name="" id="id" value="{{$val->id}}">
                                <td class="thumb"> {!! $val->img? '<img src="data/images/'.$val->img.'">':'' !!} </td>
                                <td>{{$val->name}}</td>
                                <td>{{$val->code}}</td>
                                <td>
                                    <label class="container"><input <?php if($val->home == 'true'){echo "checked";} ?> type="checkbox" id='home_province' ><span class="checkmark"></span></label>
                                </td>
                                <td>
                                    <label class="container"><input <?php if($val->status == 'true'){echo "checked";} ?> type="checkbox" id='status_province' ><span class="checkmark"></span></label>
                                </td>
                                <td>{{$val->updated_at}}</td>
                                <td style="display: flex;">
                                    <a href="{{route('province.edit',[$val->id])}}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    <!-- <form action="{{route('province.destroy',[$val->id])}}" method="POST">
                                      @method('DELETE')
                                      @csrf
                                      <button class="button_none" onclick="return confirm('xác nhận')"><i class="fas fa-trash-alt"></i></button>
                                    </form> -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="search">
                        <div>Hiển thị: </div>
                        <select class="form-control paginate" name="per_page" onchange="this.form.submit()">
                            <option value="10" {{ request()->per_page == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request()->per_page == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request()->per_page == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request()->per_page == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <div> Từ {{ $provinces->firstItem() }} đến {{ $provinces->lastItem() }} trên tổng {{ $provinces->total() }} </div>
                        {{ $provinces->appends(request()->all())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
</div>
@endsection

@section('js')

@endsection