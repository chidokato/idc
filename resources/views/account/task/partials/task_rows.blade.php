@forelse($tasks as $task)
@php
    // dùng lại cách ép số giống controller (nhanh gọn ở đây)
    $cost = (float) preg_replace('/[^\d\-]/', '', (string)($task->expected_costs ?? 0));
    $days = (float) preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', (string)($task->days ?? 0)));
    $rate = (float) preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', (string)($task->rate ?? 0)));
    $rowTotal = $cost * $days;
    $rowPaid  = $rowTotal * (1 - $rate/100);
  @endphp
  <tr>
    <td>
      @if($task->approved)
          <span class="badge bg-success">Đã duyệt</span>
      @else
          <span class="badge bg-warning">Chờ duyệt</span>
      @endif
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

    <td>{{ $task->content ?? '' }}</td>
  </tr>
@empty
  <tr>
    <td colspan="11" class="text-center text-muted py-4">Không có dữ liệu phù hợp</td>
  </tr>
@endforelse


