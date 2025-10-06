@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">{{__('lang.post')}}</h2>
    <a class="add-iteam" href="{{route('product.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> {{__('lang.add')}}</button></a>
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
            <div class="tab-content">
                <div class="tab-pane overflow active" id="tab2">
                    @if(count($posts) > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Slug</th>
                                <!-- <th>View</th> -->
                                <th>Status</th>
                                <th>User</th>
                                <th>Sort By</th>
                                <th>date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                            <tr>
                                <td>
                                    @foreach($post->PostTranslation as $key => $val)
                                    <div><a href="{{route('product.edit',[$val->id])}}" class="mr-2"> [{{$val->locale}}] {{$val->name}} </a> </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($post->PostTranslation as $key => $val)
                                    <div>{{$val->CategoryTranslation->name}}</div>
                                    @endforeach
                                </td>
                                <td class="line-height">{{$post->slug}}</td>
                                <td class="line-height">{{$post->status}}</td>
                                <td class="line-height">Admin</td>
                                <td class="line-height">{{$post->sort_by}}</td>
                                <td>
                                    @foreach($post->PostTranslation as $key => $val)
                                    <div>{{$val->updated_at}}</div>
                                    @endforeach
                                </td>
                                <td>
                                    <!-- <a href="{{route('product.edit',[$post->id])}}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i> Edit</a> -->
                                    <form action="{{route('product.destroy', [$post->id])}}" method="POST">
                                      @method('DELETE')
                                      @csrf
                                      <button style="padding: 0" class="button_none" onclick="return confirm('Bạn muốn xóa bản ghi ?')"><i class="fas fa-trash-alt"></i> Del</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </form>
                    </table>
                    @endif
                </div>
                {{ $posts->links(); }}
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .line-height{line-height: 71px;}
</style>
@endsection