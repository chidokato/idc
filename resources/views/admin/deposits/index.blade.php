@extends('admin.layout.main')

@section('css')
<link href="admin_asset/css/custom.css" rel="stylesheet">
@endsection
@section('content')
@include('admin.layout.header')
@include('admin.alert')

<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Danh sách nạp tiền</h2>
</div>

{{-- Filter --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="status" class="form-control">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Chờ duyệt</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Đã duyệt</option>
            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Từ chối</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary">Lọc</button>
    </div>
</form>

<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#tab1">{{__('lang.all')}}</a></li>
                    <!-- <li><a data-toggle="tab" class="nav-link " href="#tab2">Hiển thị</a></li> -->
                    <!-- <li><a data-toggle="tab" class="nav-link" href="#tab3">Ẩn</a></li> -->
                </ul>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="tab2">
                    {{-- Table --}}
                    <table class="table table-bordered align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Số tiền</th>
                        <th>Ngân hàng</th>
                        <th>Mã GD</th>
                        <th>Trạng thái</th>
                        <th width="260">Thao tác</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($deposits as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->user->yourname }}</td>
                        <td>{{ number_format($d->amount) }} đ</td>
                        <td>{{ $d->bank_name }}</td>
                        <td>{{ $d->transaction_code }}
                            @if($d->proof_image)
    <a href="{{ asset('uploads/'.$d->proof_image) }}"
       target="_blank"
       class="btn btn-sm btn-outline-primary">
        Xem ảnh
    </a>
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
                            {{-- Duyệt --}}
                            @if($d->status !== 'approved')
                            <form method="POST" action="{{ route('admin.deposits.updateStatus', $d) }}" class="d-inline">
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
                            <form method="POST" action="{{ route('admin.deposits.updateStatus', $d) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Xác nhận từ chối / rollback?')">
                                    Từ chối
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>

                    {{-- History --}}
                    @if($d->histories->count())
                    <tr class="bg-light">
                        <td colspan="7">
                            <ul class="mb-0">
                                @foreach($d->histories as $h)
                                    <li>
                                        {{ $h->created_at }}
                                        – <b>{{ $h->admin->yourname }}</b>
                                        → <i>{{ strtoupper($h->action) }}</i>
                                        @if($h->note) ({{ $h->note }}) @endif
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                    @endif

                    @endforeach
                    </tbody>
                    </table>

                    {{ $deposits->links() }}

                    </div>

                </div>
                
            </div>
        </div>
    </div>
</div>

@endsection
