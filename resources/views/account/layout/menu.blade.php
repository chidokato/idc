<div id="sidebarMain" class="d-none"><aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
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
            <a class="js-navbar-vertical-aside-menu-link nav-link active" href="account/main" title="Dashboards">
              <i class="tio-home-vs-1-outlined nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Trang chủ</span>
            </a>
          </li>
          <!-- End Dashboards -->

          <li class="nav-item">
            <small class="nav-subtitle" title="Pages">Marketing</small>
            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
          </li>

          <!-- Pages -->
          
          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/mkt-register" title="Welcome page" data-placement="left">
              <i class="tio-add-circle nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Đăng ký Marketing</span>
            </a>
          </li>

          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="{{ route('tasks.user') }}" title="Welcome page" data-placement="left">
               <i class="tio-layers-outlined nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Quản lý Marketing</span>
            </a>
          </li>

          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/report" title="Welcome page" data-placement="left">
              <i class="tio-dashboard nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Quản lý tổng</span>
            </a>
          </li>

          <li class="nav-item">
            <small class="nav-subtitle" title="Pages">Quản lý tiền</small>
            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
          </li>

          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/wallet" title="Welcome page" data-placement="left">
              <i class="tio-wallet nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Ví tiền</span>
            </a>
          </li>

          <li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link " href="account/wallet/deposit" title="Welcome page" data-placement="left">
              <i class="tio-money nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Nạp tiền</span>
            </a>
          </li>

          <!-- Help -->
          <li class="navbar-vertical-aside-has-menu nav-footer-item ">
            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle " href="javascript:;" title="Help">
              <i class="tio-home-vs-1-outlined nav-icon"></i>
              <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Help</span>
            </a>

            <ul class="js-navbar-vertical-aside-submenu nav nav-sub">
              <li class="nav-item">
                <a class="nav-link" href="#" title="Resources &amp; tutorials">
                  <i class="tio-book-outlined dropdown-item-icon"></i> Resources &amp; tutorials
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" title="Keyboard shortcuts">
                  <i class="tio-command-key dropdown-item-icon"></i> Keyboard shortcuts
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" title="Connect other apps">
                  <i class="tio-alt dropdown-item-icon"></i> Connect other apps
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" title="What's new?">
                  <i class="tio-gift dropdown-item-icon"></i> What's new?
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" title="Contact support">
                  <i class="tio-chat-outlined dropdown-item-icon"></i> Contact support
                </a>
              </li>
            </ul>
          </li>
          <!-- End Help -->

          
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