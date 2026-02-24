@extends('account.layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection

@section('css')

@endsection

@section('body') @endsection

@section('content')

<div class="content container-fluid">

  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-sm mb-2 mb-sm-0">
        <h1 class="page-header-title">Trang chủ</h1>
      </div>
    </div>
  </div>

  <div class="mb-3">
    <div class="row align-items-center flex-grow-1">
      <div class="col-sm-auto">
        <select id="filterYear" class="js-select2-custom custom-select select2-hidden-accessible">
          <option value="2026">Năm 2026</option>
        </select>
      </div>
      <div class="col-sm-auto">
        <select id="filterMonth" class="js-select2-custom custom-select select2-hidden-accessible">
          <option value="1">Tháng 1</option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-3 mb-3 ">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center flex-grow-1">
            <div class="col-sm mb-2 mb-sm-0">
              <h4 class="card-header-title">Dự án <i class="tio-help-outlined text-body ml-1" data-toggle="tooltip" data-placement="top" title="Dự án"></i></h4>
            </div>
            <div class="col-sm-auto">
              <button
                type="button"
                class="btn btn-primary jsUpdateProject"
                data-url="{{ route('task_cost_post.update', ['note' => '__NOTE__']) }}"
                data-note="post"
              >Cập nhật</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          
        </div>
      </div>
    </div>
  </div>
  
</div>

@endsection


@section('js')

<script>
  $(document).on('click', '.jsUpdateProject', function (e) {
    e.preventDefault();

    const $btn = $(this);

    const templateUrl = $btn.data('url'); // .../update/__NOTE__
    const note = $btn.data('note');       // post | san | nhom
    const url = templateUrl.replace('__NOTE__', encodeURIComponent(note));

    const year = $('#filterYear').val();
    const month = $('#filterMonth').val();

    const oldText = $btn.text();
    $btn.prop('disabled', true).text('Đang cập nhật...');

    $.ajax({
      url,
      type: 'POST',
      dataType: 'json',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      data: { year, month },
      success: (res) => alert(res.message ?? 'Cập nhật thành công!'),
      error: (xhr) => alert(xhr.responseJSON?.message ?? 'Có lỗi xảy ra!'),
      complete: () => $btn.prop('disabled', false).text(oldText)
    });
  });


</script>


@endsection