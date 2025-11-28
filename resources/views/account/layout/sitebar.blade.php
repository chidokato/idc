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
    <button class="btn btn-quanly" onclick="window.location.href='{{route('task.index')}}'">QUẢN LÝ CHI PHÍ MARKETING</button>
    @if(Auth::User()->rank < 3)<button class="btn btn-quanly" onclick="window.location.href='{{route('task.index')}}'">QUẢN LÝ ĐÓNG TIỀN</button>@endif
    @if(Auth::User()->rank == 1)<button class="btn btn-quanly" onclick="window.location.href='{{route('report.index')}}'">QUẢN LÝ TỔNG</button>@endif
</div>

<div class="widget widget-list mb-3">
    <h4><span>Liên hệ</span></h4>
    <ul>
        <li class="mb-3">Hỗ trợ kỹ thuật: <a href="https://zalo.me/0977572947" target="_blank"><i class="icon-next me-2"></i><strong>Nguyễn Tuấn</strong></a></li>
        <li class="mb-3">Hỗ trợ đóng tiền MKT: <a href="https://zalo.me/0977572947" target="_blank"><i class="icon-next me-2"></i><strong>Hằng Phan</strong></a></li>
        <li class="mb-3">Hỗ trợ đăng ký MKT: <a href="https://zalo.me/0977572947" target="_blank"><i class="icon-next me-2"></i><strong>Tống Hồ Phương Thúy</strong></a></li>
        <li class="mb-3">Hỗ trợ nạp tiền bds.com: 
            <a href="https://zalo.me/09775729047" target="_blank"><i class="icon-next me-2"></i><strong>Tống Hồ Phương Thúy</strong></a>
            <a href="https://zalo.me/0977572947" target="_blank"><i class="icon-next me-2"></i><strong>Nguyễn Tuấn</strong></a>
        </li>
    </ul>
</div>