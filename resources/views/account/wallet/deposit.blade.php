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
        <div class="col-lg-6">
          <div class="card mb-3">
            <div class="card-header">
              <h2 class="card-header-title h5">Nạp tiền</h2>

            </div>
            <div class="card-body">
              @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
              @endif

              
              @if($activeDeposit)
                @php
                  $remainSeconds = max(0, now()->diffInSeconds(\Carbon\Carbon::parse($activeDeposit->expires_at), false));
                @endphp
                <div class="alert alert-info text-center d-flex align-items-center justify-content-center" style="gap:5px; padding: 2px;">
                  Vui lòng chuyển khoản đúng số tiền và upload UNC trong 
                  <span id="countdown"
                      data-remain="{{ $remainSeconds }}"
                      data-expire-url="{{ route('wallet.deposit.expire', $activeDeposit->id) }}"></span>
                  phút
                </div>

                <div class="bankinfo">
                  <div class="maqr">
                    <img class="w-100" src="account/img/qr/phamthithuhang.jpg">
                    <div>Số tiền nạp</div>
                    <div class="price"> {{ number_format($activeDeposit->amount) }} đ </div>
                  </div>
                </div>
                <div class="bankname">
                  <form method="POST" action="{{ route('wallet.deposit.upload', $activeDeposit->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                      <label>Ngân hàng:</label>
                      <input disabled type="" name="" class="form-control" value="VP BANK (Việt Nam Thịnh Vượng)">
                    </div>
                    <div class="form-group">
                      <label>Tài khoản:</label>
                      <input disabled type="" name="" class="form-control" value="PHẠM THỊ THU HẰNG">
                    </div>
                    <div class="form-group">
                      <label>Tên tài khoản:</label>
                      <input disabled type="" name="" class="form-control" value="20825092002">
                    </div>
                    <div class="form-group">
                      <label>Ảnh UNC</label>
                      <input type="file" name="proof_image" class="form-control" accept="image/*" required>
                    </div>
                    <button class="btn btn-primary w-100">Gửi yêu cầu nạp tiền !!</button>
                  </form>
                </div>
                
              @else
                {{-- Form nhập số tiền tạo lệnh --}}
                <form method="POST" action="{{ route('wallet.deposit.create') }}">
                  @csrf
                  <div class="mb-3">
                    <label>Số tiền cần nạp</label>
                    <input type="text"
                           id="amount"
                           class="form-control"
                           inputmode="numeric"
                           autocomplete="off"
                           placeholder="0 ₫"
                           required>

                    <input type="hidden" id="amount_raw" name="amount">

                    <div class="invalid-feedback">
                      Số tiền phải ≥ 10.000 và là bội số của 1.000
                    </div>

                  </div>
                  <button class="btn btn-primary w-100">Tạo lệnh nạp & hiện QR</button>
                </form>
              @endif


            </div>

          </div>
        </div>
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <h2 class="card-header-title h5">Lịch sử nạp tiền</h2>
            </div>

            <div class="card-body ">
              <div class="table-responsive datatable-custom">
                
              
              <table class="table table-lg table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                  <tr>
                    <th>Ngày tạo</th>
                    <th>Số tiền</th>
                    <th>Mã GD</th>
                    <th>Trạng thái</th>
                  </tr>
                </thead>

                <tbody>
                  @forelse($deposits as $d)
                    <tr>
                      <td>
                        {{ $d->created_at->format('d/m/Y H:i') }}
                      </td>

                      <td>
                        <strong>{{ number_format($d->amount) }} đ</strong>
                      </td>

                      <td>
                        <span class="badge badge-soft-info">
                          {{ $d->transaction_code ?? '—' }}
                        </span>
                      </td>

                      <td>
                        @switch($d->status)

                          @case('pending_upload')
                            <span class="badge badge-soft-warning">
                              ⏳ Chờ chuyển khoản
                            </span>
                            @if($d->expires_at)
                              <div class="small text-muted">
                                Hết hạn:
                                {{ $d->expires_at->format('H:i d/m') }}
                              </div>
                            @endif
                          @break

                          @case('pending')
                            <span class="badge badge-soft-primary">
                              🔍 Chờ duyệt
                            </span>
                          @break

                          @case('approved')
                            <span class="badge badge-soft-success">
                              ✅ Đã duyệt
                            </span>
                          @break

                          @case('rejected')
                            <span class="badge badge-soft-danger">
                              ❌ Từ chối
                            </span>
                          @break

                          @case('expired')
                            <span class="badge badge-soft-secondary">
                              ⌛ Hết hạn
                            </span>
                          @break

                          @case('canceled')
                            <span class="badge badge-soft-dark">
                              🚫 Đã huỷ
                            </span>
                          @break

                          @default
                            <span class="badge badge-soft-light">
                              —
                            </span>

                        @endswitch
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted py-4">
                        Chưa có lịch sử nạp tiền
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
              <div class="mt-3">
                {{ $deposits->links() }}
              </div>
              </div>

            </div>
          </div>


        </div>
        
      </div>
</div>

@endsection


@section('js')
<script>
(function () {
  const el = document.getElementById('countdown');
  if (!el) return;

  let remain = Number(el.dataset.remain || 0);

  function pad(n){ return String(n).padStart(2,'0'); }

  function tick(){
    if (remain <= 0) { el.textContent = "00:00"; return; }
    const m = Math.floor(remain / 60);
    const s = remain % 60;
    el.textContent = `${pad(m)}:${pad(s)}`;
    remain--;
  }

  tick();
  setInterval(tick, 1000);
})();
</script>

<script>
(async function () {
  const el = document.getElementById('countdown');
  if (!el) return;

  let remain = Number(el.dataset.remain || 0);
  const expireUrl = el.dataset.expireUrl;

  function pad(n){ return String(n).padStart(2,'0'); }

  async function expireNow() {
    const res = await fetch(expireUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    });

    const json = await res.json();
    console.log('expire response:', json);

    // nếu updated=0 thì vẫn reload để UI đúng, nhưng bạn biết backend chưa update
    location.reload();
  }

  function tick(){
    if (remain <= 0) {
      el.textContent = "00:00";
      expireNow();
      return;
    }
    const m = Math.floor(remain / 60);
    const s = remain % 60;
    el.textContent = `${pad(m)}:${pad(s)}`;
    remain--;
  }

  tick();
  setInterval(tick, 1000);
})();
</script>


<script>
(function () {
  const displayEl = document.getElementById('amount');      // input hiển thị
  const rawEl     = document.getElementById('amount_raw');  // input hidden submit

  if (!displayEl || !rawEl) return;

  // ====== cấu hình ======
  const MIN  = 10000;  // tối thiểu
  const STEP = 1000;   // bội số

  // ====== helpers ======
  const onlyDigits = (s) => (s || '').replace(/\D+/g, '');
  const formatVND  = (n) => (Number(n) || 0).toLocaleString('vi-VN');

  function setInvalid(msg) {
    displayEl.setCustomValidity(msg || 'Không hợp lệ');
    displayEl.classList.add('is-invalid');
    displayEl.classList.remove('is-valid');
  }

  function setValid() {
    displayEl.setCustomValidity('');
    displayEl.classList.remove('is-invalid');
    if (displayEl.value.trim() !== '') displayEl.classList.add('is-valid');
  }

  function validate(n) {
    if (!n) return { ok: false, msg: 'Vui lòng nhập số tiền' };
    if (n < MIN) return { ok: false, msg: `Số tiền phải ≥ ${formatVND(MIN)} đ` };
    if (n % STEP !== 0) return { ok: false, msg: `Số tiền phải là bội số của ${formatVND(STEP)} đ` };
    return { ok: true, msg: '' };
  }

  // Giữ vị trí con trỏ sau khi format
  function applyFormatKeepCaret() {
    const before = displayEl.value;
    const caret  = displayEl.selectionStart ?? before.length;

    // Đếm số digit bên trái con trỏ (để phục hồi caret)
    const leftDigitsCount = onlyDigits(before.slice(0, caret)).length;

    // Parse digits -> number
    const digits = onlyDigits(before);
    if (!digits) {
      displayEl.value = '';
      rawEl.value = '';
      setInvalid('Vui lòng nhập số tiền');
      return;
    }

    const n = parseInt(digits, 10);
    rawEl.value = String(n);

    // Format
    const formatted = formatVND(n);
    displayEl.value = formatted;

    // Validate
    const v = validate(n);
    if (v.ok) setValid();
    else setInvalid(v.msg);

    // Phục hồi caret theo số digit bên trái
    let pos = 0, digitSeen = 0;
    while (pos < formatted.length && digitSeen < leftDigitsCount) {
      if (/\d/.test(formatted[pos])) digitSeen++;
      pos++;
    }
    displayEl.setSelectionRange(pos, pos);
  }

  // Khi gõ
  displayEl.addEventListener('input', applyFormatKeepCaret);

  // Chặn ký tự không phải số (vẫn cho phép Ctrl/Command keys)
  displayEl.addEventListener('keydown', (e) => {
    const allowKeys = [
      'Backspace','Delete','ArrowLeft','ArrowRight','Home','End','Tab','Enter'
    ];
    if (allowKeys.includes(e.key)) return;
    if (e.ctrlKey || e.metaKey) return;

    // chỉ cho nhập số
    if (!/^\d$/.test(e.key)) e.preventDefault();
  });

  // Paste: chỉ lấy số
  displayEl.addEventListener('paste', (e) => {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text');
    const digits = onlyDigits(text);
    if (!digits) return;
    displayEl.value = digits; // tạm
    applyFormatKeepCaret();
  });

  // Blur: nếu muốn auto làm tròn về bội STEP gần nhất
  displayEl.addEventListener('blur', () => {
    const digits = onlyDigits(displayEl.value);
    if (!digits) return;

    let n = parseInt(digits, 10);

    // làm tròn về bội STEP gần nhất (bạn muốn luôn làm tròn xuống thì đổi Math.round -> Math.floor)
    n = Math.round(n / STEP) * STEP;

    rawEl.value = String(n);
    displayEl.value = formatVND(n);

    const v = validate(n);
    if (v.ok) setValid();
    else setInvalid(v.msg);
  });

  // Init
  setInvalid('Vui lòng nhập số tiền');
})();
</script>


@endsection