<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">
<!-- <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar"> -->
    
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin')}}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup> </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="admin/main">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <!-- <hr class="sidebar-divider"> -->
    <!-- <div class="sidebar-heading">Interface</div> -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('category.index')}}"><i class="fas fa-fw fa-chart-area"></i><span>Danh mục</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('post.index')}}"><i class="fas fa-fw fa-chart-area"></i><span>Dự án</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('news.index')}}"><i class="fas fa-fw fa-chart-area"></i><span>Tin tức</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Internal"
            aria-expanded="true" aria-controls="Internal">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Internal</span>
        </a>
        <div id="Internal" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('internalcategory.index')}}">Category</a>
                <a class="collapse-item" href="{{route('internalpost.index')}}">Post</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#customer"
            aria-expanded="true" aria-controls="customer">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Khách hàng</span>
        </a>
        <div id="customer" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('customer.index')}}">Data</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#setting"
            aria-expanded="true" aria-controls="setting">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Cấu hình</span>
        </a>
        <div id="setting" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('setting.index')}}">Cấu hình</a>
                <a class="collapse-item" href="{{route('departments.index')}}">Phòng ban</a>
                <a class="collapse-item" href="{{route('suppliers.index')}}">Nhà cung cấp</a>
                <a class="collapse-item" href="{{route('channels.index')}}">Kênh chạy</a>
                <a class="collapse-item" href="{{route('duan.index')}}">Dự án</a>
                <a class="collapse-item" href="{{route('menu.index')}}">Menu</a>
                <!-- <a class="collapse-item" href="{{route('option.index')}}">Tùy chọn</a> -->
                <a class="collapse-item" href="{{route('slider.show', 'slider')}}">Slider</a>
                <a class="collapse-item" href="{{route('slider.show', 'banner')}}">Banner</a>
            </div>
        </div>
    </li>
   
    <!-- <li class="nav-item">
        <a class="nav-link" href="admin/messages/list"><i class="fas fa-fw fa-chart-area"></i><span>Messages</span></a>
    </li> -->
    
    <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#giaydep"
            aria-expanded="true" aria-controls="giaydep">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Quản lý giày dép</span>
        </a>
        <div id="giaydep" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="admin/quanlykho/list">Tồn kho</a>
                <a class="collapse-item" href="admin/nhaphang/list">Nhập hàng</a>
                <a class="collapse-item" href="admin/banhang/list">Bán hàng</a>
                <a class="collapse-item" href="admin/giaydep/list">option</a>
            </div>
        </div>
    </li> -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Location</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('province.index')}}">Province</a>
                <a class="collapse-item" href="{{route('district.index')}}">District</a>
                <a class="collapse-item" href="{{route('ward.index')}}">Ward</a>
                <a class="collapse-item" href="{{route('street.index')}}">Street</a>
            </div>
        </div>
    </li>
    <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#setting"
            aria-expanded="true" aria-controls="setting">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Cấu hình</span>
        </a>
        <div id="setting" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="admin/menu/list">Menu</a>
                <a class="collapse-item" href="admin/themes/list">Giao diện</a>
                <a class="collapse-item" href="admin/setting/list">Cài đặt</a>
                <a class="collapse-item" href="admin/seo/list">Seo</a>
            </div>
        </div>
    </li> -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#users"
            aria-expanded="true" aria-controls="users">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Người dùng</span>
        </a>
        <div id="users" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('users.index')}}">Admin</a>
                <a class="collapse-item" href="{{route('users.member')}}">Member</a>
            </div>
        </div>
    </li>
    <!-- <li class="nav-item">
        <a class="nav-link" href="{{route('users.index')}}"><i class="fas fa-fw fa-chart-area"></i><span>Người dùng</span></a>
    </li> -->

    

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>