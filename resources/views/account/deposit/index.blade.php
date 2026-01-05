@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')
<link rel="stylesheet" href="daterangepicker/daterangepicker.css">
@endsection

@section('body') @endsection

@section('content')

<div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
          <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
              <h1 class="page-header-title">Trang chủ</h1>
            </div>
          </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-2">
          <div class="col-lg-12 mb-3 mb-lg-12">
            <!-- Card -->
            <div class="card h-100">
              <!-- Header -->
              <form method="GET" action="{{ url()->current() }}">
  <div class="card-header">
    <div class="row align-items-center flex-grow-1 g-2">

      <div class="col-lg-2">
  <input type="text"
         name="yourname"
         value="{{ request('yourname') }}"
         class="form-control"
         placeholder="Tìm theo tên...">
</div>


        <div class="col-lg-2">
            <select name="department_id" class="form-control">
              <option value="">-- Sàn/phòng/nhóm --</option>
              {!! $departmentOptions !!}
              </select>

                    </div>

                    <div class="col-lg-2">
                      <select name="status" class="form-control">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status')=='approved' ? 'selected':'' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status')=='rejected' ? 'selected':'' }}>Từ chối</option>
                      </select>
                    </div>

                    <div class="col-lg-2 d-flex gap-2">
                     <!-- <input
  type="text"
  name="range"
  class="js-daterangepicker form-control"
  placeholder="Chọn khoảng thời gian"
  value="{{ request('range') }}"
  data-range="{{ request('range') }}"
> -->

<input
  type="text"
  name="range"
  class="js-daterangepicker-clear form-control daterangepicker-custom-input"
  placeholder="Select dates"
  value="{{ request('range') ?? '' }}"
  data-hs-daterangepicker-options='{
    "autoUpdateInput": false,
    "locale": { "cancelLabel": "Clear" }
  }'
>



                    </div>

                    <div class="col-lg-3 d-flex gap-2">
                      <button class="btn btn-primary">Lọc</button>
                      <a href="{{ url()->current() }}" class="btn btn-warning">Reset</a>
                    </div>

                  </div>
                </div>
              </form>

              <!-- Body -->
                <!-- Bar Chart -->
                <div class="table-responsive">
                  <table class="table table-lg table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>Mã NV</th>
                        <th>Họ Tên</th>
                        <th>Sàn/Nhóm</th>
                        <th>Số tiền</th>
                        <th>Check</th>
                        <th>Tình trạng</th>
                        <th>Thời gian</th><th>Thao tác</th>
                        <th>Lịch sử</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($deposits as $d)
                    <tr>
                        <td>{{ $d->user->employee_code }}</td>
                        <td>{{ $d->user->yourname }}</td>
                        <td>{{ $d->user->department?->name }}</td>
                        <td>{{ number_format($d->amount) }} đ</td>
                        <td>{{ $d->transaction_code }}

                            @if($d->proof_image)
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary btn-proof-modal"
                                        data-src="{{ asset('uploads/'.$d->proof_image) }}">
                                    Images
                                </button>
                            @endif
                        </td>
                        
                        <td>
                            @if($d->status=='pending')
                                <span class="badge bg-warning">Chờ duyệt</span>
                            @elseif($d->status=='approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @else
                                <span class="badge bg-danger">Từ chối</span>
                            @endif
                        </td>

                        <td>
                          {{ $d->created_at }}
                        </td>
                        <td>
                            {{-- Duyệt --}}
                            @if($d->status !== 'approved')
                            <form method="POST" action="{{ route('deposits.updateStatus', $d) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button class="btn btn-success btn-sm"
                                    onclick="return confirm('Xác nhận duyệt nạp tiền?')">
                                    Duyệt
                                </button>
                            </form>
                            @endif

                            {{-- Từ chối / Rollback --}}
                            @if($d->status !== 'rejected')
                            <form method="POST" action="{{ route('deposits.updateStatus', $d) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Xác nhận từ chối / rollback?')">
                                    Từ chối
                                </button>
                            </form>
                            @endif
                        </td>

                          <td>
                            @if($d->histories->count())
                              <button type="button"
                                      class="btn btn-sm btn-outline-secondary popover-history"
                                      data-toggle="popover"
                                      data-placement="left">
                                Lịch sử
                              </button>

                              <div class="history-content d-none">
                                <ul class="mb-0 ps-3">
                                  @foreach($d->histories as $h)
                                    <li>
                                      {{ $h->created_at }}
                                      – <b>{{ $h->admin->yourname }}</b>
                                      → <i>{{ strtoupper($h->action) }}</i>
                                      @if($h->note) ({{ $h->note }}) @endif
                                    </li>
                                  @endforeach
                                </ul>
                              </div>
                            @endif
                          </td>


                  

                        
                    </tr>

              
                    @endforeach
                    </tbody>
                    </table>
                  </div>
                    {{ $deposits->links() }}
                <!-- End Bar Chart -->
              <!-- End Body -->
            </div>
            <!-- End Card -->
          </div>
        </div>
        <!-- End Row -->

        <!-- Card -->

        
      </div>


      <!-- Modal Zoom Image -->
<div class="modal fade" id="proofModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ảnh minh chứng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body text-center">
        <img style="    height: 75vh;" id="proofModalImg" src="" alt="Proof" class="img-fluid rounded">
      </div>
    </div>
  </div>
</div>



@endsection


@section('js')

<script src="daterangepicker/moment.min.js"></script>
<script src="daterangepicker/daterangepicker.js"></script>

<script>
  $(document).on('ready', function () {
    $.HSCore.components.HSDaterangepicker.init($('.js-daterangepicker-clear'));

    $('.js-daterangepicker-clear').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('.js-daterangepicker-clear').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
    });
  });
</script>







<script>
$(function () {
  // Nếu bảng có redraw (DataTables/Ajax), gọi lại hàm này sau khi render xong
  function initHistoryPopovers() {
    $('.popover-history').each(function () {
      const html = $(this).siblings('.history-content').html() || '';
      $(this).attr('data-content', html); // đẩy content vào attribute trước
    });

    $('.popover-history').popover('dispose').popover({
      trigger: 'focus',
      html: true,
      container: 'body'
    });
  }

  initHistoryPopovers();

  // Nếu bạn dùng DataTables, mở comment này:
  // $('#yourTableId').on('draw.dt', initHistoryPopovers);
});
</script>


<script>
  $(document).on('click', '.btn-proof-modal', function () {
    const src = $(this).data('src');
    $('#proofModalImg').attr('src', src);

    const modal = new bootstrap.Modal(document.getElementById('proofModal'));
    modal.show();
  });

  // Clear src khi đóng (đỡ giữ bộ nhớ)
  $('#proofModal').on('hidden.bs.modal', function () {
    $('#proofModalImg').attr('src', '');
  });
</script>


@endsection