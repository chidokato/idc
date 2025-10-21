<div class="hotline-phone-ring-wrap form-ring-wrap">
    <div class="hotline-phone-ring ">
        <div class="hotline-phone-ring-circle"></div>
        <div class="hotline-phone-ring-circle-fill"></div>
        <div class="hotline-phone-ring-img-circle click_popup">
            <a href="javascript:void(0)" class="pps-btn-img">
                <img src="assets/img/icon/dowload.png" alt="Gọi điện thoại" width="50">
            </a>
        </div>
        <div class="hotline-bar click_popup">
            <a href="javascript:void(0)">
                <span class="text-hotline">Bảng giá</span>
            </a>
        </div>
    </div>
</div>

<!-- <div class="hotline-phone-ring-wrap zalo-ring-wrap">
    <div class="hotline-phone-ring">
        <div class="hotline-phone-ring-circle"></div>
        <div class="hotline-phone-ring-circle-fill"></div>
        <div class="hotline-phone-ring-img-circle">
        <a target="_blank" href="https://zalo.me/{{$setting->hotline}}" class="pps-btn-img">
            <img src="assets/img/icon/zalo.png" alt="Gọi điện thoại" width="50">
        </a>
        </div>
    
    <div class="hotline-bar">
        <a target="_blank" href="https://zalo.me/{{$setting->hotline}}">
            <span class="text-hotline">Chat</span>
        </a>
    </div>
    </div>
</div> -->

<!-- <div class="hotline-phone-ring-wrap hotline-ring-wrap">
    <div class="hotline-phone-ring">
        <div class="hotline-phone-ring-circle"></div>
        <div class="hotline-phone-ring-circle-fill"></div>
        <div class="hotline-phone-ring-img-circle">
        <a href="tel:{{$setting->hotline}}" class="pps-btn-img">
            <img src="assets/img/icon/icon-call-nh.png" alt="Gọi điện thoại" width="50">
        </a>
        </div>
    
    <div class="hotline-bar">
        <a href="tel:{{$setting->hotline}}">
            <span class="text-hotline">Gọi ngay</span>
        </a>
    </div>
    </div>
</div> -->


<div id="popup-banggia" class="popup-overlay">
    <div class="popup-content">
        <span class="close-popup">&times;</span>
        <h3>NHẬN BẢNG GIÁ</h3>
        <form id="validateForm" method="post" action="question">
        @csrf
            <input type="hidden" id="current-url" name="url" value="">
            <label>
                <input type="text" name="name" placeholder="Họ và Tên (*)">
            </label>
            <label>
                <input type="text" name="phone" placeholder="Số điện thoại (*)">
            </label>
            <label>
                <input type="text" name="email" placeholder="Địa chỉ email">
            </label>
            <p class="sub">(*) Bằng việc nhấn vào "nhận báo giá". Quý khách đồng ý với <a target="_blank" href="https://indochinerealestate.vn/tin-noi-bo/chinh-sach-bao-mat-thong-tin-indochine-real-estate">Chính sách bảo mật thông tin </a> của chúng tôi.</p>
            <button class="btn btn-circle" type="submit">Nhận báo giá</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlInput = document.getElementById("current-url");
        if (urlInput) {
            urlInput.value = window.location.href;
        }
    });
</script>


<div class="zalo-chat-widget" data-oaid="2481470734556094804" data-welcome-message="Rất vui khi được hỗ trợ bạn!" data-autopopup="0" data-width="300" data-height="500"></div>

<script src="https://sp.zalo.me/plugins/sdk.js"></script>