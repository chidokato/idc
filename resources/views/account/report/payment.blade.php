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
                <div class="table-responsive-mobile">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>Đơn vị</th>
                                <th class="text-end">Chi phí dự kiến</th>
                                <th class="text-end">Tiền hỗ trợ</th>
                                <th class="text-end">Chi phí ròng</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($ctys as $cty)
                            @php $ctyTotal = sumDepartmentCost($cty->id, $taskByDepartment); @endphp

                            {{-- CTY --}}
                            <tr class="fw-bold bg-light">
                                <td>{{ $cty->name }}</td>
                                <td class="text-end">{{ number_format($ctyTotal['gross']) }}</td>
                                <td class="text-end text-success">{{ number_format($ctyTotal['support']) }}</td>
                                <td class="text-end">{{ number_format($ctyTotal['net']) }}</td>
                            </tr>

                            {{-- SÀN --}}
                            @foreach($cty->children as $san)
                                @php $sanTotal = sumDepartmentCost($san->id, $taskByDepartment); @endphp

                                <tr>
                                    <td class="ps-4 fw-bold">— {{ $san->name }}</td>
                                    <td class="text-end">{{ number_format($sanTotal['gross']) }}</td>
                                    <td class="text-end text-success">{{ number_format($sanTotal['support']) }}</td>
                                    <td class="text-end">{{ number_format($sanTotal['net']) }}</td>
                                </tr>

                                {{-- PHÒNG --}}
                                @foreach($san->children as $phong)
                                    @php $phongTotal = sumDepartmentCost($phong->id, $taskByDepartment); @endphp

                                    <tr>
                                        <td class="ps-5">—— {{ $phong->name }}</td>
                                        <td class="text-end">{{ number_format($phongTotal['gross']) }}</td>
                                        <td class="text-end text-success">{{ number_format($phongTotal['support']) }}</td>
                                        <td class="text-end">{{ number_format($phongTotal['net']) }}</td>
                                    </tr>

                                    {{-- USER --}}
                                    @foreach($usersByDepartment[$phong->id] ?? [] as $user)
                                        @php
                                            $userTotal = $taskByUser[$user->id] ?? null;
                                        @endphp

                                        @if($userTotal)
                                            <tr class="text-muted">
                                                <td class="ps-6 fw-bold">——— {{ $user->yourname }}</td>
                                                <td class="text-end">{{ number_format($userTotal->gross_cost) }}</td>
                                                <td class="text-end text-success">
                                                    {{ number_format($userTotal->gross_cost - $userTotal->net_cost) }}
                                                </td>
                                                <td class="text-end">{{ number_format($userTotal->net_cost) }}</td>
                                            </tr>

                                            {{-- TASK --}}
                                            @foreach($tasks[$phong->id][$user->id] ?? [] as $task)
                                                <tr class="text-secondary">
                                                    <td class="ps-7">{{ $task->Post->name }}</td>
                                                    <td class="text-end">
                                                        {{ number_format($task->days * $task->expected_costs) }}
                                                    </td>
                                                    <td class="text-end text-success">
                                                        {{ number_format(
                                                            ($task->days * $task->expected_costs)
                                                            - ($task->days * $task->expected_costs * (1 - $task->rate / 100))
                                                        ) }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ number_format(
                                                            $task->days * $task->expected_costs * (1 - $task->rate / 100)
                                                        ) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
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

@endsection