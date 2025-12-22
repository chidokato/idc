@forelse($tasks as $task)
  <tr>
    <td class="table-column-pr-0">
      <div class="custom-control custom-checkbox">
        <input id="datatableCheck{{ $task->id }}" type="checkbox"
               class="custom-control-input row-check" value="{{ $task->id }}">
        <label class="custom-control-label" for="datatableCheck{{ $task->id }}"></label>
      </div>
    </td>

    <td>{{ $task->handler?->employee_code }}</td>
    <td>{{ $task->handler?->yourname }}</td>
    <td>{{ $task->department?->name }}</td>
    <td>{{ $task->Post?->name }}</td>
    <td>{{ $task->channel?->name ?? $task->channel ?? '' }}</td>

    <td class="text-end">
      {{ number_format((float)($task->expected_costs * $task->days), 0, ',', '.') }}
    </td>

    <td class="text-end">{{ $task->rate }}%</td>

    <td class="text-end">
      {{ number_format((float)(($task->expected_costs * $task->days) * (1 - $task->rate/100)), 0, ',', '.') }}
    </td>

    <td>
      @if(($task->paid ?? 0) == 1)
        <span class="badge badge-soft-success">Đã đóng</span>
      @else
        <span class="badge badge-soft-warning">Chưa đóng</span>
      @endif
    </td>

    <td>{{ $task->note ?? '' }}</td>
  </tr>
@empty
  <tr>
    <td colspan="11" class="text-center text-muted py-4">Không có dữ liệu phù hợp</td>
  </tr>
@endforelse
