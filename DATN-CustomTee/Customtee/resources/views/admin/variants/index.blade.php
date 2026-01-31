@extends('admin.layout.AdminLayout')

@section('AdminContent')
<table class="table">
    <a href="{{ route('variants.create') }}" class="btn btn-primary mb-3">
    + Thêm biến thể
</a>

    <thead>
        <tr>
            <th>Ảnh</th>
            <th>Danh mục</th>
            <th>Sản phẩm</th>
            <th>Màu</th>
            <th>Size</th>
            <th>Giá</th>
            <th>Kho</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    @foreach($variants as $v)
        <tr>
            <<td>
    <img src="{{ asset('storage/' . $v->product->hinh_anh_chinh) }}"
         width="60"
         style="border-radius:6px">
</td>




{{-- <td>{{ $v->product->ten_san_pham }}</td> --}}

<td>{{ $v->product->category->ten_danh_muc }}</td>

            <td>{{ $v->product->ten_san_pham }}</td>
            <td>{{ $v->color->ten_mau }}</td>
            <td>{{ $v->size->ten_kich_thuoc }}</td>
            <td>{{ number_format($v->gia) }}</td>
            <td>{{ $v->so_luong }}</td>
            <td>{{ $v->trang_thai ? 'Hiện' : 'Ẩn' }}</td>
           <td>
    <a href="{{ route('variants.edit', $v->id) }}" class="btn btn-warning btn-sm">Sửa</a>

        <form action="{{ route('variants.delete', $v->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm"
                onclick="return confirm('Xóa biến thể này?')">Xoá</button>
         </form>
</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection

