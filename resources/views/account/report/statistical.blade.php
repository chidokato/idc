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
    <form method="POST" action="account/task-cost-post/update">
      <div class="row align-items-center flex-grow-1">
          @csrf
          <!-- <div class="col-sm-auto">
            <select name="duan" class="js-select2-custom custom-select select2-hidden-accessible">
              <option value="post_id">Dự án</option>
            </select>
          </div> -->
          <div class="col-sm-auto">
            <select name="department_id" class="js-select2-custom custom-select select2-hidden-accessible">
              @foreach($departments as $dep)
              <option value="{{ $dep->id }}">{{ $dep->name }}</option>
              @endforeach
            </select>
          </div>
          <!-- <div class="col-sm-auto">
            <select name="nam" class="js-select2-custom custom-select select2-hidden-accessible">
              <option value="2026">Năm 2026</option>
            </select>
          </div>
          <div class="col-sm-auto">
            <select name="thang" class="js-select2-custom custom-select select2-hidden-accessible">
              <option value="1">Tháng 1</option>
            </select>
          </div> -->
          <div class="col-sm-auto">
            <select name="report_id[]" class="js-select2-custom custom-select select2-hidden-accessible" multiple>
              @foreach($reports as $rep)
              <option value="{{ $rep->id }}">{{ $rep->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-auto">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
          </div>
      </div>
    </form>
  </div>
  <div class="row">
    <div class="col-sm-3 mb-3 ">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center flex-grow-1">
            <div class="col-sm mb-2 mb-sm-0">
              <h4 class="card-header-title">Dự án <i class="tio-help-outlined text-body ml-1" data-toggle="tooltip" data-placement="top" title="Dự án"></i></h4>
            </div>
          </div>
        </div>
        <div class="card-body">
          <table>
            <th>
              <td>Dự án</td>
              <td>Tổng tiền</td>
            </th>
            @foreach($duan_idc as $val)
            <tr>
              <td>{{ $val->Post->name }}</td>
              <td>{{ $val->total_cost }}</td>
            </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>
  </div>
  
</div>

@endsection


@section('js')




@endsection