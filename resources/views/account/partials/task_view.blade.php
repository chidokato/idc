<form method="GET" action="{{ url()->current() }}">
    <div class="text-uppercase title-cat flex gap1">
        <div>
            <select class="form-control" name="department_id" onchange="this.form.submit()">
                {!! $departmentOptions !!}
            </select>
        </div>

        <div>
            <select class="form-control" name="report_id" onchange="this.form.submit()">
                @foreach($reports as $key => $val)
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
            <tr class="text-white bg-dark">
                <th></th>
                <th>Mã NV</th>
                <th>Họ Tên</th>
                <th>Phòng/Nhóm</th>
                <th>Dự án</th>
                <th class="text-center">Kênh</th>
                <th class="text-end">Tổng tiền</th>
                <th class="text-end">Hỗ trợ</th>
                <th class="text-end">Tiền nộp</th>
                <th>Ghi chú</th>
                <!-- <th>Thời gian</th> -->
            </tr>
        </thead>
        <tbody>
        @foreach($tree as $lv1)
            @php $lv1Key = 'lv1_'.$lv1['id']; @endphp

            {{-- LV1 subtotal --}}
            <tr class="bg-secondary text-white" style="cursor:pointer" onclick="toggleGroup('{{ $lv1Key }}')">
                <td colspan="6"><b>▶ {{ $lv1['name'] }}</b></td>
                <td class="text-end"><b>{{ number_format($lv1['gross'],0,',','.') }}</b></td>
                <td></td>
                <td class="text-end"><b>{{ number_format($lv1['net'],0,',','.') }}</b></td>
                <td colspan="2"></td>
            </tr>

            @foreach($lv1['lv2s'] as $lv2)
                @php $lv2Key = $lv1Key.'_lv2_'.$lv2['id']; @endphp

                {{-- LV2 subtotal --}}
                <tr class="bg-primary text-white" data-group="{{ $lv1Key }}" style="cursor:pointer"
                    onclick="toggleGroup('{{ $lv2Key }}')">
                    <td colspan="6"><b>— {{ $lv2['name'] }}</b></td>
                    <td class="text-end"><b>{{ number_format($lv2['gross'],0,',','.') }}</b></td>
                    <td></td>
                    <td class="text-end"><b>{{ number_format($lv2['net'],0,',','.') }}</b></td>
                    <td colspan="2"></td>
                </tr>

                @foreach($lv2['rooms'] as $room)
                    @php $roomKey = $lv2Key.'_room_'.$room['id']; @endphp

                    {{-- PHÒNG subtotal --}}
                    <tr class="bg-warning bg-gradient" data-group="{{ $lv1Key }}" data-subgroup="{{ $lv2Key }}"
                        style="cursor:pointer" onclick="toggleGroup('{{ $roomKey }}')">
                        <td colspan="6"><b>—— {{ $room['name'] }}</b></td>
                        <td class="text-end"><b>{{ number_format($room['gross'],0,',','.') }}</b></td>
                        <td></td>
                        <td class="text-end"><b>{{ number_format($room['net'],0,',','.') }}</b></td>
                        <td colspan="2"></td>
                    </tr>

                    @foreach($room['users'] as $uNode)
                        @php $userKey = $roomKey.'_u_'.$uNode['id']; @endphp

                        {{-- USER subtotal --}}
                        <tr class="bg-light" data-group="{{ $lv1Key }}" data-subgroup="{{ $lv2Key }}" data-leaf="{{ $roomKey }}"
                            style="cursor:pointer" onclick="toggleGroup('{{ $userKey }}')">
                            <td></td>
                            <td>{{ $uNode['employee_code'] }}</td>
                            <td><b>——— {{ $uNode['yourname'] }}</b></td>
                            <td colspan="3"></td>
                            <td class="text-end"><b>{{ number_format($uNode['gross'],0,',','.') }}</b></td>
                            <td></td>
                            <td class="text-end"><b>{{ number_format($uNode['net'],0,',','.') }}</b></td>
                            <td colspan="2"></td>
                        </tr>

                        {{-- TASK rows --}}
                        @foreach($uNode['tasks'] as $task)
                            <tr data-group="{{ $lv1Key }}" data-subgroup="{{ $lv2Key }}" data-leaf="{{ $roomKey }}" data-node="{{ $userKey }}">
                                <td><span class="badge bg-success">Duyệt</span></td>
                                <td>{{ $uNode['employee_code'] }}</td>
                                <td>———— {{ $uNode['yourname'] }}</td>
                                <td>{{ $task->department?->name }}</td>
                                <td>{{ $task->Post?->name }}</td>
                                <td class="text-center">{{ $task->Channel?->name }}</td>
                                <td class="text-end">{{ number_format($task->gross_cost,0,',','.') }}</td>
                                <td class="text-end">{{ $task->rate }}%</td>
                                <td class="text-end">{{ number_format($task->net_cost,0,',','.') }}</td>
                                <td class="text-center">
                                    <button
  type="button"
  class="btn btn-sm update-paid {{ $task->paid ? 'btn-success' : 'btn-secondary' }}"
  data-url="{{ route('tasks.updatePaid', $task->id) }}"
>
  {{ $task->paid ? 'Đã thanh toán' : 'Chưa thanh toán' }}
</button>


                                </td>
                                <td>{{ $task->content }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>


