<!------------------- NAVIGATOR ------------------->
<header class="navhome" id="header">
    <nav class="navbar navbar-expand-lg navbar-dark" aria-label="Ninth navbar example">
        <div class="container">
          
          <div class="toggle-menu" data-bs-toggle="button">
                <button class="navbar-toggler ico-menu" id="navbarToggler">
                    <div>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
            </div>
    
          <div class="navbar-collapse flex-grow-1" id="navbarsExample07XL">
            <ul class="collapse navbar-nav mb-lg-0">
                <li class="nav-item active">
                    <a class="nav-link" href="{{asset('')}}">Trang chủ</a>
                </li>
                @foreach($menu as $key => $val)
                    @if($val->parent == 0)
                        @if($key==2)
                        <li class="logo">
                            <a class="navbar-brand" href="{{asset('')}}"><img src="data/images/{{$setting->img}}" alt="" class="mw-100"></a>
                        </li>
                        @else
                            @if(count($menu->where('parent', $val->id)) == 0)
                            <li class="nav-item">
                                <a class="nav-link" href="{{$val->slug}}">{{$val->name}}</a>
                            </li>
                            @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="{{$val->slug}}" data-bs-toggle="dropdown" onclick="myFunctLink(this)">{{$val->name}}</a>
                                <a class="expand dropdown-toggle d-lg-none" href="#" data-bs-toggle="dropdown"></a>
                                <div class="dropdown-menu">
                                    <ul>
                                        @foreach($menu->where('parent', $val->id) as $sub)
                                        <li><a href="{{$sub->slug}}" class="submenu-link"><i class="icon-next me-2"></i>{{$sub->name}}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            @endif
                        @endif
                    @endif
                @endforeach
                <li class="nav-item">
                    <div class="icon-header">
                        <a target="_blank" href="{{$setting->facebook}}"><img src="assets/images/facebook-logo-icon.png"></a>
                        <a target="_blank" href="{{$setting->youtube}}"><img src="assets/images/youtube-logo-icon.png"></a>
                        
                        @if(Auth::check())
                        <a href="{{ Auth::check() ? url('account/main') : url('dangnhap') }}">
                            <img src="{{ Auth::user()->avatar }}" alt="user">
                        </a>
                        @else
                        <a href="{{ url('dangnhap') }}">
                            <img src="{{ asset('assets/images/user-icon.png') }}" alt="user">
                        </a>
                        @endif
                    </div>
                </li>
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="news.htm" data-bs-toggle="dropdown" onclick="myFunctLink(this)">Tin tức</a>
                    <a class="expand dropdown-toggle d-lg-none" href="#" data-bs-toggle="dropdown"></a>
                    <div class="dropdown-menu">
                        <ul>
                            <li><a href="#" class="submenu-link"><i class="icon-next me-2"></i>Tin phong thủy</a></li>
                            <li><a href="#" class="submenu-link"><i class="icon-next me-2"></i>Thẩm định giá</a></li>
                            <li><a href="#" class="submenu-link"><i class="icon-next me-2"></i>Dịch vụ công chứng</a></li>
                            <li><a href="#" class="submenu-link"><i class="icon-next me-2"></i>Quy hoạch đô thị</a></li>
                        </ul>
                    </div>
                </li> -->
                
            </ul>
            
          </div>
        </div>
      </nav>
</header>
<!------------------- END NAVIGATOR ------------------->