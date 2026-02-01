@extends('admin.layout.AdminLayout')

@section('AdminContent')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 font-weight-bold">Quản lý biến thể sản phẩm</h2>
    <a href="{{ route('variants.create') }}" class="btn btn-primary shadow-sm px-4">
        <i class="fa fa-plus"></i> Thêm biến thể
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
@endif

<div class="card shadow-lg border-0">
    <div class="card-body p-0">

        @forelse($sanPhams as $sp)
        <div class="border-bottom">

            {{-- HEADER SẢN PHẨM --}}
            <div class="d-flex justify-content-between align-items-center p-4 bg-white">

                <div class="d-flex align-items-center">
                    <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                         width="80" height="80"
                         style="object-fit:cover; border-radius:10px;"
                         class="shadow-sm mr-3">

                    <div>
                        <div class="font-weight-bold" style="font-size: 18px;">
                            {{ $sp->ten_san_pham }}
                        </div>

                        <div class="text-muted small">
                            Danh mục: {{ $sp->danhMuc->ten_danh_muc ?? '-' }}
                        </div>

                        <div class="mt-1">
                            <span class="badge badge-info px-3 py-1">
                                {{ $sp->variants->count() }} biến thể
                            </span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('variants.create', ['san_pham_id' => $sp->id]) }}"
                   class="btn btn-success btn-sm shadow px-3">
                    <i class="fa fa-plus-circle"></i> Thêm biến thể
                </a>

            </div>

            {{-- BẢNG BIẾN THỂ --}}
            @if($sp->variants->count() > 0)
            <div class="table-responsive px-3 pb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Màu</th>
                            <th>Size</th>
                            <th>Giá</th>
                            <th>Kho</th>
                            <th>Trạng thái</th>
                            <th width="150">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sp->variants as $v)
                        <tr>
                            <td>{{ $v->color->ten_mau ?? '-' }}</td>
                            <td>{{ $v->size->ten_kich_thuoc ?? '-' }}</td>
                            <td class="font-weight-bold text-danger">
                                {{ number_format($v->gia ?? 0) }}đ
                            </td>
                            <td>{{ $v->so_luong }}</td>
                            <td>
                                @if($v->trang_thai)
                                    <span class="badge badge-success px-3">Hiện</span>
                                @else
                                    <span class="badge badge-secondary px-3">Ẩn</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('variants.edit', $v->id) }}"
                                   class="btn btn-warning btn-sm">
                                   <i class="fa fa-edit"></i>
                                </a>

                                <form action="{{ route('variants.delete', $v->id) }}"
                                      method="POST"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Xóa biến thể này?')">
                                            <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-4 text-center text-muted small">
                Chưa có biến thể —
                <a href="{{ route('variants.create', ['san_pham_id' => $sp->id]) }}" class="font-weight-bold">
                    Thêm biến thể đầu tiên
                </a>
            </div>
            @endif

        </div>
        @empty
        <div class="alert alert-info m-4">
            Chưa có sản phẩm nào. Vui lòng thêm sản phẩm trước khi tạo biến thể.
        </div>
        @endforelse

    </div>
</div>

@endsection
