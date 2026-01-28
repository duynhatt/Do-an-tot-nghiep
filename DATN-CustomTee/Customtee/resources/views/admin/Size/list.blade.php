@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="container-fluid" style="margin-top: 30px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary" data-toggle="modal" style="margin-bottom:20px;" data-target="#modalAdd">
            <i class="fas fa-plus"></i> Thêm kích thước
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover text-center">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Tên kích thước</th>
                        <th>Trạng thái</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kichThuocs as $key => $kt)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $kt->ten_kich_thuoc }}</td>
                        <td>
                            @if($kt->trang_thai)
                            <span class="badge badge-success">Hiển thị</span>
                            @else
                            <span class="badge badge-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $kt->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $kt->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">Chưa có kích thước nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="modalAdd">
    <div class="modal-dialog">
        <form id="formAdd">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm kích thước</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên kích thước (S, M, L, 38, 39...)</label>
                        <input type="text" name="ten_kich_thuoc" class="form-control" required>
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

<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <form id="formEdit">
            @csrf
            <input type="hidden" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa kích thước</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên kích thước</label>
                        <input type="text" id="edit_ten_kich_thuoc" class="form-control" required>
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

<script>
    $(function() {

        $('#formAdd').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('admin.kich-thuoc.store') }}", $(this).serialize(), function(res) {
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
            $.get("{{ url('admin/kich-thuoc') }}/" + id, function(res) {
                if (res.status) {
                    $('#edit_id').val(res.data.id);
                    $('#edit_ten_kich_thuoc').val(res.data.ten_kich_thuoc);
                    $('#edit_trang_thai').val(res.data.trang_thai ? 1 : 0);
                    $('#modalEdit').modal('show');
                } else {
                    toastr.error('Không tìm thấy kích thước');
                }
            }).fail(() => toastr.error('Lỗi tải dữ liệu'));
        });

        $('#formEdit').submit(function(e) {
            e.preventDefault();

            let id = $('#edit_id').val();

            $.ajax({
                url: "{{ url('admin/kich-thuoc') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "PUT",
                    ten_kich_thuoc: $('#edit_ten_kich_thuoc').val(),
                    trang_thai: $('#edit_trang_thai').val(),
                },
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Cập nhật thành công!');
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
            if (!confirm('Bạn có chắc muốn xóa kích thước này?')) return;
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('admin/kich-thuoc') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status) {
                        setTimeout(() => location.reload(), 1500);
                        toastr.success(res.message);
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