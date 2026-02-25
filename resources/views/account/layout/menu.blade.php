<div id="sidebarMain" class="d-none">
  <aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered default navbar-vertical-aside-initialized ">
  <div class="navbar-vertical-container">
    <div class="navbar-vertical-footer-offset">
      <div class="navbar-brand-wrapper justify-content-between">
        <!-- Logo -->
          <a class="navbar-brand" href="{{asset('')}}" aria-label="Front">
            <img class="navbar-brand-logo" src="account/img/logo/logo.svg" alt="Logo">
            <img class="navbar-brand-logo-mini" src="account/img/logo/logomini.svg" alt="Logo">
          </a>
        
        <!-- End Logo -->

        <!-- Navbar Vertical Toggle -->
        <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
          <i class="tio-clear tio-lg"></i>
        </button>
        <!-- End Navbar Vertical Toggle -->
      </div>

      <!-- Content -->
      <div class="navbar-vertical-content">
        <ul class="navbar-nav navbar-nav-lg nav-tabs">
          <!-- Dashboards -->
          <li class="navbar-vertical-aside-has-menu show">
            <a class="js-navbar-vertical-aside-menu-link nav-link active" href="account/main" title="Trang chủ">
              <i class="tio-home-vs-1-outlined nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Trang chủ</span>
            </a>
          </li>
          <!-- End Dashboards -->

          <li class="nav-item">
            <small class="nav-subtitle" title="Marketing">Marketing</small>
            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
          </li>

          <!-- Pages -->
          
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/mkt-register" title="Đăng ký Marketing" data-placement="left">
              <i class="tio-add-circle nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Đăng ký Marketing</span>
            </a>
          </li>

          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/actualcosts" title="Quản lý Marketing" data-placement="left">
               <i class="tio-layers-outlined nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Quản lý Marketing</span>
            </a>
          </li>

          
          @if(auth()->check() && in_array((int)auth()->user()->rank, [1], true))
          <li class="nav-item">
            <small class="nav-subtitle" title="Quản lý">Quản lý</small>
            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
          </li>
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/report" title="Duyệt Marketing" data-placement="left">
              <i class="tio-dashboard nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Duyệt Marketing</span>
            </a>
          </li>
          <!-- <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/actualcosts" title="Chi phí thực tế" data-placement="left">
              <i class="tio-dashboard nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Chi phí thực tế</span>
            </a>
          </li> -->
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/deposits" title="Duyệt đóng tiền" data-placement="left">
              <i class="tio-dashboard nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Duyệt đóng tiền</span>
            </a>
          </li>
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/wallets" title="Quản lý ví tiền" data-placement="left">
              <i class="tio-dashboard nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Quản lý ví tiền</span>
            </a>
          </li>
          <!-- <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/bulk-mail" title="Gửi mail" data-placement="left">
              <i class="tio-dashboard nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Gửi mail</span>
            </a>
          </li> -->
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/statistical" title="Quản lý Marketing" data-placement="left">
               <i class="tio-layers-outlined nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Thống kê</span>
            </a>
          </li>
          @endif

          <li class="nav-item">
            <small class="nav-subtitle" title="Quản lý tiền">Quản lý tiền</small>
            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
          </li>

          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/wallet" title="Ví tiền" data-placement="left">
              <i class="tio-wallet nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Ví tiền</span>
            </a>
          </li>
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/wallet/deposit" title="Nạp tiền" data-placement="left">
              <i class="tio-money nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Nạp tiền</span>
            </a>
          </li>

          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/wallet/transfer" title="Chuyển tiền" data-placement="left">
              <i class="tio-money nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Chuyển tiền</span>
            </a>
          </li>

          <li class="nav-item">
            <small class="nav-subtitle" title="Pages">Tiện ích</small>
            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
          </li>
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/opened" title="Hướng dẫn sử dụng" data-placement="left">
              <i class="tio-book-opened nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Hướng dẫn sử dụng</span>
            </a>
          </li>
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/invite" title="Hướng dẫn sử dụng" data-placement="left">
              <i class="tio-book-opened nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Tạo thư mời</span>
            </a>
          </li>
        </ul>
      </div>
      <!-- End Content -->

      <!-- Footer -->
      <div class="navbar-vertical-footer">
        <ul class="navbar-vertical-footer-list">
          

          <li class="navbar-vertical-footer-list-item">
            <!-- Other Links -->
            <div class="hs-unfold">
              <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle" href="javascript:;" data-hs-unfold-options='{
                  "target": "#otherLinksDropdown",
                  "type": "css-animation",
                  "animationIn": "slideInDown",
                  "hideOnScroll": true
                 }'>
                <i class="tio-help-outlined"></i>
              </a>

              <div id="otherLinksDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu navbar-vertical-footer-dropdown">
                <span class="dropdown-header">Help</span>
                <a class="dropdown-item" href="#">
                  <i class="tio-book-outlined dropdown-item-icon"></i>
                  <span class="text-truncate pr-2" title="Resources &amp; tutorials">Resources &amp; tutorials</span>
                </a>
                <a class="dropdown-item" href="#">
                  <i class="tio-command-key dropdown-item-icon"></i>
                  <span class="text-truncate pr-2" title="Keyboard shortcuts">Keyboard shortcuts</span>
                </a>
                <a class="dropdown-item" href="#">
                  <i class="tio-alt dropdown-item-icon"></i>
                  <span class="text-truncate pr-2" title="Connect other apps">Connect other apps</span>
                </a>
                <a class="dropdown-item" href="#">
                  <i class="tio-gift dropdown-item-icon"></i>
                  <span class="text-truncate pr-2" title="What's new?">What's new?</span>
                </a>
                <div class="dropdown-divider"></div>
                <span class="dropdown-header">Contacts</span>
                <a class="dropdown-item" href="#">
                  <i class="tio-chat-outlined dropdown-item-icon"></i>
                  <span class="text-truncate pr-2" title="Contact support">Contact support</span>
                </a>
              </div>
            </div>
            <!-- End Other Links -->
          </li>

         
        </ul>
      </div>
      <!-- End Footer -->
    </div>
  </div>
</aside></div>