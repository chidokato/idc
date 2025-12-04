<div class="table-responsive-mobile">
    
<table class="table">
    <thead>
        <tr>
             <th>Đóng/mở</th>
            <th>Tên</th>
            <th>Thời gian</th>
            <th>Số ngày</th>
           
        </tr>
    </thead>
    <tbody>
        @foreach($reports as $r)
        <tr data-id="{{ $r->id }}">
            <td>
                <label class="switch">
                    <input type="checkbox" class="active-toggle" data-id="{{ $r->id }}" {{ $r->active ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>

            </td>
            <td class="r-name"><a href="account/report/{{ $r->id }}">{{ $r->name }}</a></td>
            <td class="r-date">{{ \Carbon\Carbon::parse($r->time_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($r->time_end)->format('d/m/Y') }}</td>
            <td>{{ $r->days }}</td>
            
            <td>
                <button class="edit btn btn-warning">Sửa</button>
                <button class="del btn btn-danger" data-id="{{ $r->id }}">Xóa</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>