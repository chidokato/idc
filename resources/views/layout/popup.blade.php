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
        <a target="_blank" href="https://zalo.me/{{$setting->zalo}}" class="pps-btn-img">
            <img src="assets/img/icon/zalo.png" alt="Gọi điện thoại" width="50">
        </a>
        </div>
    
    <div class="hotline-bar">
        <a target="_blank" href="https://zalo.me/{{$setting->zalo}}">
            <span class="text-hotline">Chat Zalo</span>
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

<style>
    .zalo-chat-widget {
        right: 75px !important;
        bottom: 52px !important;
    }
</style>

@if(strlen($setting->zalo ?? '') > 12)
<div class="zalo-chat-widget" data-oaid="{{$setting->zalo}}" data-welcome-message="Rất vui khi được hỗ trợ bạn!" data-autopopup="0" data-width="300" data-height="500"></div>
<script src="https://sp.zalo.me/plugins/sdk.js"></script>
@else
@if($setting->zalo)
<div style="position: fixed; bottom: 52px; right: 75px; z-index: 99999; width: 60px; height: 60px;">
    <div style="position: absolute; width: 100px; height: 100px; top: -20px; left: -20px; border-radius: 50%; border: 2px solid #0068ff; animation: phonering-alo-circle-anim 1.2s infinite ease-in-out; opacity: 0.5;"></div>
    <div style="position: absolute; width: 80px; height: 80px; top: -10px; left: -10px; border-radius: 50%; background-color: rgba(0, 104, 255, 0.4); animation: phonering-alo-circle-fill-anim 2.3s infinite ease-in-out;"></div>
    <a href="https://zalo.me/{{$setting->zalo}}" target="_blank" style="position: absolute; width: 60px; height: 60px; background-color: #fff; border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; animation: phonering-alo-circle-img-anim 1s infinite ease-in-out; top: 0; left: 0;">
        <img src="assets/img/icon/zalo.png" style="width: 35px; height: 35px; border-radius: 50%;" alt="Zalo Chat">
        <span style="color: #0068ff; font-size: 10px; font-weight: bold; margin-top: -2px;">Zalo</span>
    </a>
</div>
@endif
@endif

