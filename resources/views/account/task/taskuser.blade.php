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
  <div class="col-sm-3 col-md-3 mb-3 mb-sm-0">
    <select name="department_id" id="filterDepartment" class="form-control">
      <option value="">-- Chọn phòng ban --</option>
      {!! $departmentOptions !!}
    </select>
  </div>

  <div class="col-sm-3 col-md-3 mb-3 mb-sm-0">
    <select name="report_id" id="filterReport" class="form-control">
      <option value="">-- Chọn thời gian --</option>
      @foreach($reports as $report)
        <option value="{{ $report->id }}" {{ (int)($reportId ?? 0) === (int)$report->id ? 'selected' : '' }}>
          {{ $report->name }}
        </option>
      @endforeach
</select>
  </div>
</div>
  <!-- End Row -->
  </div>
  <!-- End Header -->
  <!-- Table -->
  <div class="table-responsive datatable-custom">
    <table id="taskTable" class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
      <thead class="thead-light"> <tr> <th class="table-column-pr-0"> <div class="custom-control custom-checkbox"> <input id="datatableCheckAll" type="checkbox" class="custom-control-input"> <label class="custom-control-label" for="datatableCheckAll"></label> </div> </th> <th>Mã NV</th> <th>Họ & Tên</th> <th>Phòng / nhóm</th> <th>Dự án</th> <th>Kênh</th> <th>Tổng tiền</th> <th>Hỗ trợ</th> <th>Tiền nộp</th> <th>Đóng tiền</th> <th>Ghi chú</th> </tr> </thead>

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
  const $tbody = $('#taskTableBody');
  let xhr = null;

  function loadTasks() {
    const department_id = $dept.val();
    const report_id = $report.val();

    if (xhr) xhr.abort();

    $tbody.html(`<tr><td colspan="11" class="text-center py-4 text-muted">Đang tải...</td></tr>`);

    xhr = $.ajax({
      url: "{{ route('tasks.user') }}",
      type: "GET",
      data: { department_id, report_id },
      success: function (res) {
        $tbody.html(res.html || '');
      },
      error: function (xhr) {
        if (xhr.statusText === 'abort') return;
        $tbody.html(`<tr><td colspan="11" class="text-center py-4 text-danger">Có lỗi xảy ra!</td></tr>`);
      }
    });
  }

  $dept.on('change', loadTasks);
  $report.on('change', loadTasks);
});
</script>



@endsection