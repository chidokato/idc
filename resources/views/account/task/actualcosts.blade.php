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
  <form id="filterForm" method="GET" action="{{ url()->current() }}">
  <div class="row align-items-center flex-grow-1 g-2" id="filterBar">

    <div class="col-sm-3 col-md-3">
      <input type="text"
             name="name"
             class="form-control"
             placeholder="Mã NV / Họ tên"
             value="{{ request('name') }}">
    </div>

    <div class="col-sm-3 col-md-3">
      <select name="department_id" class="form-control">
        <option value="">-- Phòng/nhóm --</option>
        {!! $departmentOptions !!}
      </select>
    </div>

    <div class="col-sm-3 col-md-3">
      <select name="report_id" class="form-control">
        <option value="">-- Báo cáo --</option>
        @foreach($reports as $val)
          <option value="{{ $val->id }}" {{ (string)$selectedReportId === (string)$val->id ? 'selected' : '' }}>
            {{ $val->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-sm-2 col-md-2 d-flex gap-2">
      <button type="submit" class="btn btn-primary" id="btnSearch">Lọc</button>

      <a href="{{ url()->current() }}" class="btn btn-warning" id="btnReset">Reset</a>
    </div>

  </div>
</form>

</div>
  <!-- End Header -->
  <!-- Table -->
  <div class="table-responsive datatable-custom">
  <table id="taskTable" class="table table-lg table-thead-bordered table-nowrap table-align-middle card-table">
    <thead class="thead-light">
      <tr>
        <th></th>
        <th>Mã NV</th>
        <th>Họ & Tên</th>
        <th>Phòng / nhóm</th>
        <th>Dự án</th>
        <th>Kênh</th>
        <th class="text-end">Tổng tiền</th>
        <th class="text-end">Tiền nộp</th>
        <th>Thực tế</th>
        <th class="text-end">Tất toán</th>
        <th>Ghi chú</th>
        <th></th>
      </tr>

      <tr id="sumRow" class="font-weight-bold bg-light" style="{{ $tasks->count() ? '' : 'display:none' }}">
        <td colspan="6" class="text-end">Tổng:</td>
        <td class="text-end" id="sumTotalText">{{ number_format($sumTotal, 0, ',', '.') }}</td>
        <td></td>
        <td class="text-end" id="sumPaidText">{{ number_format($sumPaid, 0, ',', '.') }}</td>
        <td colspan="3"></td>
      </tr>
    </thead>

    <tbody id="taskTableBody">
      @include('account.task.partials._rows', ['tasks' => $tasks])
    </tbody>
  </table>
</div>

<div id="paginationWrap">
  @include('account.task.partials._pagination', ['tasks' => $tasks])
</div>
  <!-- End Table -->
  </div>
</div>

@endsection


@section('js')

<script>
/* =======================
   VN MONEY INPUT HELPERS
======================= */
function vnMoneyToDigits(str) {
  return (str || '').toString().replace(/[^\d]/g, '');
}
function formatVnMoneyDigits(digits) {
  digits = (digits || '').toString().replace(/[^\d]/g, '');
  if (!digits) return '';
  return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/* =======================
   AJAX SAVE
======================= */
function saveActualCosts($input) {
  const url = $input.data('url');
  if (!url) return;

  const token = $('meta[name="csrf-token"]').attr('content');

  const rawDigits = ($input[0].dataset.raw || vnMoneyToDigits($input.val()) || '');
  const numberVal = rawDigits ? parseInt(rawDigits, 10) : 0;

  const last = parseInt($input.data('last') || 0, 10);
  if (numberVal === last) return;

  $input.prop('disabled', true).addClass('is-loading');

  $.ajax({
    url: url,
    type: 'POST',
    data: { _token: token, actual_costs: numberVal },
    success: function(res) {
      if (!res || !res.ok) {
        showToast?.('error', res?.message || 'Lỗi cập nhật');

        // rollback
        $input.val(formatVnMoneyDigits(String(last)));
        $input[0].dataset.raw = String(last);
        return;
      }

      const actual = parseInt(res.task?.actual_costs || 0, 10);

      // update input + last
      $input.val(formatVnMoneyDigits(String(actual)));
      $input[0].dataset.raw = String(actual);
      $input.data('last', actual);

      // update diff từ server
      const $diffEl = $input.closest('tr').find('.js-actual-diff');
      $diffEl.text(res.task?.diff_formatted ?? '');
      $diffEl.toggleClass('text-danger', !!res.task?.is_danger);

      showToast?.('success', res.message || 'Đã lưu');
    },
    error: function(xhr) {
      const msg = xhr?.responseJSON?.message || 'Lỗi server';
      showToast?.('error', msg);

      // rollback
      $input.val(formatVnMoneyDigits(String(last)));
      $input[0].dataset.raw = String(last);
    },
    complete: function() {
      $input.prop('disabled', false).removeClass('is-loading');
    }
  });
}

/* =======================
   EVENTS
======================= */

// Chặn ký tự lạ
$(document).on('keydown', '.actual-cost-input', function(e) {
  const allow = ['Backspace','Delete','Tab','Enter','Escape','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home','End'];
  if ((e.ctrlKey || e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
  if (allow.includes(e.key)) return;
  if (/^\d$/.test(e.key)) return;
  e.preventDefault();
});

// Format khi gõ + lưu raw
$(document).on('input', '.actual-cost-input', function() {
  const el = this;
  const oldVal = el.value;
  const oldPos = el.selectionStart || 0;

  const digits = vnMoneyToDigits(oldVal);
  const newVal = formatVnMoneyDigits(digits);

  el.value = newVal;
  el.dataset.raw = digits;

  const diffLen = newVal.length - oldVal.length;
  const newPos = Math.max(0, oldPos + diffLen);
  try { el.setSelectionRange(newPos, newPos); } catch (e) {}
});

// Blur => save
$(document).on('blur', '.actual-cost-input', function() {
  const digits = vnMoneyToDigits(this.value);
  this.value = formatVnMoneyDigits(digits);
  this.dataset.raw = digits;

  saveActualCosts($(this));
});

// Enter => blur => save
$(document).on('keydown', '.actual-cost-input', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    $(this).blur();
  }
});

// Init format cho input có sẵn
$(function() {
  $('.actual-cost-input').each(function() {
    const digits = vnMoneyToDigits(this.value);
    this.value = formatVnMoneyDigits(digits);
    this.dataset.raw = digits;
  });
});
</script>




@endsection