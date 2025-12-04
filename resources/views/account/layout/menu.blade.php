<div class="menu-account">
	<ul>
		<li><a href="account/task">Quản lý</a></li> |
		<li><a href="account/mkt-register">Đăng ký</a></li> @if(Auth::User()->rank < 3) | 
		<li><a href="account/task">Đóng tiền</a></li> @endif @if(Auth::User()->rank == 1) |
		<li><a href="account/report">QL Tổng</a></li>@endif 
	</ul>
</div>