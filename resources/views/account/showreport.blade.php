@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('content')
<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="mb-4 flex space-between"><button type="button" onclick="window.location.href='{{route('report.index')}}'" class="btn btn-primary">Trở về trang trước</button> {{ $report->name }} ({{ \Carbon\Carbon::parse($report->time_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($report->time_end)->format('d/m/Y') }}) </h3>
                <hr>
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-2">
                        <select name="department_id" class="form-control select2">
                            <option value="">-- Sàn / Nhóm --</option>
                            {!! $departmentOptions !!}
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="post_id" class="form-control select2">
                            <option value="">-- Dự án --</option>
                            @foreach($posts as $p)
                                <option value="{{ $p->id }}" {{ request('post_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="channel_id" class="form-control select2">
                            <option value="">-- Kênh --</option>
                            {!! $channelsOptions !!}
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="approved" class="form-control select2">
                            <option value="" {{ request()->filled('approved') ? '' : 'selected' }}>
                                -- Duyệt ?? --
                            </option>
                            <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>
                                Đã duyệt
                            </option>
                            <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>
                                Chưa duyệt
                            </option>
                        </select>
                    </div>


                    <div class="col-md-2">
                        <button class="btn button-search form-control bg-success">Lọc</button>
                    </div>

                </form>

                @php
                      $canBulkEdit = auth()->check() && in_array(auth()->user()->rank, [1,2]);
                    @endphp

                <table class="table table-hover">
                    <thead class="thead">
                        <tr >
                            <th style="width:36px" class="text-center">
                              @if($canBulkEdit)
                                <input type="checkbox" id="check-all">
                              @endif
                            </th>
                            <th>Họ Tên</th>
                            <th>Sàn</th>
                            <th>Nhóm</th>
                            <th>Dự án</th>
                            <th class="text-center">Kênh</th>
                            <th>Chi phí</th>
                            <!-- <th>Số ngày</th> -->
                            <th class="text-end">Tổng tiền </th>
                            <th>Hỗ trợ</th>
                            <th>Ghi chú</th>
                            <th>KPI</th>
                            <!-- <th></th> -->
                            <th>Duyệt</th>
                            <th></th>
                        </tr>
                        
                    </thead>

                    

                    @if($canBulkEdit)
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-open-bulk-modal" disabled>
                            Sửa hàng loạt (<span id="bulk-count">0</span>)
                        </button>
<!-- 
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-clear-selected" disabled>
                            Bỏ chọn
                        </button> -->
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Sửa hàng loạt</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>

                          <div class="modal-body">

                            {{-- Expected costs --}}
                            <div class="border rounded p-2 mb-3">
                              <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="apply_expected">
                                <label class="form-check-label" for="apply_expected">Áp dụng Chi phí (expected_costs)</label>
                              </div>
                              <input type="text" class="form-control form-control-sm" id="bulk_expected" placeholder="VD: 1.500.000" disabled>
                            </div>

                            {{-- Rate --}}
                            <div class="border rounded p-2 mb-3">
                              <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="apply_rate">
                                <label class="form-check-label" for="apply_rate">Áp dụng Hỗ trợ (rate)</label>
                              </div>

                              <select class="form-select form-select-sm" id="bulk_rate" disabled>
                                @foreach(config('datas.rates') as $value => $label)
                                  <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                              </select>
                            </div>

                            {{-- Approved --}}
                            <div class="border rounded p-2">
                              <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="apply_approved">
                                <label class="form-check-label" for="apply_approved">Áp dụng Duyệt (approved)</label>
                              </div>

                              <select class="form-select form-select-sm" id="bulk_approved_action" disabled>
                                <option value="approve">Duyệt</option>
                                <option value="unapprove">Bỏ duyệt</option>
                              </select>
                            </div>

                          </div>

                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                            <button type="button" class="btn btn-primary btn-sm" id="btn-bulk-save">Lưu</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endif


                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end">{{ number_format($tongTien, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>   
                        </tr>
                        <?php
                            $task = $task->sortBy([
                                fn($a, $b) => strcmp($a->department?->hierarchy_levels['level2'] ?? '', $b->department?->hierarchy_levels['level2'] ?? ''),
                                fn($a, $b) => strcmp($a->department?->hierarchy_levels['level3'] ?? '', $b->department?->hierarchy_levels['level3'] ?? ''),
                            ]);
                        ?>
                        @foreach($task as $val)
                        <?php $levels = $val->department?->hierarchy_levels ?? []; ?>
                        <tr class="padding16" id="row-{{ $val->id }}">
                            <td class="text-center">
                              @if($canBulkEdit)
                                <input type="checkbox" class="row-check" value="{{ $val->id }}">
                              @endif
                            </td>
                            <td>{{ $val->handler?->yourname ?? '---' }}</td>
                            <td>{{ $levels['level2'] ?? '-' }}</td>
                            <td>{{ $levels['level3'] ?? '-' }}</td>
                            <td>{{ $val->Post?->name }}</td>
                            <td class="text-center">{{ $val->Channel?->name }}</td>
                            <td class="text-end"><input type="text" style="width: 80px" class="form-control form-select-sm expected-cost-input" value="{{ number_format($val->expected_costs, 0, ',', '.') }}" data-id="{{ $val->id }}">
                            </td>
                            <!-- <td>{{ $val->days }}</td> -->
                            <td class="text-end total-cost-cell"
                                data-days="{{ $val->days }}"
                                data-rate="{{ $val->rate }}"
                            >
                                <span class="total-cost-text">
                                    {{ number_format($val->total_costs ?? $val->days * $val->expected_costs, 0, ',', '.') }}
                                </span>
                                <span
                                    title="{{ number_format($val->expected_costs, 0, ',', '.') }}đ * {{ $val->days }} ngày"
                                    class="note"
                                >?</span>
                            </td>

                            <td>
                                <select name="rate" class="rate-select form-select form-select-sm" data-id="{{ $val->id }}">
                                    @foreach(config('datas.rates') as $value => $label)
                                        <option value="{{ $value }}" {{ $val->rate == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- <td>{{ number_format($val->support_money ?? 0, 0, ',', '.') }} đ</td> -->
                            <td class="ghichu" title="{{ $val->content }}">
                                <span class="tooltip-wrapper">
                                    <span class="text-truncate-set-1 text-truncate-set">
                                        {{ $val->content }}
                                    </span>
                                    <span class="tooltip">
                                        {{ $val->content }}
                                    </span>
                                </span>
                            </td>

                            <td>
                                <input type="text" class="task-kpi form-control form-select-sm" value="{{ $val->kpi ?? '' }}" data-id="{{ $val->id }}" placeholder="..." >
                            </td>
                            <!-- <td>
                                <form action="{{ route('account.tasks.delete', $val) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="del-db btn btn-danger p-1" data-id="{{ $val->id }}">Xóa</button>
                                </form>
                            </td> -->
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="active-toggle" data-id="{{ $val->id }}" {{ $val->approved ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td> @if($val->approved) <span class="badge bg-success">Đã duyệt</span> @else <span class="badge bg-warning">Chờ duyệt</span> @endif </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
$(document).ready(function() {
    $('.active-toggle').on('change', function() {
        let checkbox = $(this);
        let taskId = checkbox.data('id');
        let approved = checkbox.is(':checked'); // true/false

        $.ajax({
            url: 'account/task/toggle-approved/' + taskId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                approved: approved
            },
            success: function(res) {
                if(res.success) {
                    let badge = checkbox.closest('tr').find('td:last span');
                    if(res.approved) {
                        badge.removeClass('bg-warning').addClass('bg-success').text('Đã duyệt');
                    } else {
                        badge.removeClass('bg-success').addClass('bg-warning').text('Chờ duyệt');
                    }
                }
            },
            error: function(err) {
                alert('Cập nhật thất bại!');
                // revert checkbox
                checkbox.prop('checked', !approved);
            }
        });
    });
});
</script>

<script>
$(document).on('click', '.del-db', function (e) {
    e.preventDefault();

    let id = $(this).data('id');
    let row = $("#row-" + id);

    let approved = row.find('td:nth-child(5) span').hasClass('bg-success'); 

    if (approved) {
        Swal.fire('Không thể xóa!', 'Tác vụ đã được duyệt, không thể xóa.', 'warning');
        return;
    }

    let url = "{{ url('account/tasks/delete') }}/" + id;

    Swal.fire({
        title: 'Bạn có chắc muốn xóa?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Có',
        cancelButtonText: 'Không'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: url,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    if (res.status) {

                        row.fadeOut(300, function() {
                            $(this).remove();

                            // Cập nhật giao diện số liệu
                            $("#tongduan").text(res.stats.total_project + " dự án");
                            $("#tongtien").text(res.stats.total_expected + " đ");
                            $("#tongphainop").text(res.stats.total_pay + " đ");
                        });

                    } else {
                        Swal.fire('Lỗi!', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi!', 'Không thể kết nối server.', 'error');
                }
            });

        }
    });
});
</script>

<script>
$(document).on('change', '.rate-select', function () {
    let rate = $(this).val();
    let taskId = $(this).data('id');

    $.ajax({
        url: "{{ route('tasks.updateRate') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: taskId,
            rate: rate
        },
        success: function (res) {
            if (res.success) {
                showToast('success', 'Đã cập nhật rate ' + rate + '%');
            } else {
                showToast('warning', 'Cập nhật rate không thành công');
            }
        },
        error: function () {
            showToast('error', 'Lỗi khi cập nhật rate');
        }
    });
});
</script>

<script>
$(document).on('change', '.task-kpi', function () {
    let input = $(this);
    let kpi = input.val();
    let taskId = input.data('id');

    $.ajax({
        url: "{{ route('task.updateKpi') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            task_id: taskId,
            kpi: kpi
        },
        success: function (res) {
            if (res.status) {
                input.css('border', '1px solid #28a745');
            }
        },
        error: function () {
            alert('Lỗi khi lưu KPI');
            input.css('border', '1px solid red');
        }
    });
});
</script>

<script>
$(document).on('blur', '.expected-cost-input', function () {
    let input = $(this);
    let taskId = input.data('id');

    // bỏ dấu chấm
    let rawValue = input.val().replace(/\./g, '');

    if (rawValue === '' || isNaN(rawValue)) {
        alert('Số tiền không hợp lệ');
        return;
    }

    $.ajax({
        url: "{{ route('task.updateExpectedCost') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            task_id: taskId,
            expected_costs: rawValue
        },
        success: function (res) {
            if (res.status) {

                // format lại input
                input.val(res.expected_costs);

                let row = input.closest('tr');

                let expected = parseInt(res.raw_expected_costs); // server trả về
                let days = row.find('.total-cost-cell').data('days');
                let rate = row.find('.total-cost-cell').data('rate');

                let gross = expected * days;
                let net = Math.round(gross);

                // update số tiền
                row.find('.total-cost-text').text(
                    net.toLocaleString('vi-VN')
                );

                // update tooltip
                row.find('.note').attr(
                    'title',
                    expected.toLocaleString('vi-VN') + 'đ * ' + days + ' ngày'
                );
            }
        },
        error: function () {
            alert('Lỗi khi lưu chi phí');
            input.css('border', '1px solid red');
        }
    });
});
</script>

<script>
$(function () {
    const canBulkEdit = @json($canBulkEdit);

    function formatVn(n){
        n = parseInt(n || 0, 10);
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseVnMoney(str){
        if(!str) return 0;
        return parseInt(String(str).replace(/[^\d]/g,''), 10) || 0;
    }

    function selectedIds(){
        return $('.row-check:checked').map(function(){ return $(this).val(); }).get();
    }

    function refreshBulkButtons(){
        const count = selectedIds().length;
        $('#bulk-count').text(count);
        $('#btn-open-bulk-modal').prop('disabled', count === 0);
        $('#btn-clear-selected').prop('disabled', count === 0);
    }

    if(!canBulkEdit) return;

    // CSRF header
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    // select all
    $(document).on('change', '#check-all', function(){
        $('.row-check').prop('checked', this.checked);
        refreshBulkButtons();
    });

    // select single
    $(document).on('change', '.row-check', function(){
        const all = $('.row-check').length;
        const checked = $('.row-check:checked').length;
        $('#check-all').prop('checked', all > 0 && checked === all);
        refreshBulkButtons();
    });

    // clear
    $('#btn-clear-selected').on('click', function(){
        $('.row-check').prop('checked', false);
        $('#check-all').prop('checked', false);
        refreshBulkButtons();
    });

    // modal enable/disable fields
    $('#apply_expected').on('change', function(){ $('#bulk_expected').prop('disabled', !this.checked); });
    $('#apply_rate').on('change', function(){ $('#bulk_rate').prop('disabled', !this.checked); });
    $('#apply_approved').on('change', function(){ $('#bulk_approved_action').prop('disabled', !this.checked); });

    // open modal
    $('#btn-open-bulk-modal').on('click', function(){
        // reset modal state
        $('#apply_expected, #apply_rate, #apply_approved').prop('checked', false);
        $('#bulk_expected').val('').prop('disabled', true);
        $('#bulk_rate').prop('disabled', true);
        $('#bulk_approved_action').prop('disabled', true);

        const modalEl = document.getElementById('bulkEditModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // save bulk
    $('#btn-bulk-save').on('click', function(){
        const ids = selectedIds();
        if(ids.length === 0){
            showCenterError("Bạn chưa chọn dòng nào!");
            return;
        }

        const payload = {
            ids: ids,
            apply_expected: $('#apply_expected').is(':checked') ? 1 : 0,
            expected_costs: parseVnMoney($('#bulk_expected').val()),

            apply_rate: $('#apply_rate').is(':checked') ? 1 : 0,
            rate: $('#bulk_rate').val(),

            apply_approved: $('#apply_approved').is(':checked') ? 1 : 0,
            approved_action: $('#bulk_approved_action').val(),
        };

        // ít nhất phải tick 1 mục áp dụng
        if(!payload.apply_expected && !payload.apply_rate && !payload.apply_approved){
            showCenterError("Hãy chọn ít nhất 1 mục để áp dụng!");
            return;
        }

        $.ajax({
            url: "{{ route('account.tasks.bulkUpdate') }}",
            method: "POST",
            data: payload,
            success: function(res){
                showToast('success', res.message ?? "Đã thực hiện thành công");

                // Update UI từng row
                (res.rows || []).forEach(function(r){
                    const row = $('#row-' + r.id);

                    // expected_costs input
                    if(payload.apply_expected){
                        row.find('.expected-cost-input').val(formatVn(r.expected_costs));
                        row.find('.total-cost-text').text(formatVn(r.total_costs));
                        // cập nhật tooltip ghi chú nhân ngày nếu bạn muốn:
                        row.find('.total-cost-cell .note')
                          .attr('title', formatVn(r.expected_costs) + 'đ * ' + row.find('.total-cost-cell').data('days') + ' ngày');
                    }

                    // rate select
                    if(payload.apply_rate){
                        row.find('.rate-select').val(r.rate);
                    }

                    // approved switch + badge
                    if(payload.apply_approved){
                        row.find('.active-toggle').prop('checked', r.approved == 1);
                        const badgeCell = row.find('td').last(); // cột badge đang là td cuối
                        if(r.approved == 1){
                            badgeCell.html('<span class="badge bg-success">Đã duyệt</span>');
                        } else {
                            badgeCell.html('<span class="badge bg-warning">Chờ duyệt</span>');
                        }
                    }
                });

                // đóng modal
                bootstrap.Modal.getInstance(document.getElementById('bulkEditModal')).hide();

                // bỏ chọn sau khi lưu
                $('#btn-clear-selected').click();
            },
            error: function(xhr){
                if(xhr.status === 403){
                    showCenterError("Bạn không có quyền thực hiện thao tác này!");
                    return;
                }
                let msg = "Có lỗi xảy ra, vui lòng thử lại!";
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showCenterError(msg);
            }
        });
    });

    refreshBulkButtons();
});
</script>

@endsection