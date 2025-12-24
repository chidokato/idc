@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')
<link rel="stylesheet" href="account/css/custom.css">
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
            <li class="breadcrumb-item active" aria-current="page">Danh sách link đăng ký MKT</li>
          </ol>
        </nav>
        <h1 class="page-header-title">Danh sách link đăng ký MKT</h1>
      </div>
      
    </div>
    <!-- End Row -->
  </div>
  <div class="card">
  <!-- Header -->
  <div class="card-header">
    <div class="row align-items-center flex-grow-1" id="filterBar">
      <div class="col-sm-3 col-md-3 mb-sm-0">
        <select name="department_id" id="filterDepartment" class="form-control">
          <option value="">-- Chọn phòng ban --</option>
          {!! $departmentOptions !!}
        </select>
      </div>
      <div class="col-sm-3 col-md-3 mb-sm-0">
        <select name="report_id" id="filterReport" class="form-control">
          <option value="">-- Chọn thời gian --</option>
          @foreach($reports as $report)
            <option value="{{ $report->id }}" {{ (int)($reportId ?? 0) === (int)$report->id ? 'selected' : '' }}>
              {{ $report->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-sm-2 col-md-2 mb-sm-0">
        <select name="approved" id="filterApproved" class="form-control">
          <option value="">-- Duyệt ?? --</option>
          <option value="1">Đã duyệt</option>
          <option value="0">Không duyệt</option>
        </select>
      </div>
    </div>
    <!-- End Row -->
  </div>
  <!-- End Header -->
  <!-- Table -->
  <div class="table-responsive datatable-custom">
    <table id="taskTable" class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
      <thead class="thead-light"> 
        <tr> 
          <th></th>
          <th>Mã NV</th>
          <th>Họ & Tên</th> 
          <th>Phòng / nhóm</th> 
          <th>Dự án</th> 
          <th>Kênh</th> 
          <th>Tổng tiền</th> 
          <th>Hỗ trợ</th> 
          <th>Tiền nộp</th> 
          <th>Đóng tiền</th> 
          <th>Ghi chú</th> 
          <th></th>
        </tr>
        @if($tasks->count())
        <tr class="font-weight-bold bg-light">
          <td colspan="6" class="text-end">Tổng:</td>
          <td class="text-end" id="sumTotalText">{{ number_format($sumTotal, 0, ',', '.') }}</td>
          <td></td>
          <td class="text-end" id="sumPaidText">{{ number_format($sumPaid, 0, ',', '.') }}</td>
          <td colspan="3"></td>
        </tr>
        @endif
      </thead>
      <tbody id="taskTableBody">
        @include('account.task.partials.task_rows', ['tasks' => $tasks])
      </tbody>
    </table>
  </div>
  <!-- End Table -->
  </div>
</div>

@endsection


@section('js')
<script>
  (function () {
    const input = document.getElementById('quickSearch');
    const table = document.getElementById('taskTable');
    if (!input || !table) return;

    const tbody = table.tBodies[0];
    const noResultRow = document.getElementById('noResultRow');

    // Lấy tất cả row TR (trừ noResultRow)
    const rows = Array.from(tbody.querySelectorAll('tr'))
      .filter(tr => tr.id !== 'noResultRow');

    function normalize(text) {
      return (text || '')
        .toString()
        .toLowerCase()
        .normalize('NFD')                 // bỏ dấu tiếng Việt
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, ' ')
        .trim();
    }

    function filterRows() {
      const q = normalize(input.value);
      let visibleCount = 0;

      rows.forEach(tr => {
        // textContent lấy toàn bộ text trong dòng (mã NV, họ tên, phòng, dự án, ghi chú...)
        const rowText = normalize(tr.textContent);
        const match = rowText.includes(q);

        tr.style.display = match ? '' : 'none';
        if (match) visibleCount++;
      });

      if (noResultRow) {
        noResultRow.style.display = (visibleCount === 0) ? '' : 'none';
      }
    }

    // debounce để gõ mượt
    let t = null;
    input.addEventListener('input', function () {
      clearTimeout(t);
      t = setTimeout(filterRows, 150);
    });

    // lọc ngay lần đầu (nếu input có sẵn)
    filterRows();
  })();
</script>

<script>
$(function () {
  const $dept = $('#filterDepartment');
  const $report = $('#filterReport');
  const $approved = $('#filterApproved');
  const $tbody = $('#taskTableBody');

  // nơi hiển thị tổng (nếu không có thì sẽ tự bỏ qua)
  const $sumTotalText = $('#sumTotalText');
  const $sumPaidText  = $('#sumPaidText');


  let xhr = null;

  function formatVND(n) {
    n = Number(n || 0);
    return n.toLocaleString('vi-VN');
  }

  function loadTasks() {
    const department_id = $dept.val();
    const report_id = $report.val();
    const approved = $approved.val();

    if (xhr) xhr.abort();

    $tbody.html(`<tr><td colspan="11" class="text-center py-4 text-muted">Đang tải...</td></tr>`);

    xhr = $.ajax({
      url: "{{ route('tasks.user') }}",
      type: "GET",
      data: { department_id, report_id, approved  },
      dataType: "json",
      success: function (res) {
        $tbody.html(res.html || '');

        // cập nhật tổng (nếu backend có trả về sumTotal/sumPaid)
        if ($sumTotalText.length) $sumTotalText.text(formatVND(res.sumTotal));
        if ($sumPaidText.length)  $sumPaidText.text(formatVND(res.sumPaid));
      },
      error: function (xhr) {
        if (xhr.statusText === 'abort') return;
        $tbody.html(`<tr><td colspan="11" class="text-center py-4 text-danger">Có lỗi xảy ra!</td></tr>`);
      }
    });
  }

  $dept.on('change', loadTasks);
  $report.on('change', loadTasks);
  $approved.on('change', loadTasks);
});
</script>


<script>
document.addEventListener('change', function (e) {
  const el = e.target;
  if (!el.classList.contains('active-toggle')) return;

  const url = el.dataset.url;
  const paid = el.checked ? 1 : 0;
  const oldState = !el.checked;

  const rank = parseInt(el.dataset.rank || '0', 10);
  const isMine = parseInt(el.dataset.mine || '0', 10) === 1;
  const sameDept = parseInt(el.dataset.samedept || '0', 10) === 1;

  // ===== RULE UI =====
  // rank2: chỉ HOLD nếu cùng department_id, không RELEASE
  if (rank === 2) {
    if (paid === 0) { el.checked = true; showCenterError('Rank 2 không được hủy giữ tiền (RELEASE).'); return; }
    if (!sameDept)  { el.checked = false; showCenterError('Rank 2 chỉ được giữ tiền cho tác vụ cùng phòng ban (department).'); return; }
  }

  // rank3: chỉ HOLD task của mình, không RELEASE
  if (rank === 3) {
    if (paid === 0) { el.checked = true; showCenterError('Rank 3 không được hủy giữ tiền (RELEASE).'); return; }
    if (!isMine)    { el.checked = false; showCenterError('Rank 3 chỉ được giữ tiền (HOLD) tác vụ của mình.'); return; }
  }

  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
    },
    body: JSON.stringify({ paid })
  })
  .then(async (res) => {
  const data = await res.json().catch(() => ({}));
  if (!res.ok || data.status === false) throw new Error(data.message || 'Có lỗi xảy ra');

  showToast('success', data.message || 'Thành công');

  // update badge trong cùng dòng (nếu có)
  const tr = el.closest('tr');
  const badgeCell = tr?.querySelector('.hold-badge');
  if (badgeCell) {
    badgeCell.innerHTML = paid
      ? `<span class="badge badge-soft-success">Đã đóng</span>`
      : `<span class="badge badge-soft-warning">Chưa đóng</span>`;
  }

  // ✅ update số dư trên menu
  if (data.wallet && typeof data.wallet.balance !== 'undefined') {
    const menuBalanceEl = document.getElementById('menuBalance');
    if (menuBalanceEl) {
      const num = Number(data.wallet.balance || 0);
      menuBalanceEl.textContent = num.toLocaleString('vi-VN');
    }

    // (tuỳ chọn) nếu có hiển thị held:
    const menuHeldEl = document.getElementById('menuHeld');
    if (menuHeldEl) menuHeldEl.textContent = Number(data.wallet.held_balance||0).toLocaleString('vi-VN');
  }
})

  .catch(err => {
    el.checked = oldState;
    showCenterError(err.message || 'Có lỗi xảy ra, vui lòng thử lại!');
  });
});
</script>



@endsection