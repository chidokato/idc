@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<?php use App\Models\CategoryTranslation; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý khách hàng</h2>
</div>

<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#tab1">{{__('lang.all')}}</a></li>
                </ul>
                <button type="button" class="btn btn-danger btn-sm" onclick="submitBulkDelete()">Xóa mục đã chọn</button>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="tab2">
                    @if(count($customer) > 0)
                        <table class="table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="check-all"></th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Link</th>
                                        <th>date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer as $val)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{$val->id}}" class="check-item"></td>
                                        <td>{{$val->name}}</td>
                                        <td>{{$val->phone}}</td>
                                        <td>{{$val->email}}</td>
                                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{$val->title}}">{{$val->title}}</td>
                                        <td>{{$val->created_at}}</td>
                                        <td style="display: flex;">
                                            <form action="{{route('customer.destroy', [$val->id])}}" method="POST">
                                              @method('DELETE')
                                              @csrf
                                              <button class="button_none" onclick="return confirm('Bạn muốn xóa bản ghi ?')"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                        </table>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('check-all').addEventListener('change', function(e) {
        let checkboxes = document.querySelectorAll('.check-item');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });

    function submitBulkDelete() {
        let checked = document.querySelectorAll('.check-item:checked');
        if (checked.length === 0) {
            alert('Vui lòng chọn ít nhất một bản ghi để xóa.');
            return;
        }
        if (confirm('Bạn có chắc chắn muốn xóa các bản ghi đã chọn?')) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('customer.bulkDelete') }}";
            
            let csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            checked.forEach(item => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = item.value;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

@endsection