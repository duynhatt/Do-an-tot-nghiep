@extends('admin.layout.layout')

@section('content')
<div class="container-fluid" style="margin-top: 30px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Quản lý màu sắc</h4>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAdd">
            <i class="fas fa-plus"></i> Thêm màu sắc
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover text-center">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Tên màu</th>
                        <th>Mã màu</th>
                        <th>Màu xem trước</th>
                        <th>Trạng thái</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mauSacs as $key => $mau)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $mau->ten_mau }}</td>
                        <td>{{ $mau->ma_mau ?? '—' }}</td>
                        <td>
                            @if($mau->ma_mau)
                            <div style="width: 30px; height: 30px; background-color: {{ $mau->ma_mau }}; border: 1px solid #ddd; border-radius: 4px; margin: 0 auto;"></div>
                            @else
                            —
                            @endif
                        </td>
                        <td>
                            @if($mau->trang_thai)
                            <span class="badge badge-success">Hiển thị</span>
                            @else
                            <span class="badge badge-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $mau->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $mau->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">Chưa có màu sắc nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL ADD -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog">
        <form id="formAdd">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm màu sắc</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên màu</label>
                        <input type="text" name="ten_mau" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Mã màu (ví dụ: #FF0000)</label>
                        <input type="color" name="ma_mau" class="form-control" placeholder="#RRGGBB">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" class="form-control">
                            <option value="1">Hiển thị</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <form id="formEdit">
            @csrf
            <input type="hidden" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa màu sắc</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên màu</label>
                        <input type="text" id="edit_ten_mau" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Mã màu</label>
                        <input type="color" id="edit_ma_mau" class="form-control" placeholder="#RRGGBB">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select id="edit_trang_thai" class="form-control">
                            <option value="1">Hiển thị</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Cập nhật</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {

        $('#formAdd').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('admin.mau-sac.store') }}", $(this).serialize(), function(res) {
                if (res.status) {
                    toastr.success(res.message);
                    $('#modalAdd').modal('hide');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(res.message || 'Có lỗi xảy ra');
                }
            }).fail(() => toastr.error('Lỗi kết nối server'));
        });

        $('.btn-edit').click(function() {
            let id = $(this).data('id');
            $.get("{{ url('admin/mau-sac') }}/" + id, function(res) {
                if (res.status) {
                    $('#edit_id').val(res.data.id);
                    $('#edit_ten_mau').val(res.data.ten_mau);
                    $('#edit_ma_mau').val(res.data.ma_mau);
                    $('#edit_trang_thai').val(res.data.trang_thai ? 1 : 0);
                    $('#modalEdit').modal('show');
                } else {
                    toastr.error('Không tìm thấy màu sắc');
                }
            }).fail(() => toastr.error('Lỗi tải dữ liệu'));
        });

        $('#formEdit').submit(function(e) {
            e.preventDefault();

            let id = $('#edit_id').val();

            $.ajax({
                url: "{{ url('admin/mau-sac') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "PUT", 
                    ten_mau: $('#edit_ten_mau').val(),
                    ma_mau: $('#edit_ma_mau').val(),
                    trang_thai: $('#edit_trang_thai').val(),
                },
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Cập nhật màu sắc thành công!');
                        $('#modalEdit').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(res.message || 'Có lỗi xảy ra');
                    }
                },
                error: function() {
                    toastr.error('Lỗi kết nối server');
                }
            });
        });


        $('.btn-delete').click(function() {
            if (!confirm('Bạn có chắc muốn xóa màu này?')) return;
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('admin/mau-sac') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(res.message || 'Không thể xóa');
                    }
                },
                error: () => toastr.error('Lỗi khi xóa')
            });
        });

    });
</script>
@endsection