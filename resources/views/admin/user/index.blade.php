@extends('admin.layout.main')

@section('content')
@include('admin.layout.header')
@include('admin.alert')
<div class="d-sm-flex align-items-center justify-content-between mb-3 flex">
    <h2 class="h3 mb-0 text-gray-800 line-1 size-1-3-rem">Quản lý người dùng</h2>
    <a class="add-iteam" href="{{route('users.create')}}"><button class="btn-success form-control" type="button"><i class="fa fa-plus" aria-hidden="true"></i> {{__('lang.add')}}</button></a>
</div>

<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <ul class="nav nav-pills">
                    <li><a data-toggle="tab" class="nav-link active" href="#tab1">Admin</a></li>
                    <!-- <li><a data-toggle="tab" class="nav-link " href="#tab2">User</a></li> -->
                    <!-- <li><a data-toggle="tab" class="nav-link" href="#tab3">Ẩn</a></li> -->
                </ul>
            </div>
            <div class="tab-content overflow">
                <div class="tab-pane active" id="tab1">
                    @if(count($admins) > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Phòng/sàn</th>
                                <th>Email</th>
                                <th>Quyền</th>
                                <th>Status</th>
                                <th>date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $val)
                            <tr>
                                <td>{{$val->id}}</td>
                                <td><a href="{{route('users.edit',[$val->id])}}">{{$val->yourname}}</a></td>
                                <td>
                                    {{ $val->Department->name }} / {{ $val->Departmentlv2->name }} / {{ $val->Departmentlv1->name }}
                                </td>
                                <td>{{$val->email}}</td>
                                <td>{{$val->permission}}</td>
                                <td>
                                    <label class="container">
                                        <input type="checkbox" class="change-user-status"
                                               data-id="{{ $val->id }}"
                                               {{ $val->status == 'active' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                
                                <td>{{$val->created_at}}</td>
                                <td style="display: flex;">
                                    <a href="{{route('users.edit',[$val->id])}}" class="mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    <form action="{{route('users.destroy', [$val->id])}}" method="POST">
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

@endsection

@section('css')
    
@endsection
@section('js')
<script>
    $(document).on('change', '.change-user-status', function() {
        console.log("Checkbox clicked!");

        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 'active' : 'inactive';

        $.ajax({
            url: "{{ route('user.changeStatus') }}",
            type: 'POST',
            data: {
                id: id,
                status: status,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log("Server trả về:", response);

                // 🔥 Thông báo thành công
                showToast('success', 'Cập nhật trạng thái thành công!');
            },
            error: function(xhr) {
                console.log("Lỗi:", xhr.responseText);

                // ❌ Thông báo lỗi
                showToast('error', 'Có lỗi xảy ra khi cập nhật!');
            }
        });
    });

</script>
@endsection