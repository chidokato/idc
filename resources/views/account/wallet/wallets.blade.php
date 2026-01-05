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
                    <th>Họ tên</th>
                    <th>Phòng ban</th>
                    <th class="text-end">Số dư</th>
                    <th>Cập nhật</th>
                  </tr>
                </thead>

                <tbody>
                  @forelse($wallets as $w)
                    <tr>
                      <td>{{ $w->user?->employee_code ?? '---' }}</td>
                      <td>{{ $w->user?->yourname ?? '---' }}</td>
                      <td>{{ $w->user?->department?->name ?? '---' }}</td>
                      <td class="text-end">{{ number_format($w->balance ?? 0) }} đ</td>
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

@endsection


@section('js')




@endsection