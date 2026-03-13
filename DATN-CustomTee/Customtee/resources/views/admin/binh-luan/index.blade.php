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
                <th>Nội dung</th>
                <th>Số sao</th>
                <th>Trạng thái</th>
                <th>Ngày</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>

        <tbody>

        @foreach($binhLuans as $bl)

            <tr>
                <td>{{ $bl->id }}</td>

                <td>{{ $bl->user->name ?? 'N/A' }}</td>

                <td>{{ $bl->sanPham->ten_san_pham ?? 'N/A' }}</td>

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

                <td>{{ $bl->created_at->format('d/m/Y') }}</td>

                <td>

                    {{-- Ẩn / Hiện --}}
                    <a href="{{ route('admin.binh-luan.toggle',$bl->id) }}"
                       class="btn btn-warning btn-sm">
                        Ẩn/Hiện
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