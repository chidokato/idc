@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<?php use App\Models\menuTranslation; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Nhà cung cấp</h2>
    <a class="add-iteam" href="{{route('suppliers.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> {{__('lang.add')}}</button></a>
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
                    @if(count($suppliers) > 0)
                    <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Slug</th>
                                    <th>View</th>
                                    <th>User</th>
                                    <th>date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php dequymenu ($suppliers,0,$str='',old('parent')); ?>  
                            </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    function dequymenu ($menulist, $parent=0, $str='')
    {
        foreach ($menulist as $val) 
        {
            if ($val['parent'] == $parent) 
            { 
                ?>
                    <tr id="menu" style="border-bottom: 1px solid #f3f6f9;">
                        <input type="hidden" name="id" id="id" value="{{$val->id}}" >
                        <td>{!! isset($val->img) ? '<img data-action="zoom" src="data/menu/'.$val->img.'" class="thumbnail-img align-self-end" alt="">' : '' !!}</td>
                        <td><a href="{{ route('suppliers.duplicate', $val->id) }}" class="mr-3" title="Nhân bản"><i class="fas fa-copy" aria-hidden="true"></i></a> <a href="{{route('suppliers.edit', $val)}}">{{$str}}{{$val->name}}</a> </td>
                        <td>{{$val->code}}</td>
                        <td><input type="text" id="menu_view" value="{{$val->view}}" name="" class="form-control cat_view"></td>
                        <td>{{$val->user->name}}</td>
                        <td class="date">{{date('d/m/Y',strtotime($val->created_at))}} <sup title="Sửa lần cuối: {{date('d/m/Y',strtotime($val->updated_at))}}"><i class="fa fa-question-circle-o" aria-hidden="true"></i></sup> </td>
                        <td style="display: flex;">
                            <a href="{{ route('suppliers.edit', $val) }}" class="mr-3"><i class="fas fa-edit" aria-hidden="true"></i></a>
                            <form action="{{ route('suppliers.destroy', $val) }}"
                                  method="POST"
                                  style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Bạn chắc chắn?')" class="button_none">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php
                dequymenu ($menulist, $val['id'], $str.'_');
            }
        }
    }
?>

@endsection