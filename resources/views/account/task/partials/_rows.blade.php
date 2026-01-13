@forelse($tasks as $task)
  @php
    $expected = (float)($task->expected_costs ?? 0);
    $days     = (float)($task->days ?? 0);
    $rate     = (float)($task->rate ?? 0);

    $rowTotal = $expected * $days;
    $rowPaid  = $rowTotal * (1 - $rate/100);

    $paid     = (int)($task->paid ?? 0);
    $actual   = (float)($task->actual_costs ?? 0);
    $hold     = $rowPaid;
  @endphp

  <tr id="row-{{ $task->id }}">
    <td>
      @if($task->approved == 1)
        <span class="badge btn-success">Duyệt</span>
      @else
        <span class="badge btn-danger">Không</span>
      @endif
    </td>

    <td>{{ $task->handler?->employee_code }}</td>
    <td>{{ $task->handler?->yourname }}</td>
    <td>{{ $task->department?->name }}</td>
    <td>{{ $task->Post?->name }}</td>
    <td>{{ $task->channel?->name ?? $task->channel ?? '' }}</td>

    <td class="text-end">{{ number_format($rowTotal, 0, ',', '.') }}</td>

    <td class="text-end">
      <div class="note {{ $paid == 1 ? 'text-success' : 'text-danger' }}"
           data-toggle="tooltip" data-placement="left"
           data-original-title="{{ (int)$rate }}%">
        {{ number_format($rowPaid, 0, ',', '.') }}
      </div>
    </td>

    <td>
      <input
        style="width: 120px;"
        class="form-control actual-cost-input"
        type="text"
        name="actual_costs"
        value="{{ number_format((int)$task->actual_costs, 0, ',', '.') }}"
        data-task-id="{{ $task->id }}"
        data-last="{{ (int)($task->actual_costs ?? 0) }}"
        data-url="{{ route('tasks.ajaxUpdateActualCosts', $task) }}"
        placeholder="Nhập..."
      >
    </td>

    <td class="text-end">
      <span class="js-refund-money text-success">
        {{ number_format((float)$task->refund_money, 0, ',', '.') }}
      </span>
    </td>

    <td class="text-end">
      <span class="js-extra-money text-danger">
        {{ number_format((float)$task->extra_money, 0, ',', '.') }}
      </span>
    </td>

    <td>
      <div style="width: 200px;" class="note" data-toggle="tooltip" data-placement="top"
           data-original-title="{{ $task->content ?? '' }}">
        {{ $task->content ?? '' }}
      </div>
    </td>

    <td>
    </td>
  </tr>

@empty
  <tr>
    <td colspan="12" class="text-center text-muted py-4">Không có dữ liệu phù hợp</td>
  </tr>
@endforelse
