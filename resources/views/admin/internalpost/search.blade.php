@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<?php use App\Models\Category; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý sản phẩm</h2>
    <a class="add-iteam" href="{{route('post.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> Thêm mới</button></a>
</div>

<style type="text/css">
    .search{ display:flex; margin-bottom:15px }
    .search input, .search select{ width:200px; margin-right:15px }
</style>

<div class="row">
    <form method="post" action="{{route('post_search')}}" enctype="multipart/form-data">
    @csrf
    <div class="col-xl-12 col-lg-12 search">
        <input type="name" value="{{isset($name) ? $name:''}}" placeholder="Tên sản phẩm" class="form-control" name="name">
        <select class="form-control" name="category_id">
            <option value="">...</option>
            @foreach($category as $val)
            <option {{isset($category_id) && $category_id == $val->id ? 'selected':''}} value="{{$val->id}}">{{$val->name}}</option>
                @foreach(Category::where('parent', $val->id)->get() as $sub)
                <option {{isset($category_id) && $category_id == $sub->id ? 'selected':''}} value="{{$sub->id}}">--{{$sub->name}}</option>
                @endforeach
            @endforeach
        </select>
        <button type="submit" class="btn btn-success">Tìm kiếm</button>
    </div>
    </form>
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
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Hot</th>
                                <th>Status</th>
                                <th>date</th>
                                <th>User</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="data-wrapper">
                            @include('admin.post.data_loadmore')
                        </form>
                    </table>
                    @endif
                </div>
                <div class="text-center">
                    <button class="btn btn-success load-more-data"><i class="fa fa-refresh"></i> Hiển thị thêm dữ liệu...</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="container mt-5" style="max-width: 750px">
    <div id="data-wrapper">
        @include('data')
    </div>
    <div class="text-center">
        <button class="btn btn-success load-more-data"><i class="fa fa-refresh"></i> Load More Data...</button>
    </div>
</div> -->

<style type="text/css">
    .name{ width:500px; overflow:hidden; text-overflow: ellipsis; }
    .slug{ font-size:.8rem }
    .thumb{ padding:0px !important; width:43px; margin:0px; }
    .thumb img{ width:59px; height:59px; object-fit:cover; }
    .load-more-data{ margin-bottom:30px }
</style>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var ENDPOINT = "admin/post/search";
    var page = 1;
  
    $(".load-more-data").click(function(){
        page++;
        infinteLoadMore(page);
    });
  
    /*------------------------------------------
    --------------------------------------------
    call infinteLoadMore()
    --------------------------------------------
    --------------------------------------------*/
    function infinteLoadMore(page) {
        $.ajax({
                url: ENDPOINT + "?page=" + page,
                datatype: "html",
                type: "get",
                beforeSend: function () {
                    $('.auto-load').show();
                }
            })
            .done(function (response) {
                if (response.html == '') {
                    $('.auto-load').html("We don't have more data to display :(");
                    return;
                }
                $('.auto-load').hide();
                $("#data-wrapper").append(response.html);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                console.log('Server error occured');
            });
    }
</script>

@endsection


