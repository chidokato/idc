<?php use App\Models\Option; ?>
@foreach($data as $val)
<div class="col-md-2">
    <div class="form-group">
        @if($val->name == 'img_1')
        <label><input type="file" name="img_1"></label>
        @elseif($val->name == 'img_2')
        <label>Dây trên: <input type="file" name="img_1"></label>
        <label>Dây dưới: <input type="file" name="img_2"></label>
        <label>Dây cạnh mặt: <input type="file" name="img_3"></label>
        @else
        <label>{{$val->name}}: </label>
        @endif
    </div>
</div>
<div class="col-md-10 customize">
    <div class="form-group">
        @foreach(Option::where('parent', $val->id)->get() as $key => $subO)
        <label> <input value="{{$subO->name}}" class="form-check-input" type="radio" name="{{$val->sku}}"> {{$subO->name}}</label>
        @endforeach
    </div>
</div>
@endforeach
