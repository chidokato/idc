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
  <div class="row align-items-center" id="filterBar">

    <div class="col-sm-3 col-md-3">
      <input type="text"
             name="name"
             class="form-control"
             placeholder="Mã NV / Họ tên"
             value="{{ request('name') }}">
    </div>

    <div class="col-sm-3 col-md-3">
      <select name="department_id" class="form-control select2">
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

<div id="addtask" data-toggle="popover-dark">
  <a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#newProjectModal">
    <i class="tio-add mr-1"></i> New project
  </a>
</div>

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
        <th class="text-end" title="Nộp thêm/Trả lại">Nộp/Trả</th>
        <th>Ghi chú</th>
        <th></th>
      </tr>

      <tr id="sumRow" class="font-weight-bold bg-light" style="{{ $tasks->count() ? '' : 'display:none' }}">
        <td colspan="6" class="text-end">Tổng:</td>
        <td class="text-end" id="sumTotalText">{{ number_format($sumTotal, 0, ',', '.') }}</td>
        <td class="text-end" id="sumPaidText">{{ number_format($sumPaid, 0, ',', '.') }}</td>
        <td class="text-end" id="">{{ number_format($sumActual, 0, ',', '.') }}</td>
        <td colspan="3"></td>
      </tr>
    </thead>

    <tbody id="taskTableBody">
      @include('account.task.partials._rows', ['tasks' => $tasks])
    </tbody>
  </table>
</div>

  </div>
</div>

<div class="modal fade" id="newProjectModal" tabindex="-1" role="dialog" aria-labelledby="editCardModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h4 id="editCardModalTitle" class="modal-title">Thêm mới</h4>
        <button type="button" class="btn btn-icon btn-sm btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
          <i class="tio-clear tio-lg"></i>
        </button>
      </div>

      {{-- FORM --}}
      <form id="createTaskForm" method="POST" action="{{ route('account.task.store') }}">
        @csrf
        <input type="hidden" name="addreport_id" value="{{ (string)$selectedReportId }}">
        {{-- URL hiện tại (có cả filter + page) để redirect về đúng chỗ --}}
        <input type="hidden" name="redirect_url" value="{{ url()->full() }}">

        <div class="modal-body">
          <div class="row">

            <div class="col-sm-3">
              <div class="form-group">
                <label class="input-label">Họ tên nhân viên</label>
                <select name="user_id" required class="custom-select select2">
                  <option value="">...</option>
                  @foreach($users as $val)
                    <option value="{{ $val->id }}">{{ $val->yourname }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="input-label">Dự án</label>
                <select name="post_id" required class="custom-select select2">
                  <option value="">...</option>
                  @foreach($posts as $val)
                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="input-label">Kênh chạy</label>
                <select name="channel_id" required class="custom-select select2">
                  <option value="">...</option>
                  @foreach($channels as $val)
                    <option value="{{ $val->id }}" {{ $val->name == 'Facebook' ? 'selected' : '' }}>
                      {{ $val->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label for="expected_costs" class="input-label">Số tiền</label>
                <div class="input-group input-group-merge">
                  <input
                    type="text"
                    class="form-control actual-cost-input"
                    name="expected_costs"
                    id="expected_costs"
                    value="500000"
                    placeholder="Số tiền"
                  >
                </div>
              </div>
            </div>

          </div>

          <div class="d-flex align-items-center">
            <div class="ml-auto">
              <button type="submit" class="btn btn-primary" id="btnCreateTask">
                <i class="tio-save"></i> Lưu lại
              </button>
            </div>
          </div>
        </div>
      </form>
      {{-- END FORM --}}

    </div>
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

<!-- select2 multiple JavaScript -->
<script src="admin_asset/select2/js/select2.min.js"></script>
<script src="admin_asset/select2/js/select2-searchInputPlaceholder.js"></script>
<script type="text/javascript">
    $(document).ready(function() { $('.select2').select2({ searchInputPlaceholder: '...' }); });
</script>


<script>
  // Submit tạo task
  $(document).on('submit', '#createTaskForm', function(e) {
    e.preventDefault();

    const $form = $(this);
    const $btn  = $('#btnCreateTask');

    // đổi tiền VN sang digits trước khi gửi
    const digits = vnMoneyToDigits($('#expected_costs').val());
    $('#expected_costs').val(digits ? digits : '0');

    $btn.prop('disabled', true);

    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      data: $form.serialize(),
      dataType: 'json',
      success: function(res) {
        if (!res || !res.ok) {
          showToast?.('error', res?.message || 'Thêm mới thất bại');
          return;
        }

        // đóng modal (tuỳ bạn)
        $('#newProjectModal').modal('hide');

        // redirect về đúng trang hiện tại (giữ filter + page)
        window.location.href = res.redirect || "{{ url()->full() }}";
      },
      error: function(xhr) {
        const msg = xhr?.responseJSON?.message || 'Lỗi server';
        showToast?.('error', msg);

        // nếu muốn show lỗi validate chi tiết:
        // console.log(xhr?.responseJSON?.errors);
      },
      complete: function() {
        $btn.prop('disabled', false);
      }
    });
  });
</script>



@endsection