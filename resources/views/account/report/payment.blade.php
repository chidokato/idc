@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/news.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
@endsection

@section('content')
@include('account.layout.menu')
<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                @include('account.layout.sitebar')
            </div>
            <div class="col-lg-10">
                @include('account.partials.task_view')
            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('js')
<script>
function toggleGroup(key){
    const rows = document.querySelectorAll(
        `[data-group="${key}"], [data-subgroup="${key}"], [data-leaf="${key}"]`
    );
    rows.forEach(r => r.style.display = (r.style.display === 'none' ? '' : 'none'));
}
</script>

<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  document.addEventListener('change', async (e) => {
    if (!e.target.classList.contains('js-paid-toggle')) return;

    const checkbox = e.target;
    const taskId = checkbox.dataset.id;
    const paid = checkbox.checked ? 1 : 0;

    // lock tạm để tránh double click
    checkbox.disabled = true;

    try {
      const res = await fetch(`/tasks/${taskId}/paid`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ paid })
      });

      if (!res.ok) throw new Error('Request failed');
      const data = await res.json();

      // cập nhật paid_at nhỏ nhỏ
      const paidAtEl = document.querySelector(`.js-paid-at-${taskId}`);
      if (paidAtEl) paidAtEl.textContent = data.paid_at ?? '';

    } catch (err) {
      // rollback UI nếu lỗi
      checkbox.checked = !checkbox.checked;
      alert('Không cập nhật được trạng thái đóng tiền. Vui lòng thử lại.');
    } finally {
      checkbox.disabled = false;
    }
  });
</script>

@endsection