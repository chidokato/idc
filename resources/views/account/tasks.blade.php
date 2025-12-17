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
                <form method="GET" action="{{ url()->current() }}">
    <div class="text-uppercase title-cat flex gap1">
        <div>
            <select class="form-control" name="department_id" onchange="this.form.submit()">
                <option value="">Tất cả sàn</option>
                {!! $departmentOptions !!}
            </select>
        </div>

        <div>
            <select class="form-control" name="report_id" onchange="this.form.submit()">
                @foreach($reports as $val)
                    <option value="{{ $val->id }}" {{ (int)$selectedReportId === (int)$val->id ? 'selected' : '' }}>
                        {{ $val->name }} ({{ date('d/m/Y',strtotime($val->time_start)) }} - {{ date('d/m/Y',strtotime($val->time_end)) }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</form>

                <div class="table-responsive-mobile">
    <table class="table table-task">
        <thead class="thead1">
    <tr class="text-white bg-secondary">
        <th></th>
        <th>Mã NV</th>
        <th>Họ Tên</th>
        <th>Phòng/Nhóm</th>
        <th>Dự án</th>
        <th class="text-center">Kênh</th>
        <th class="text-end">Tổng tiền (đ)</th>
        <th class="text-end">Hỗ trợ</th>
        <th class="text-end">Tiền nộp (đ)</th>
        <th>Ghi chú</th>
        <th>Thời gian</th>
    </tr>

    <tr class="bg-light bg-gradient totall">
        <td colspan="6"><b>TỔNG CHI PHÍ TOÀN SÀN</b></td>
        <td class="text-end"><b>{{ number_format($grandGross, 0, ',', '.') }}</b></td>
        <td></td>
        <td class="text-end"><b>{{ number_format($grandNet, 0, ',', '.') }}</b></td>
        <td colspan="2"></td>
    </tr>
</thead>

<tbody>
@foreach($lv2Tree as $lv2)
    @php $lv2Key = 'lv2_'.$lv2['id']; @endphp

    {{-- LV2 subtotal --}}
    <tr class="bg-primary text-white" style="cursor:pointer"
        onclick="toggleGroup('{{ $lv2Key }}')">
        <td colspan="6"><b>▶ {{ $lv2['name'] }}</b></td>
        <td class="text-end"><b>{{ number_format($lv2['gross'], 0, ',', '.') }}</b></td>
        <td></td>
        <td class="text-end"><b>{{ number_format($lv2['net'], 0, ',', '.') }}</b></td>
        <td colspan="2"></td>
    </tr>

    @foreach($lv2['rooms'] as $room)
        @php $roomKey = $lv2Key.'_room_'.$room['id']; @endphp

        {{-- PHÒNG subtotal (cái bạn cần thêm) --}}
        <tr class="bg-warning bg-gradient" data-group="{{ $lv2Key }}" style="cursor:pointer"
            onclick="toggleGroup('{{ $roomKey }}')">
            <td colspan="6"><b>— {{ $room['name'] }}</b></td>
            <td class="text-end"><b>{{ number_format($room['gross'], 0, ',', '.') }}</b></td>
            <td></td>
            <td class="text-end"><b>{{ number_format($room['net'], 0, ',', '.') }}</b></td>
            <td colspan="2"></td>
        </tr>

        @foreach($room['users'] as $uNode)
            @php $userKey = $roomKey.'_u_'.$uNode['id']; @endphp

            {{-- USER subtotal --}}
            <tr class="bg-light" data-group="{{ $lv2Key }}" data-subgroup="{{ $roomKey }}"
                style="cursor:pointer" onclick="toggleGroup('{{ $userKey }}')">
                <td></td>
                <td>{{ $uNode['employee_code'] }}</td>
                <td><b>—— {{ $uNode['yourname'] }}</b></td>
                <td colspan="3"></td>
                <td class="text-end"><b>{{ number_format($uNode['gross'], 0, ',', '.') }}</b></td>
                <td></td>
                <td class="text-end"><b>{{ number_format($uNode['net'], 0, ',', '.') }}</b></td>
                <td colspan="2"></td>
            </tr>

            {{-- TASK rows --}}
            @foreach($uNode['tasks'] as $task)
                <tr data-group="{{ $lv2Key }}" data-subgroup="{{ $roomKey }}" data-leaf="{{ $userKey }}">
                    <td><span class="badge bg-success">Duyệt</span></td>
                    <td>{{ $uNode['employee_code'] }}</td>
                    <td>——— {{ $uNode['yourname'] }}</td>
                    <td>{{ $task->department?->name }}</td>
                    <td>{{ $task->Post?->name }}</td>
                    <td class="text-center">{{ $task->Channel?->name }}</td>
                    <td class="text-end">{{ number_format($task->gross_cost, 0, ',', '.') }}</td>
                    <td class="text-end">{{ $task->rate }}%</td>
                    <td class="text-end">{{ number_format($task->net_cost, 0, ',', '.') }}</td>
                    <td>{{ $task->content }}</td>
                    <td>
                        {{ date('d/m/Y', strtotime($task->Report->time_start)) }}
                        -
                        {{ date('d/m/Y', strtotime($task->Report->time_end)) }}
                    </td>
                </tr>
            @endforeach
        @endforeach
    @endforeach
@endforeach
</tbody>
    </table>
</div>

              
            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('script')
<script>
function toggleGroup(key){
    const rows = document.querySelectorAll(
        `[data-group="${key}"], [data-subgroup="${key}"], [data-leaf="${key}"]`
    );
    rows.forEach(r => r.style.display = (r.style.display === 'none' ? '' : 'none'));
}
</script>


@endsection