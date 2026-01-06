@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')

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
        <div class="row gx-2 gx-lg-3">
          <div class="col-lg-12 mb-3 mb-lg-12">
            <!-- Card -->
            <div class="card h-100">
              <!-- Header --><form method="GET" action="{{ url()->current() }}">
              <div class="card-header">
                
                  <div class="row align-items-center flex-grow-1 g-2">

                    <div class="col-lg-4">
                      <input type="text"
                             name="key"
                             value="{{ request('key') }}"
                             class="form-control"
                             placeholder="Tìm mã nhân viên / tên...">
                    </div>

                    <div class="col-lg-4">
                      <select name="department_id" class="form-control">
                        <option value="">-- Nhóm/Phòng (đệ quy) --</option>
                        {!! $departmentOptions !!}
                      </select>
                    </div>

                    <div class="col-lg-4 d-flex gap-2">
                      <button class="btn btn-primary">Lọc</button>
                      <a href="{{ url()->current() }}" class="btn btn-warning">Reset</a>
                    </div>

                  </div>
              

                <!-- End Nav -->
              </div></form>
              <!-- End Header -->

              <!-- Body -->
              <div class="table-responsive datatable-custom">
              <table class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                  <tr>
                    <th>Mã NV</th>
                    <th>Email</th>
                    <th>Họ tên</th>
                    <th>Phòng ban</th>
                    <th>Số dư</th>
                    <th>Tiền Hold</th>
                    <th>Lịch sử</th>
                    <th>Cập nhật</th>
                  </tr>
                </thead>

                <tbody>
                  @forelse($wallets as $w)
                    <tr>
                      <td>{{ $w->user?->employee_code ?? '---' }}</td>
                      <td>{{ $w->user?->email ?? '---' }}</td>
                      <td>{{ $w->user?->yourname ?? '---' }}</td>
                      <td>{{ $w->user?->department?->name ?? '---' }}</td>
                      <td>{{ number_format($w->balance ?? 0) }} đ</td>
                      <td>{{ number_format($w->held_balance ?? 0) }} đ</td>
                      <td>
                        <a class="btn btn-xs btn-white mr-2 btn-wallet-history"
                           href="javascript:;"
                           data-wallet-id="{{ $w->id }}"
                           data-user-name="{{ $w->user?->yourname ?? '---' }}"
                           data-toggle="modal"
                           data-target="#editCardModal">
                          <i class="tio-edit mr-1"></i> Lịch sử
                        </a>


                      </td>
                      <td>{{ $w->updated_at }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>

              <div class="mt-3">
                {{ $wallets->links() }}
              </div>
            </div>

              <!-- End Body -->
            </div>
            <!-- End Card -->
          </div>
        </div>
        <!-- End Row -->

<!-- Card -->

      
      </div>


<div class="modal fade" id="editCardModal" tabindex="-1" role="dialog" aria-labelledby="editCardModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h4 id="editCardModalTitle" class="modal-title">Lịch sử</h4>

        <button type="button" class="btn btn-icon btn-sm btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
          <i class="tio-clear tio-lg"></i>
        </button>
      </div>
      <!-- End Header -->

      <!-- Body -->
      <div class="modal-body">
          <!-- Form Group -->
        <table class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
          <thead class="thead-light">
            <tr>
              <td>Thời gian</td>
              <td>Loại</td>
              <td>Số tiền</td>
              <td>Ghi chú</td>
            </tr>
          </thead>
          <tbody id="wallet-history-body">
  <tr>
    <td colspan="4" class="text-center text-muted">Đang tải...</td>
  </tr>
</tbody>

        </table>
          <!-- End Form Group -->
      </div>
      <!-- End Body -->
    </div>
  </div>
</div>

@endsection


@section('js')


<script>
  $(document).on('click', '.btn-wallet-history', function () {
    const walletId = $(this).data('wallet-id');
    const userName = $(this).data('user-name') || '---';

    $('#editCardModalTitle').text('Lịch sử ví - ' + userName);
    $('#wallet-history-body').html(`<tr><td colspan="4" class="text-center text-muted">Đang tải...</td></tr>`);

    // route() ra URL kiểu: /account/wallets/0/histories rồi replace 0 thành walletId
    let url = @json(route('wallets.histories', ['wallet' => 0]));
    url = url.replace('/0/', '/' + walletId + '/');

    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'json',
      success: function (res) {
        if (!res || !res.ok) {
          $('#wallet-history-body').html(`<tr><td colspan="4" class="text-center text-danger">Load thất bại</td></tr>`);
          showToast?.('error', 'Không tải được lịch sử ví.');
          return;
        }
        $('#wallet-history-body').html(res.html);
      },
      error: function (xhr) {
        $('#wallet-history-body').html(`<tr><td colspan="4" class="text-center text-danger">Có lỗi xảy ra</td></tr>`);
        showToast?.('error', 'Có lỗi khi tải lịch sử ví.');

        // debug nhanh (mở console sẽ thấy)
        console.log('Status:', xhr.status);
        console.log('Response:', xhr.responseText);
      }
    });
  });
</script>



@endsection