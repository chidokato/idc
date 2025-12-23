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
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="account">Account</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chuyển tiền</li>
          </ol>
        </nav>
      <h1 class="page-header-title">Chuyển tiền</h1>
      </div>
    </div>
    <!-- End Row -->
  </div>
  <div class="row gx-2 gx-lg-3">
        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
        <!-- Card -->
        <div class="card h-100">
        <div class="card-body">
        <h6 class="card-subtitle mb-2">Tổng tiền hiện có</h6>
        <div class="row align-items-center gx-2">
        <div class="col">
        <span class="js-counter display-4 text-dark" data-value="{{ number_format($wallet->balance) }}">{{ number_format($wallet->balance) }}</span>
        <span class="text-body font-size-sm ml-1">VNĐ</span>
        </div>
        </div>
        <!-- End Row -->
        </div>
        </div>
        <!-- End Card -->
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
        <!-- Card -->
        <div class="card h-100">
        <div class="card-body">
        <h6 class="card-subtitle mb-2">Tiền tạm giữ</h6>

        <div class="row align-items-center gx-2">
        <div class="col">
        <span class="js-counter display-4 text-dark" data-value="{{ number_format($wallet->held_balance) }}">{{ number_format($wallet->held_balance) }}</span>
        <span class="text-body font-size-sm ml-1">VNĐ</span>
        </div>
        </div>
        <!-- End Row -->
        </div>
        </div>
        <!-- End Card -->
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
        <!-- Card -->
        <div class="card h-100">
        <div class="card-body">
        <h6 class="card-subtitle mb-2">Tiền có thể dùng</h6>

        <div class="row align-items-center gx-2">
        <div class="col">
        <span class="js-counter display-4 text-dark" data-value="0">0</span>
        <span class="text-body font-size-sm ml-1">VNĐ</span>
        </div>
        </div>
        <!-- End Row -->
        </div>
        </div>
        <!-- End Card -->
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
        <!-- Card -->
        <div class="card h-100">
        <div class="card-body">
        <h6 class="card-subtitle mb-2">Tổng tiền đã chi</h6>

        <div class="row align-items-center gx-2">
        <div class="col">
        <span class="js-counter display-4 text-dark" data-value="0">0</span>
        <span class="text-body font-size-sm ml-1">VNĐ</span>
        </div>
        </div>
        <!-- End Row -->
        </div>
        </div>
        <!-- End Card -->
        </div>
    </div>
  <div class="page-body">
    <div class="card">
      <div class="card-header">
        <h4 class="card-header-title">Chuyển tiền</h4>
      </div>
      <div class="card-body">
        
      <form method="POST" action="{{ route('wallet.bulk.submit') }}">
        @csrf
        <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">

        <div class="mb-3">
          <label class="form-label">Chế độ</label>
          <select name="mode" id="mode" class="form-control">
            <option value="same" {{ old('mode','same')=='same'?'selected':'' }}>Cùng số tiền cho tất cả</option>
            <option value="custom" {{ old('mode')=='custom'?'selected':'' }}>Mỗi người 1 số tiền</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Chọn người nhận (nhấn giữ Ctrl để chọn nhiều người)</label>
          <select name="recipient_ids[]" id="recipient_ids" class="form-control" multiple size="10">
            @foreach($users as $u)
              <option value="{{ $u->id }}" @if(collect(old('recipient_ids',[]))->contains($u->id)) selected @endif>
                {{ $u->employee_code }} | {{ $u->yourname }} | ({{ $u->email }})
              </option>
            @endforeach
          </select>
          @error('recipient_ids') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        <div id="same_amount_box" class="mb-3">
          <label class="form-label">Số tiền (VND)</label>
          <input type="number" name="amount" class="form-control" min="1000" value="{{ old('amount') }}">
          @error('amount') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        <div id="custom_amount_box" class="mb-3" style="display:none;">
          <label class="form-label">Số tiền theo từng người</label>
          <div id="custom_amount_list"></div>
          @error('amounts') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Ghi chú</label>
          <input type="text" name="note" class="form-control" value="{{ old('note') }}">
        </div>

        <button class="btn btn-primary" type="submit">
          <i class="tio-swap-horizontal mr-1"></i> Chuyển tiền
        </button>
      </form>
      </div>
      

      

    </div>
  </div>
</div>

@endsection


@section('js')
<script>
(function(){
  const modeEl = document.getElementById('mode');
  const recipientsEl = document.getElementById('recipient_ids');
  const sameBox = document.getElementById('same_amount_box');
  const customBox = document.getElementById('custom_amount_box');
  const listBox = document.getElementById('custom_amount_list');

  const userMap = {};
  @foreach($users as $u)
    userMap[{{ $u->id }}] = @json($u->yourname . ' (' . $u->email . ')');
  @endforeach

  const oldAmounts = @json(old('amounts', []));

  function selectedIds(){
    return Array.from(recipientsEl.selectedOptions).map(o => parseInt(o.value,10));
  }

  function renderCustom(){
    listBox.innerHTML = '';
    const ids = selectedIds();
    if(!ids.length) return;

    ids.forEach(id => {
      const row = document.createElement('div');
      row.className = 'd-flex align-items-center mb-2';
      row.innerHTML = `
        <div style="flex:1;padding-right:10px;"><strong>${userMap[id] || ('User ' + id)}</strong></div>
        <div style="width:220px;">
          <input type="number" class="form-control" min="1000" name="amounts[${id}]" placeholder="Số tiền">
        </div>
      `;
      listBox.appendChild(row);

      const inp = row.querySelector(`input[name="amounts[${id}]"]`);
      if(oldAmounts && oldAmounts[id] !== undefined) inp.value = oldAmounts[id];
    });
  }

  function toggle(){
    if(modeEl.value === 'same'){
      sameBox.style.display = '';
      customBox.style.display = 'none';
    }else{
      sameBox.style.display = 'none';
      customBox.style.display = '';
      renderCustom();
    }
  }

  modeEl.addEventListener('change', toggle);
  recipientsEl.addEventListener('change', () => { if(modeEl.value==='custom') renderCustom(); });

  toggle();
})();
</script>
@endsection