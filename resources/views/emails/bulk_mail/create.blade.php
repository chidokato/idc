@extends('account.layout.index')

@section('content')
<div class="container">
    <h3>Gửi mail hàng loạt</h3>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <div><b>Lỗi:</b></div>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-info">
        Tổng user có email trong DB: <b>{{ number_format($count) }}</b><br>
        Hỗ trợ cá nhân hoá: dùng <code>{name}</code> và <code>{email}</code> trong nội dung.
    </div>

    @if(!empty($batchId))
    <hr>
    <h5>Trạng thái gửi (Batch: <code>{{ $batchId }}</code>)</h5>

    @if($stats)
        <div class="mb-3">
            <span class="badge bg-secondary">Queued: {{ $stats['queued'] }}</span>
            <span class="badge bg-success">Sent: {{ $stats['sent'] }}</span>
            <span class="badge bg-danger">Failed: {{ $stats['failed'] }}</span>
            <a class="btn btn-sm btn-outline-primary ms-2" href="{{ route('admin.bulk_mail.create', ['batch_id' => $batchId]) }}">
                Refresh
            </a>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Email</th>
                    <th>Trạng thái</th>
                    <th>Gửi lúc</th>
                    <th>Lỗi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->email }}</td>
                        <td>
                            @if($row->status === 'sent')
                                <span class="badge bg-success">sent</span>
                            @elseif($row->status === 'failed')
                                <span class="badge bg-danger">failed</span>
                            @else
                                <span class="badge bg-secondary">queued</span>
                            @endif
                        </td>
                        <td>{{ optional($row->sent_at)->format('Y-m-d H:i:s') }}</td>
                        <td style="max-width: 420px; white-space: normal;">
                            @if($row->status === 'failed')
                                <small class="text-danger">{{ $row->error }}</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Chưa có log.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($logs, 'links'))
        <div class="mt-2">
            {{ $logs->links() }}
        </div>
    @endif
@endif


    <form method="POST" action="{{ route('admin.bulk_mail.send') }}">
        @csrf

        <div class="form-group mb-3">
            <label>Tiêu đề (Subject)</label>
            <input type="text" name="subject" class="form-control"
                   value="{{ old('subject', 'Thông báo') }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Nội dung (Template)</label>
            <textarea name="content" rows="10" class="form-control" required>{{ old('content', "Chào {name},\n\nNội dung email của bạn ở đây...\n") }}</textarea>
            <small class="text-muted">
                Ví dụ: "Chào {name}," sẽ tự thay theo tên từng người.
            </small>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label>Số lượng gửi (limit)</label>
                <input type="number" name="limit" class="form-control"
                       value="{{ old('limit', 500) }}" min="1" max="5000">
                <small class="text-muted">Gmail cá nhân thường nên để 500/ngày.</small>
            </div>

            <div class="col-md-3 mb-3">
                <label>Delay mỗi email (giây)</label>
                <input type="number" name="seconds_per_email" class="form-control"
                       value="{{ old('seconds_per_email', 3) }}" min="0" max="60">
                <small class="text-muted">Khuyến nghị 2–5 giây để tránh bị Gmail chặn.</small>
            </div>

            <div class="col-md-3 mb-3 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="only_verified" value="1"
                           id="only_verified" {{ old('only_verified') ? 'checked' : '' }}>
                    <label class="form-check-label" for="only_verified">
                        Chỉ gửi email đã verify
                    </label>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit"
                    onclick="return confirm('Xác nhận xếp hàng gửi email hàng loạt?');">
                Gửi hàng loạt
            </button>

            <a href="{{ url('/') }}" class="btn btn-secondary">Quay lại</a>
        </div>

        <hr>

        <div class="alert alert-warning mb-0">
            <b>Lưu ý:</b> Trang này chỉ “xếp hàng” (dispatch job). Bạn cần chạy queue worker:
            <code>php artisan queue:work --queue=mail</code>
        </div>
    </form>
</div>
@endsection
