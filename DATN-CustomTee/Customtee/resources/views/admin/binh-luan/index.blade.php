@extends('admin.layout.AdminLayout')

@section('AdminContent')

<div class="container">

    <h2 class="mb-4">Danh sách bình luận</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>ID</th>
                <th>Người dùng</th>
                <th>Sản phẩm</th>
                <th>Biến thể</th>
                <th>Nội dung</th>
                <th>Số sao</th>
                <th>Trạng thái</th>
                <th>Trang chủ</th>
                <th>Ngày</th>
                <th width="260">Hành động</th>
            </tr>
        </thead>

        <tbody>

        @foreach($binhLuans as $bl)

            <tr>
                <td>{{ $bl->id }}</td>

                <td>{{ $bl->user->name ?? 'N/A' }}</td>

                <td>{{ $bl->sanPham->ten_san_pham ?? 'N/A' }}</td>

                <td>
                    @if($bl->bienThe)
                        @if($bl->bienThe->color)
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="border rounded" style="width:16px;height:16px;background-color:{{ $bl->bienThe->color->ma_mau ?? '#ccc' }};"></span>
                                {{ $bl->bienThe->color->ten_mau ?? '—' }}
                            </span>
                        @endif
                        @if($bl->bienThe->size)
                            <span class="ms-2 text-muted">/ {{ $bl->bienThe->size->ten_kich_thuoc ?? '—' }}</span>
                        @endif
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <td>{{ $bl->noi_dung }}</td>

                <td>
                    @for($i=1; $i<=5; $i++)
                        @if($i <= $bl->so_sao)
                            ⭐
                        @else
                            ☆
                        @endif
                    @endfor
                </td>

                <td>
                    @if($bl->trang_thai)
                        <span class="badge bg-success">Hiển thị</span>
                    @else
                        <span class="badge bg-danger">Ẩn</span>
                    @endif
                </td>

                <td>
                    @if($bl->hien_thi_trang_chu)
                        <span class="badge bg-primary">Hiển thị trang chủ</span>
                    @else
                        <span class="badge bg-secondary">Không hiển thị</span>
                    @endif
                </td>

                <td>{{ $bl->created_at->format('d/m/Y') }}</td>

                <td>
                    <a href="{{ route('admin.binh-luan.toggle',$bl->id) }}"
                       class="btn btn-warning btn-sm mb-1">
                        Ẩn/Hiện
                    </a>
                    <a href="{{ route('admin.binh-luan.toggle-home',$bl->id) }}"
                       class="btn btn-info btn-sm mb-1 {{ !$bl->trang_thai ? 'disabled' : '' }}"
                       @if(!$bl->trang_thai) aria-disabled="true" @endif>
                        Hiển thị trang chủ
                    </a>
                </td>
            </tr>

        @endforeach

        </tbody>

    </table>

    {{-- Pagination --}}
    {{ $binhLuans->links() }}

</div>

@endsection