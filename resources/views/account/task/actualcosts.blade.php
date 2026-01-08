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
    <div class="row align-items-center flex-grow-1" id="filterBar">
      <div class="col-sm-2 col-md-2 mb-sm-0">
        <input type="text" name="name" class="form-control" placeholder="...">
      </div>

    </div>
    <!-- End Row -->
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
          <th>Tổng tiền</th> 
          <th>Tiền nộp</th> 
          <th>Thực tế</th> 
          <th>Tất toán</th>
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
        @forelse($tasks as $task)
      @php
        // dùng lại cách ép số giống controller (nhanh gọn ở đây)
        $cost = (float) preg_replace('/[^\d\-]/', '', (string)($task->expected_costs ?? 0));
        $days = (float) preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', (string)($task->days ?? 0)));
        $rate = (float) preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', (string)($task->rate ?? 0)));
        $rowTotal = $cost * $days;
        $rowPaid  = $rowTotal * (1 - $rate/100);
      @endphp
  <tr>
    <td>
      @if($task->approved == 1)
          <span class="badge btn-success">Duyệt</span>
      @else
          <span class="badge btn-danger">Không</span>
      @endif
    </td>
    <td>{{ $task->handler?->employee_code }}</td>
    <td>{{ $task->handler?->yourname }}</td>
    <td>{{ $task->department?->name }}</td>
    <td>{{ $task->Post?->name }}</td>
    <td>{{ $task->channel?->name ?? $task->channel ?? '' }}</td>

    <td class="text-end">
      {{ number_format((float)($task->expected_costs * $task->days), 0, ',', '.') }}
    </td>

    <td class="text-end">
      @if(($task->paid ?? 0) == 1)
      <div class="note text-success" data-toggle="tooltip" data-placement="left" title="" data-original-title="{{ (int) $task->rate }}%">
        {{ number_format((float)(($task->expected_costs * $task->days) * (1 - $task->rate/100)), 0, ',', '.') }}
      </div>
      @else
      <div class="note text-danger" data-toggle="tooltip" data-placement="left" title="" data-original-title="{{ (int) $task->rate }}%">
        {{ number_format((float)(($task->expected_costs * $task->days) * (1 - $task->rate/100)), 0, ',', '.') }}
      </div>
      @endif
    </td> <!-- số tiền hold paid=1 -> đã hold, paid=0 hoặc null -> chưa hold -->

    <td>
      <input
        style="width: 120px;"
        class="form-control actual-cost-input"
        type="text"
        name="actual_costs"
        value="{{ number_format((float)($task->actual_costs), 0, ',', '.') }}"
        data-task-id="{{ $task->id }}"
        data-last="{{ (float)($task->actual_costs ?? 0) }}"
        data-expected="{{ (float)($task->expected_costs ?? 0) }}"
        data-days="{{ (float)($task->days ?? 0) }}"
        data-rate="{{ (float)($task->rate ?? 0) }}"
        data-url="{{ route('tasks.ajaxUpdateActualCosts', $task) }}"
        placeholder="Nhập..."
      >
    </td> <!-- chi phí thực tế -->

    @php
      $expected = (float)($task->expected_costs ?? 0);
      $days     = (float)($task->days ?? 0);
      $rate     = (float)($task->rate ?? 0);
      $total    = $expected * $days;
      $actual   = (float)($task->actual_costs ?? 0);

      if ($actual <= $total) {
        $diff = ($total - $actual) * (1 - $rate/100);
      } else {
        $diff = $total - $actual;
      }

      $diff = (int) round($diff);
    @endphp

    <td class="text-end">
      <span class="js-actual-diff">{{ number_format($diff, 0, ',', '.') }}</span>
    </td>
 <!-- số tiền thực tế người dùng phải trả -->

    <td>
      <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$task->id}}">
        <input name="settlement" type="checkbox" class="toggle-switch-input" id="stocksCheckbox{{$task->id}}">
        <span class="toggle-switch-label">
          <span class="toggle-switch-indicator"></span>
        </span>
      </label>
    </td>

    <td>
      <div style="width: 200px;" class="note" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $task->content ?? '' }}">
        {{ $task->content ?? '' }}
      </div>
    </td>
  </tr>
  @empty
    <tr>
      <td colspan="11" class="text-center text-muted py-4">Không có dữ liệu phù hợp</td>
    </tr>
  @endforelse
      </tbody>
    </table>
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

  // lấy số thật từ dataset.raw (đã set khi input)
  const rawDigits = ($input[0].dataset.raw || vnMoneyToDigits($input.val()) || '');
  const numberVal = rawDigits ? parseInt(rawDigits, 10) : 0;

  const last = parseInt($input.data('last') || 0, 10);
  if (numberVal === last) return; // không đổi -> khỏi save

  // UI state
  $input.prop('disabled', true).addClass('is-loading');

  $.ajax({
    url: url,
    type: 'POST',
    data: {
      _token: token,
      actual_costs: numberVal
    },
    success: function(res) {
      if (!res || !res.ok) {
        showToast?.('error', res?.message || 'Lỗi cập nhật');

        // rollback
        $input.val(formatVnMoneyDigits(String(last)));
        $input[0].dataset.raw = String(last);
        return;
      }

      // cập nhật actual (server trả về)
      const actual = parseInt(res.task?.actual_costs || 0, 10);
      $input.val(formatVnMoneyDigits(String(actual)));
      $input[0].dataset.raw = String(actual);
      $input.data('last', actual);

      // update cột chênh lệch từ server (đã tính + format)
      const diffFormatted = res.task?.diff_formatted ?? '';
      $input.closest('tr').find('.js-actual-diff').text(diffFormatted);

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

// 1) Chặn ký tự lạ khi gõ (chỉ cho digit + phím điều hướng + ctrl/cmd)
$(document).on('keydown', '.actual-cost-input', function(e) {
  const allow = [
    'Backspace','Delete','Tab','Enter','Escape',
    'ArrowLeft','ArrowRight','ArrowUp','ArrowDown',
    'Home','End'
  ];

  if ((e.ctrlKey || e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
  if (allow.includes(e.key)) return;
  if (/^\d$/.test(e.key)) return;

  e.preventDefault();
});

// 2) Format khi đang gõ + lưu raw vào dataset
$(document).on('input', '.actual-cost-input', function() {
  const el = this;

  const oldVal = el.value;
  const oldPos = el.selectionStart || 0;

  const digits = vnMoneyToDigits(oldVal);
  const newVal = formatVnMoneyDigits(digits);

  el.value = newVal;
  el.dataset.raw = digits;

  // giữ caret tương đối
  const diffLen = newVal.length - oldVal.length;
  const newPos = Math.max(0, oldPos + diffLen);
  try { el.setSelectionRange(newPos, newPos); } catch (e) {}
});

// 3) Blur: format lại + gọi save
$(document).on('blur', '.actual-cost-input', function() {
  const digits = vnMoneyToDigits(this.value);
  this.value = formatVnMoneyDigits(digits);
  this.dataset.raw = digits;

  saveActualCosts($(this));
});

// 4) Enter: trigger blur -> save
$(document).on('keydown', '.actual-cost-input', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    $(this).blur();
  }
});

// 5) Init format cho các input có sẵn value khi load
$(function() {
  $('.actual-cost-input').each(function() {
    const digits = vnMoneyToDigits(this.value);
    this.value = formatVnMoneyDigits(digits);
    this.dataset.raw = digits;
  });
});
</script>



@endsection