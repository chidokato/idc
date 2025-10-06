
    <div class="card card-s card-s4 ">
        <a href="{{$val->category->slug}}/{{$val->slug}}">
            <span><img src="assets/images/space-3.gif" class="card-img-top" style="background-image: url('data/images/{{$val->img?$val->img:'no_image.jpg'}}');" alt="..."></span>
            <span class="cat">
                {!! $val->monopoly?'<span class="room-status bg-red">Độc quyền</span>':'' !!}
                <!-- {!! $val->for_sale?'<span class="room-status bg-red">Đang mở bán</span>':'' !!} -->
                {!! $val->new_product?'<span class="room-status">Mới ra mắt</span>':'' !!}
            </span>
            <!-- <span class="view-more">Chi tiết</span> -->
            <div class="product-status">
                <span> {!! $val->acreage ? '<i class="icon-acreage me-1"></i>'.$val->acreage.($val->acreage_max ? ' - '.$val->acreage_max : '').' m<sup>2</sup>' : '' !!}</span>
                <span> {!! $val->bedroom ? '<i class="icon-bed me-1"></i>'.$val->bedroom.($val->bedroom_max ? ' - '.$val->bedroom_max : '').' PN' : '' !!}</span>
                <span> {!! $val->wc ? '<i class="icon-bathroom me-1"></i>'.$val->wc.($val->wc_max ? ' - '.$val->wc_max : '').' WC' : '' !!}</span>
            </div>
        </a>
        <div class="card-body">
            <div class="card-body-wrap">
                <h5 class="card-title"><a href="{{$val->category->slug}}/{{$val->slug}}" class="text-truncate">{{$val->name}}</a></h5>
                <div class="card-info" title="{{$val->address}}{{ $val->street_id ? ', '.$val->Street->name:'' }}{{$val->ward_id? ', '.$val->Ward->name:''}}{{', '.$val->District->name}}{{', '.$val->Province->name}}">
                    <span class="text-truncate-set text-truncate-set-1">
                        {{$val->address}}
                        {{ $val->street_id ? ($val->address?', ':'').$val->Street->name:'' }}
                        <!-- {{$val->ward_id? ', '.$val->Ward->name:''}} -->
                        {{$val->district_id? ', '.$val->District->name : '' }}
                        {{$val->province_id? ', '.$val->Province->name : ''}}
                    </span>
                </div>
            </div>
            <div class="d-flex card-body-price">
                <div class="card-price">
                    Giá: <span class="current-price">
                        {{$val->price >= 1000000000?$val->price/1000000000 . ' Tỷ': ($val->price? $val->price/1000000 . ' Triệu':'Liên hệ') }}
                        {{$val->price_max >= 1000000000? ' - ' . $val->price_max/1000000000 . ' Tỷ':($val->price_max?' - ' . $val->price_max/1000000 . ' Triệu':'')}}
                    </span>
                </div>  
                <div>{{ $val->total_product? 'Số căn: '.$val->total_product:'' }}</div>
            </div>
        </div>
    </div>
