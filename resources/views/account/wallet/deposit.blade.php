@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')

@endsection

@section('body') @endsection

@section('content')

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-no-gutter">
                <li class="breadcrumb-item"><a class="breadcrumb-link" href="account/main">Account</a></li>
                <li class="breadcrumb-item"><a class="breadcrumb-link" href="account/wallet">Ví tiền</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nạp tiền</li>
                </ol>
                </nav>
                <h1 class="page-header-title">Nạp tiền</h1>
            </div>
            
        </div>
    <!-- End Row -->
    </div>

      <div class="row">
        <div class="col-lg-8">
          <div class="card mb-3">
            <div class="card-header">
              <h2 class="card-header-title h5">Nạp tiền</h2>

            </div>
            <div class="card-body">
              @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

              <form method="POST"
                action="{{ route('wallet.deposit.submit') }}"
                enctype="multipart/form-data">
              @csrf

              <div class="mb-3">
                  <label>Số tiền đã chuyển</label>
                  <!-- input gốc: hiển thị trực tiếp VND -->
                  <input type="text"
                         id="amount"
                         class="form-control"
                         inputmode="numeric"
                         autocomplete="off"
                         placeholder="0 ₫"
                         required>
                  <!-- input hidden: giá trị số thật để submit -->
                  <input type="hidden" id="amount_raw" name="amount">

                  <div class="invalid-feedback">
                    Số tiền phải ≥ 1.000 và là bội số của 1.000
                  </div>
              </div>

              <div class="mb-3">
                  <label>Ảnh chứng minh chuyển khoản</label>
                  <input type="file"
                         name="proof_image"
                         class="form-control"
                         accept="image/*"
                         required>
              </div>

              <button class="btn btn-primary w-100">
                  Gửi yêu cầu nạp tiền
              </button>
          </form>
            </div>

          </div>

          <div class="card ">
            <div class="card-header">
              <h2 class="card-header-title h5">Lịch sử nạp tiền</h2>
            </div>
            <div class="card-body">
              <table class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer">
                <thead class="thead-light">
                  <tr>
                      <th>Ngày</th>
                      <th>Số tiền</th>
                      <th>Trạng thái</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($deposits as $d)
                        <tr>
                            <td>{{ $d->created_at }}</td>
                            <td>{{ number_format($d->amount) }} đ</td>
                            <td>
                                @if($d->status=='pending')
                                    ⏳ Chờ duyệt
                                @elseif($d->status=='approved')
                                    ✅ Đã duyệt
                                @else
                                    ❌ Từ chối
                                @endif
                            </td>
                        </tr>
                        @endforeach
                </tbody>
              </table>
              {{ $deposits->links() }}
            </div>
          </div>

        </div>
        <div class="col-lg-4  ">
          <div class="card">
          <div class="card-header">
              <h2 class="card-header-title h5">Thông tin chuyển khoản</h2>
            </div>
            <div class="card-body">
                <div class="row align-items-center mb-3">
                  <span class="col-6">Số tài khoản:</span>
                  <h4 class="col-6 text-right text-dark mb-0">0977572947</h4>
                </div>
                <!-- End Row -->
                <div class="row align-items-center mb-3">
                  <span class="col-6">Tên người thụ hưởng:</span>
                  <h4 class="col-6 text-right text-dark mb-0">NGUYỄN VĂN TUẤN</h4>
                </div>
                <!-- End Row -->
                <div class="row align-items-center mb-3">  
                  <span class="col-6">Ngân hàng:</span>
                  <h4 class="col-6 text-right text-dark mb-0">VP bank</h4>
                </div>
                <!-- End Row -->
                <div class="row align-items-center mb-3">
                  <span class="col-6">Nội dung CK:</span>
                  <h3 class="col-6 text-right text-dark mb-0">{{ $user->yourname }}</h3>
                </div>
                <!-- End Row -->
                <hr class="my-4">
                  <img class="w-100" src="account/img/qr/tuan.jpg">
              </div>
        </div>
      </div>
      </div>
</div>

@endsection


@section('js')
<script>
  const displayEl = document.getElementById('amount');       // input gốc (hiển thị)
  const rawEl     = document.getElementById('amount_raw');   // input hidden (submit)

  const STEP = 1000;
  const MIN  = 1000;

  function onlyDigits(str) {
    return (str || '').replace(/\D+/g, '');
  }

  function formatVND(n) {
    const val = Number(n) || 0;
    return val.toLocaleString('vi-VN');
  }

  function setValidity(isOk, msg = '') {
    displayEl.setCustomValidity(isOk ? '' : msg);
    displayEl.classList.toggle('is-invalid', !isOk);
    displayEl.classList.toggle('is-valid', isOk && displayEl.value.trim() !== '');
  }

  function renderFromDigits(digits) {
    if (!digits) {
      displayEl.value = '';
      rawEl.value = '';
      setValidity(false, 'Vui lòng nhập số tiền');
      return;
    }

    const n = Number(digits);
    rawEl.value = String(n);           // giá trị thật để submit
    displayEl.value = formatVND(n);    // hiển thị trực tiếp trên input gốc

    const ok = (n >= MIN) && (n % STEP === 0);
    setValidity(ok, ok ? '' : 'Số tiền phải ≥ 10.000 và là bội số của 10.000');
  }

  // Khi gõ: luôn format ngay trong input gốc
  displayEl.addEventListener('input', () => {
    const digits = onlyDigits(displayEl.value);
    renderFromDigits(digits);
  });

  // Khi rời khỏi ô: tự làm tròn về bội số 50.000 gần nhất (nếu muốn)
  displayEl.addEventListener('blur', () => {
    const digits = onlyDigits(displayEl.value);
    if (!digits) return;

    let n = Number(digits);
    n = Math.round(n / STEP) * STEP;   // làm tròn về bội số 50k gần nhất
    renderFromDigits(String(n));
  });

  // Init trạng thái
  setValidity(false, 'Vui lòng nhập số tiền');
</script>

@endsection