<div class="table-responsive-mobile">
    
<table class="table">
    <thead>
        <tr>
             <th>Đóng/mở</th>
             <th>Khu vực cho: GĐ / admin / MKT</th>
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
            <td>
                <a href="account/report/{{ $r->id }}"><button class="btn-info btn">Duyệt MKT</button></a>
                <a href="account/report/{{ $r->id }}"><button class="btn-info btn">Chi phí thực tế</button></a>
                <a href="account/report/{{ $r->id }}"><button class="btn-info btn">Tất cả</button></a>
            </td>
            <td class="r-name"><strong>{{ $r->name }}</strong></td>
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