@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<?php use App\Models\OptionTranslation; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Tùy chọn</h2>
    <a class="add-iteam" href="{{route('option.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> {{__('lang.add')}}</button></a>
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
                    @if(count($option) > 0)
                    <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Sku</th>
                                    <th>Category</th>
                                    <th>Slug</th>
                                    <th>View</th>
                                    <th>Status</th>
                                    <th>User</th>
                                    <th>date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php dequycategory ($option,0,$str='',old('parent')); ?>  
                            </tbody>
                    </table>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php 
    function dequycategory ($menulist, $parent=0, $str='')
    {
        foreach ($menulist as $val) 
        {
            if ($val['parent'] == $parent) 
            { 
                ?>
                    <tr id="option" style="border-bottom: 1px solid #f3f6f9;">
                        <input type="hidden" name="id" id="id" value="{{$val->id}}" >
                        <!-- <td>
                            <label class="container"><input type="checkbox" name="foo[]" value="{{$val->id}}"><span class="checkmark"></span></label>
                        </td> -->
                        <td>{!! isset($val->img) ? '<img data-action="zoom" src="data/option/'.$val->img.'" class="thumbnail-img align-self-end" alt="">' : '' !!}</td>
                        <td><a href="{{route('option.edit',[$val->id])}}">{{$str}}{{$val->name}}</a></td>
                        <td>{{$val->sku}}</td>
                        <td>{{$val->category->name}}</td>
                        <td>{{$val->slug}}</td>
                        <td><input type="text" id="view" value="{{$val->view}}" name="" class="form-control cat_view"></td>
                        <td>
                            <label class="container"><input <?php if($val->status == 'true'){echo "checked";} ?> type="checkbox" id='status' ><span class="checkmark"></span></label>
                        </td>
                        <td>{{$val->user->name}}</td>
                        <td class="date">{{date('d/m/Y',strtotime($val->created_at))}} <sup title="Sửa lần cuối: {{date('d/m/Y',strtotime($val->updated_at))}}"><i class="fa fa-question-circle-o" aria-hidden="true"></i></sup> </td>
                        <td style="display: flex;">
                            <a href="admin/option/double/{{$val->id}}" class="mr-3"><i class="far fa-copy"></i></a>
                            <a href="{{route('option.edit',[$val->id])}}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>
                            <form action="{{route('option.destroy', [$val->id])}}" method="POST">
                              @method('DELETE')
                              @csrf
                              <button class="button_none" onclick="return confirm('Bạn muốn xóa bản ghi ?')"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php
                dequycategory ($menulist, $val['id'], $str.'_');
            }
        }
    }
?>

@endsection