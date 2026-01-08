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

    $isCase2  = false;
    $isDanger = false;

    if ($paid !== 1) {
      $diff = $actual;
      $isDanger = true;
    } else {
      if ($actual <= $rowTotal) {
        $diff = ($rowTotal - $actual) * (1 - $rate/100);
      } else {
        $diff = ($actual - $rowTotal);
        $isCase2 = true;
        $isDanger = true;
      }
    }

    $diff = (int) round($diff);

    $showDiff = ($paid === 1) || ($actual > 0);
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
      <span class="js-actual-diff {{ $showDiff && $isDanger ? 'text-danger' : '' }}">
        {{ $showDiff ? number_format($diff, 0, ',', '.') : '' }}
      </span>
    </td>

    <!-- <td>
      <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$task->id}}">
        <input name="settlement" type="checkbox" class="toggle-switch-input" id="stocksCheckbox{{$task->id}}">
        <span class="toggle-switch-label">
          <span class="toggle-switch-indicator"></span>
        </span>
      </label>
    </td> -->

    <td>
      <div style="width: 200px;" class="note" data-toggle="tooltip" data-placement="top"
           data-original-title="{{ $task->content ?? '' }}">
        {{ $task->content ?? '' }}
      </div>
    </td>

    <td></td>
  </tr>

@empty
  <tr>
    <td colspan="12" class="text-center text-muted py-4">Không có dữ liệu phù hợp</td>
  </tr>
@endforelse
