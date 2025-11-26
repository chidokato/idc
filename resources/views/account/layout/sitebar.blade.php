<div class="users">
    <div><img src="{{ Auth::user()->avatar }}" alt="user"></div>
    <div>
        <h4>{{ Auth::user()->name }}</h4>
        <p>{{ Auth::user()->email }}</p>
        <p>{{ Auth::user()->phone }}</p>
    </div>
    <button class="edit" onclick="window.location.href='{{ route('account.edit') }}'">sửa</button>
</div>

<div class="row-btn">
    <button class="btn btn-dangky" onclick="window.location.href='{{route('account.mktregister')}}'">ĐĂNG KÝ MARKETING</button>
    <button class="btn btn-quanly" onclick="window.location.href=''">QUẢN LÝ CHI PHÍ MARKETING</button>
</div>

<div class="widget widget-list mb-3">
    <h4><span>DANH MỤC</span></h4>
    <ul>
        <li><a href="#"><i class="icon-next me-2"></i>Facebook</a></li>
        <li><a href="#"><i class="icon-next me-2"></i>Tin nội bộ</a></li>
    </ul>
</div>