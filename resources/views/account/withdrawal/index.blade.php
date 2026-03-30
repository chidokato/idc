@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Duyệt rút tiền</h1>
            </div>
        </div>
    </div>

    <div class="row gx-2 gx-lg-2">
        <div class="col-lg-12 mb-3 mb-lg-12">
            <div class="card h-100">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="card-header">
                        <div class="row align-items-center flex-grow-1 g-2">
                            <div class="col-lg-3">
                                <input type="text" name="yourname" value="{{ request('yourname') }}" class="form-control" placeholder="Tìm theo tên hoặc mã NV...">
                            </div>
                            <div class="col-lg-3">
                                <select name="department_id" class="form-control">
                                    <option value="">Nhóm/phòng</option>
                                    {!! $departmentOptions !!}
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select name="status" class="form-control">
                                    <option value="">Trạng thái</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã chuyển tiền</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                </select>
                            </div>
                            <div class="col-lg-4 d-flex gap-2">
                                <button class="btn btn-primary">Lọc</button>
                                <a href="{{ url()->current() }}" class="btn btn-warning">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-lg table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Mã NV</th>
                                <th>Họ tên</th>
                                <th>Nhóm/Sàn</th>
                                <th>Số tiền</th>
                                <th>Thông tin nhận tiền</th>
                                <th>Trạng thái</th>
                                <th>UNC</th>
                                <th>Thời gian</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ number_format($sumAmount) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach($withdrawals as $item)
                                <tr>
                                    <td>{{ $item->user->employee_code }}</td>
                                    <td>{{ $item->user->yourname }}</td>
                                    <td>{{ $item->user->department?->name }}</td>
                                    <td>{{ number_format($item->amount) }}</td>
                                    <td>
                                        <div><strong>{{ $item->bank_name }}</strong></div>
                                        <div>{{ $item->bank_account_name }}</div>
                                        <div class="small text-muted">{{ $item->bank_account_number }}</div>
                                        @if($item->note)
                                            <div class="small text-muted mt-1">{{ $item->note }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status === 'pending')
                                            <span class="badge badge-soft-warning">Chờ xử lý</span>
                                        @elseif($item->status === 'approved')
                                            <span class="badge badge-soft-success">Đã chuyển tiền</span>
                                        @else
                                            <span class="badge badge-soft-danger">Từ chối</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->transfer_proof)
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-proof-modal"
                                                data-src="{{ asset('uploads/' . $item->transfer_proof) }}">
                                                Xem UNC
                                            </button>
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($item->status === 'pending')
                                            <form method="POST" action="{{ route('withdrawals.process', $item) }}" enctype="multipart/form-data" class="mb-2">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <div class="mb-2">
                                                    <input type="file" name="transfer_proof" class="form-control form-control-sm" accept="image/*" required>
                                                </div>
                                                <div class="mb-2">
                                                    <textarea name="note" rows="2" class="form-control form-control-sm" placeholder="Ghi chú xác nhận chuyển tiền"></textarea>
                                                </div>
                                                <button class="btn btn-success btn-sm w-100" onclick="return confirm('Xác nhận đã chuyển tiền và trừ ví chính?')">Xác nhận chuyển tiền</button>
                                            </form>

                                            <form method="POST" action="{{ route('withdrawals.process', $item) }}">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <input type="text" name="note" class="form-control form-control-sm mb-2" placeholder="Lý do từ chối">
                                                <button class="btn btn-danger btn-sm w-100" onclick="return confirm('Xác nhận từ chối lệnh rút tiền này?')">Từ chối</button>
                                            </form>
                                        @else
                                            <span class="text-muted">Đã xử lý</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $withdrawals->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="proofModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ủy nhiệm chi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img style="height: 75vh;" id="proofModalImg" src="" alt="Proof" class="img-fluid rounded">
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  $(document).on('click', '.btn-proof-modal', function () {
    const src = $(this).data('src');
    $('#proofModalImg').attr('src', src);

    const modal = new bootstrap.Modal(document.getElementById('proofModal'));
    modal.show();
  });

  $('#proofModal').on('hidden.bs.modal', function () {
    $('#proofModalImg').attr('src', '');
  });
</script>
@endsection
