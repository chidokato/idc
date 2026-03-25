@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  .select2-selection--multiple{ height:41px }
  .select2-search__field{ height:30px }
  .s2-user{ display:flex; gap:10px; }
  .s2-user__name{
    font-weight: 600;
    line-height: 1.1;
  }
  .s2-user__pos{
    font-size: 12px;
    opacity: .7;
    margin-top: 2px;
    line-height: 1.1;
  }

  .select2-container--default .select2-selection--multiple {
      min-height: 38px;
      border: 1px solid #ced4da;
      border-radius: 4px;
  }

  .select2-results__option {
      padding: 8px 12px;
  }
</style>
@endsection

@section('body') @endsection

@section('content')
<?php $rank = (int)(auth()->user()->rank ?? 0); ?>
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
      <div class="col-sm-auto d-none d-sm-block">
        <button type="button" class="btn btn-success js-export-excel" data-table="#taskTable" data-filename="tasks_{{ date('Ymd_His') }}.xlsx"> Xuất Excel</button>
      </div>
      <div class="col-sm-auto d-none d-sm-block">
        @if($rank === 1)
        <div id="addtask" data-toggle="popover-dark">
          <a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#newProjectModal">
            <i class="tio-add mr-1"></i> New project
          </a>
        </div>
        @endif
      </div>
    </div>
    <!-- End Row -->
  </div>

  <div class="card overflow-hidden mb-3 mb-lg-5">
    <!-- <button type="button" class="btn-search-mobi" data-search-toggle>Lọc tìm</button> -->
    <!-- <div class="search-overlay" onclick="toggleSearch()"></div> -->
    <div class="card-header search-mobi">
      <div class="row align-items-sm-center flex-grow-1">
        <div class="col-sm mb-2 mb-sm-0">
          <form id="filterForm" method="GET" action="{{ url()->current() }}">
            <div class="row" id="filterBar">
              
              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="report_id" class="form-control">
                  <option value="">-- Thời gian --</option>
                  @foreach($reports as $val)
                    <option value="{{ $val->id }}" {{ (string)$selectedReportId === (string)$val->id ? 'selected' : '' }}>
                      {{ \Carbon\Carbon::parse($val->time_start)->format('d') }} - {{ \Carbon\Carbon::parse($val->time_end)->format('d') }} _ Th {{ \Carbon\Carbon::parse($val->time_start)->format('m') }} {{ \Carbon\Carbon::parse($val->time_start)->format('Y') }}
                    </option>
                  @endforeach
                </select>
                </div>
              </div>

              <div class="col-sm-6 col-md-6">
                    <div class="form-group">
                        <select name="handler_ids[]" class="form-control yourname2" multiple>
                            @foreach($users as $us)
                                <option value="{{ $us->id }}"
                                    data-department="{{ $us->department?->name }}"
                                    {{ in_array($us->id, (array) request('handler_ids', [])) ? 'selected' : '' }}>
                                    {{ $us->yourname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="approved" class="form-control select2">
                    <option value="">-- Duyệt ??</option>
                    <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>
                        Đã duyệt
                    </option>
                    <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>
                        Chưa duyệt
                    </option>
                    
                  </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="paid" class="form-control select2">
                  <option value="">-- Đóng tiền ??</option>
                  <option value="1" {{ request('paid') === '1' ? 'selected' : '' }}>
                      Đã đóng
                  </option>
                  <option value="0" {{ request('paid') === '0' ? 'selected' : '' }}>
                      Chưa đóng
                  </option>
                </select>
                </div>
              </div>

              

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="post_id" class="form-control select2">
                  <option value="">-- Dự án --</option>
                  @foreach($posts as $p)
                  <option value="{{ $p->id }}" {{ request('post_id') == $p->id ? 'selected' : '' }}>
                      {{ $p->name }}
                  </option>
                  @endforeach
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="department_id" class="form-control select2">
                  <option value="">-- Phòng/nhóm --</option>
                  {!! $departmentOptions !!}
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="channel_id" class="form-control select2">
                  <option value="">-- Kênh ??</option>
                  {!! $channelsOptions !!}
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="outstanding" class="form-control select2">
                  <option value="">-- Đối soát ??</option>
                  <option {{ request('outstanding') === '1' ? 'selected' : '' }} value="1">-- Đóng thêm ??</option>
                  <option {{ request('outstanding') === '0' ? 'selected' : '' }} value="0">-- Trả lại ??</option>

                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <select name="settled" class="form-control select2">
                  <option value="">-- Tất toán ??</option>
                  <option value="1" {{ request('settled') === '1' ? 'selected' : '' }}>
                      Đã tất toán
                  </option>
                  <option value="0" {{ request('settled') === '0' ? 'selected' : '' }}>
                      Chưa tất toán
                  </option>
                </select>
                </div>
              </div>

              <div class="col-sm-2 col-md-2">
                <div class="form-group">
                  <button type="submit" class="btn btn-primary" id="btnSearch">Lọc</button>
                  <a href="{{ url()->current() }}" class="btn btn-warning" id="btnReset">Reset</a>
                </div>
              </div>

            </div>
          </form>
        </div>
        
      </div>
      <!-- End Row -->
    </div>


  <!-- Header -->

  <!-- End Header -->
  <!-- Table -->
  <div class="table-responsive datatable-custom">
  <table id="taskTable" class="table table-lg table-thead-bordered table-nowrap table-align-middle card-table">
    <thead class="thead-light">
      <tr>
        <th>Duyệt</th>
        <th class="text-center">Họ & Tên</th>
        <th class="text-center">Phòng / nhóm</th>
        <th class="text-center">Dự án</th>
        <th class="text-center">Kênh</th>
        <th class="text-center"></th>
        <th class="text-right">Tổng tiền</th>
        <th class="text-right">Tiền nộp</th>
        <th class="text-center">Đóng tiền</th>
        <th>Thực tế</th>
        <th class="text-right">Trả lại</th>
        <th class="text-right">Đóng thêm</th>
        <th class="text-center">Tất toán</th>
        <th>Ghi chú</th>
        <th colspan="2"></th>
      </tr>

      <tr id="sumRow" class="font-weight-bold bg-light" style="{{ $tasks->count() ? '' : 'display:none' }}">
        <td colspan="6"></td>
        <td class="text-right money" id="sumTotalText">{{ number_format($sumTotal, 0, ',', '.') }}</td>
        <td class="text-right money" id="sumPaidText">{{ number_format($sumPaid, 0, ',', '.') }}</td>
        <td class="text-center money" id="">{{ number_format($sum_expected, 0, ',', '.') }}</td>
        <td class="money" id="">{{ number_format($sum_actual_costs, 0, ',', '.') }}</td>
        <td class="text-right money" id="">{{ number_format($sum_refund_money, 0, ',', '.') }}</td>
        <td class="text-right money" id="">{{ number_format($sum_extra_money, 0, ',', '.') }}</td>
        <td colspan="4"></td>
      </tr>
    </thead>
    @php
      $canBulkEdit = auth()->check() && in_array(auth()->user()->rank, [1,2]);
    @endphp
    <tbody id="taskTableBody">
      @include('account.task.partials._rows', ['tasks' => $tasks])
    </tbody>
  </table>
</div>

  </div>
</div>

<div class="modal fade" id="invoiceReceiptModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <!-- Header -->
          <div class="modal-header">
            <h4 id="editUserModalTitle" class="modal-title">Chi tiết</h4>

            <button type="button" class="btn btn-icon btn-sm btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
              <i class="tio-clear tio-lg"></i>
            </button>
          </div>
          <!-- End Header -->
      <div class="modal-body">
        <input type="hidden" id="modal_task_id">

        <div class="form-group">
          <label>Số tiền</label>
          <input type="text" class="form-control actual-cost-input" id="modal_expected_costs" placeholder="Số tiền">
        </div>

        <div class="form-group">
          <label>Days</label>
          <input type="number" class="form-control" id="modal_days">
        </div>

        <div class="form-group">
          <label>Dự án</label>
          <select id="duan" class="form-control select2">
              @foreach($posts as $p)
                <option value="{{ $p->id }}" {{ request('post_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->name }}
                </option>
            @endforeach
          </select>
          <!-- <input type="text" class="form-control" id="duan" > -->
        </div>

        <div class="form-group">
          <label>Rate (%)</label>
          <input type="number" class="form-control" id="modal_rate" min="0" max="100">
        </div>

        <div class="form-group">
          <label>Ngày tạo</label>
          <input type="text" class="form-control" id="modal_date">
        </div>

        <!-- <div class="form-group">
          <label>KPI</label>
          <input type="text" class="form-control" id="modal_kpi">
        </div>

        <div class="form-group">
          <label>Content</label>
          <textarea class="form-control" id="modal_content" rows="3"></textarea>
        </div> -->

        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary ml-2" id="btnSaveTaskModal">Lưu</button>
        </div>
      </div>

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
                <select name="post_id" class="form-control select2">
                  <option value="">-- Dự án --</option>
                  @foreach($posts as $p)
                  <option value="{{ $p->id }}" {{ request('post_id') == $p->id ? 'selected' : '' }}>
                      {{ $p->name }}
                  </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="input-label">Kênh chạy</label>
                <select name="channel_id" required class="custom-select select2">
                  <option value="">...</option>
                  {!! $channelsOptions !!}
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
                <i class="tio-save"></i> Thêm mới
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
<script src="admin_asset/select2/js/select2.min.js"></script>
<script src="admin_asset/select2/js/select2-searchInputPlaceholder.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script src="account/js/account.js?v={{ filemtime(public_path('account/js/account.js')) }}"></script>

<script>
$(document).ready(function () {
    $('.yourname2').select2({
        width: '100%',
        placeholder: "Tìm theo tên",
        allowClear: true,
        templateResult: function (data) {

            if (!data.id) return data.text;

            let department = $(data.element).data('department') ?? '';

            return $(`
                <div style="position:relative; width:100%;">
                    <span>${data.text}</span>
                    <span style="
                        position:absolute;
                        right:10px;
                        top:0;
                        font-size:12px;
                        color:#999;">
                        ${department}
                    </span>
                </div>
            `);
        }
    });

});
</script>

<script>
$('#btnSaveTaskModal').on('click', function () {
  const id = $('#modal_task_id').val();

  // const expectedNum = toNumber($('#modal_expected_costs').val());
  const days = parseInt($('#modal_days').val(), 10) || 0;
  const rate = parseInt($('#modal_rate').val(), 10) || 0;
  // const kpi = $('#modal_kpi').val() || '';
  // const content = $('#modal_content').val() || '';
  const post_id = parseInt($('#duan').val(), 10) || null;

  $.ajax({
    url: "{{ url('account/tasks') }}/" + id,
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      // expected_costs: expectedNum,
      days: days,
      rate: rate,
      post_id: post_id
      // kpi: kpi,
      // content: content
    },
    success: function (res) {
      if (!res?.ok) {
        alert('Lưu thất bại');
        return;
      }

      // Update lại UI theo dữ liệu server trả về (an toàn nhất)
      const t = res.task;
      const $row = $('#row-' + t.id);

      // $row.find('.expected-cost-input').val(formatVn(t.expected_costs));
      $row.find('.rate-input').val(parseInt(t.rate, 10) || 0);
      // $row.find('.task-kpi').val(t.kpi ?? '');

      // $row.find('td.ghichu').attr('title', t.content ?? '');
      // $row.find('td.ghichu .text-truncate-set').text(t.content ?? '');
      // $row.find('td.ghichu .tooltip').text(t.content ?? '');

      $row.find('.total-cost-cell').data('days', t.days).attr('data-days', t.days);
      $row.find('.total-cost-text')
        .text(formatVn(t.total_costs))
        .attr('title', `${formatVn(t.expected_costs)}đ * ${t.days} ngày`);

      if (t.post_id) {
        $row.find('.duan')
          .data('duan', t.post_id)
          .attr('data-duan', t.post_id)
          .text(t.post_name || '');
      }


      $('#invoiceReceiptModal').modal('hide');
      window.location.reload();
    },
    error: function (xhr) {
      // Laravel validation errors
      if (xhr.status === 422) {
        const errors = xhr.responseJSON?.errors || {};
        alert(Object.values(errors).flat().join('\n'));
        return;
      }
      alert('Có lỗi khi lưu, vui lòng thử lại.');
    }
  });
});

</script>

<script>
$(function () {
  $('#btnSaveTaskModal').off('click').on('click', function () {
    const id = $('#modal_task_id').val();
    const expectedCosts = parseInt((typeof vnMoneyToDigits === 'function' ? vnMoneyToDigits($('#modal_expected_costs').val()) : ($('#modal_expected_costs').val() || '').replace(/[^\d]/g, '')), 10) || 0;
    const days = parseInt($('#modal_days').val(), 10) || 0;
    const rate = parseInt($('#modal_rate').val(), 10) || 0;
    const postIdRaw = $('#duan').val();
    const postId = postIdRaw ? parseInt(postIdRaw, 10) : null;

    $.ajax({
      url: "{{ url('account/tasks') }}/" + id,
      method: 'PUT',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        expected_costs: expectedCosts,
        days: days,
        rate: rate,
        post_id: postId
      },
      success: function (res) {
        if (!res?.ok) {
          alert('Luu that bai');
          return;
        }

        const t = res.task || {};
        const $row = $('#row-' + t.id);
        const rateValue = parseInt(t.rate, 10) || 0;
        const totalCosts = Number(t.total_costs || 0);
        const paidTotal = Number(t.paid_total || 0);

        $row.find('.total-cost-cell')
          .data('days', t.days)
          .attr('data-days', t.days)
          .text(t.days);

        $row.find('.js-row-total').text(formatVn(totalCosts));

        $row.find('.hold-badge')
          .attr('data-rate', rateValue)
          .attr('data-original-title', rateValue + '%')
          .text(formatVn(paidTotal));

        $row.find('.js-post-name').text(t.post_name || '');

        $row.find('.btn-edit-task')
          .attr('data-post-id', t.post_id || '')
          .attr('data-post-name', t.post_name || '')
          .attr('data-days', t.days)
          .attr('data-rate', rateValue);

        $('#invoiceReceiptModal').modal('hide');
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          const errors = xhr.responseJSON?.errors || {};
          alert(Object.values(errors).flat().join('\n'));
          return;
        }

        alert(xhr.responseJSON?.message || 'Co loi khi luu, vui long thu lai.');
      }
    });
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('click', function (event) {
    const editBtn = event.target.closest('.btn-edit-task');

    if (!editBtn) {
      return;
    }

    if (parseInt(editBtn.getAttribute('data-paid') || '0', 10) === 1) {
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();
      alert('Task da dong tien, khong the sua.');
    }
  }, true);

  const saveBtn = document.getElementById('btnSaveTaskModal');

  if (!saveBtn) {
    return;
  }

  saveBtn.addEventListener('click', function (event) {
    event.preventDefault();
    event.stopImmediatePropagation();

    const id = $('#modal_task_id').val();
    const expectedCosts = parseInt((typeof vnMoneyToDigits === 'function' ? vnMoneyToDigits($('#modal_expected_costs').val()) : ($('#modal_expected_costs').val() || '').replace(/[^\d]/g, '')), 10) || 0;
    const days = parseInt($('#modal_days').val(), 10) || 0;
    const rate = parseInt($('#modal_rate').val(), 10) || 0;
    const postIdRaw = $('#duan').val();
    const postId = postIdRaw ? parseInt(postIdRaw, 10) : null;
    const activeEditBtn = document.querySelector('.btn-edit-task[data-id="' + id + '"]');

    if (activeEditBtn && parseInt(activeEditBtn.getAttribute('data-paid') || '0', 10) === 1) {
      alert('Task da dong tien, khong the sua.');
      return;
    }

    $.ajax({
      url: "{{ url('account/tasks') }}/" + id,
      method: 'PUT',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        expected_costs: expectedCosts,
        days: days,
        rate: rate,
        post_id: postId
      },
      success: function (res) {
        if (!res?.ok) {
          alert('Luu that bai');
          return;
        }

        $('#invoiceReceiptModal').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();

        setTimeout(function () {
          window.location.reload();
        }, 150);
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          const directMessage = xhr.responseJSON?.message;
          if (directMessage) {
            alert(directMessage);
            return;
          }

          const errors = xhr.responseJSON?.errors || {};
          alert(Object.values(errors).flat().join('\n'));
          return;
        }

        alert(xhr.responseJSON?.message || 'Co loi khi luu, vui long thu lai.');
      }
    });
  }, true);
});
</script>
@endsection
