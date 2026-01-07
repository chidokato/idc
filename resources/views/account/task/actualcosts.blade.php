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
    </td>

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
    </td>

    <td class="text-end">
      <span class="js-actual-diff">
        {{ ((float)$task->actual_costs > 0)
          ? number_format(
              round((float)$task->actual_costs * (1 - ((float)($task->rate ?? 0) / 100))),
              0, ',', '.'
            )
          : '' }}
      </span>
    </td>

    <td>
      <div class="note" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $task->content ?? '' }}">
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
function vnMoneyToNumber(str) {
  return (str || '').toString().replace(/[^\d]/g, ''); // bỏ hết không phải số
}
function formatVnMoney(rawDigits) {
  rawDigits = (rawDigits || '').replace(/[^\d]/g, '');
  if (!rawDigits) return '';
  return rawDigits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function saveActualCosts($input) {
  const url = $input.data('url');
  const taskId = $input.data('task-id');

  const raw = vnMoneyToNumber($input.val()); // "1234567"
  const numberVal = raw ? Number(raw) : 0;

  const last = Number($input.data('last') || 0);
  if (numberVal === last) return; // không đổi thì khỏi gọi

  $.ajax({
    url: url,
    type: 'POST',
    data: {
      _token: $('meta[name="csrf-token"]').attr('content'),
      actual_costs: numberVal
    },
    success: function(res) {
      if (!res.ok) {
        showToast?.('error', res.message || 'Lỗi cập nhật');
        return;
      }

      const actual = Number(res.task.actual_costs || 0); // tiền thực tế
      $input.val(formatVnMoney(String(Math.trunc(actual))));
      $input.data('last', actual);

      // ===== update cột chênh lệch =====
      const expected = Number($input.data('expected') || 0); // tiền dự kiến
      const days = Number($input.data('days') || 0); // số ngày
      const rate = Number($input.data('rate') || 0); // tỷ lệ hỗ trợ

      const diff = actual > 0 ? (actual * (1 - rate/100)) : null;

      const $diffEl = $input.closest('tr').find('.js-actual-diff');
      $diffEl.text(diff === null ? '' : formatVnMoney(String(Math.round(diff))));

      showToast?.('success', res.message || 'Đã lưu');
    }
    ,
    error: function(xhr) {
      const msg = xhr?.responseJSON?.message || 'Lỗi server';
      showToast?.('error', msg);

      // trả về giá trị cũ
      $input.val(formatVnMoney(String(last)));
    }
  });
}

// Chặn ký tự lạ (chỉ cho số)
$(document).on('keydown', '.actual-cost-input', function(e) {
  const allow = ['Backspace','Delete','Tab','Enter','Escape','ArrowLeft','ArrowRight','Home','End'];
  if ((e.ctrlKey || e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
  if (allow.includes(e.key)) return;
  if (/^\d$/.test(e.key)) return;

  e.preventDefault();
  // showToast?.('warning', 'Chỉ nhập số (tiền VNĐ)');
});

// Auto format khi gõ
$(document).on('input', '.actual-cost-input', function() {
  const oldPos = this.selectionStart || 0;
  const old = this.value;

  const raw = vnMoneyToNumber(old);
  const formatted = formatVnMoney(raw);

  this.value = formatted;

  const diff = formatted.length - old.length;
  const newPos = Math.max(0, oldPos + diff);
  this.setSelectionRange(newPos, newPos);
});

// Lưu khi blur
$(document).on('blur', '.actual-cost-input', function() {
  saveActualCosts($(this));
});

// Lưu khi Enter
$(document).on('keydown', '.actual-cost-input', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    $(this).blur(); // kích hoạt save
  }
});


</script>



@endsection