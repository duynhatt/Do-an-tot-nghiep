@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="container-fluid" style="margin-top: 30px;">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary" data-toggle="modal" style="margin-bottom:20px;" data-target="#modalAdd">
            <i class="fas fa-plus"></i> Thêm danh mục
        </button>
    </div>

    {{-- Table --}}
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover text-center">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Trạng thái</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhMucs as $key => $dm)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $dm->ten_danh_muc }}</td>
                        <td>{{ $dm->slug }}</td>
                        <td>
                            @if($dm->trang_thai == 1)
                            <span class="badge badge-success">Hiển thị</span>
                            @else
                            <span class="badge badge-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-edit"
                                data-id="{{ $dm->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete"
                                data-id="{{ $dm->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">Chưa có danh mục nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ================= MODAL ADD ================= --}}
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog">
        <form id="formAdd">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm danh mục</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên danh mục</label>
                        <input type="text" name="ten_danh_muc" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="3"></textarea>
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

{{-- ================= MODAL EDIT ================= --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <form id="formEdit">
            @csrf
            <input type="hidden" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa danh mục</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên danh mục</label>
                        <input type="text" id="edit_ten_danh_muc" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea id="edit_mo_ta" class="form-control" rows="3"></textarea>
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

            $.post("{{ route('admin.danh-muc.store') }}", $(this).serialize(), function(res) {
                if (res.status) {
                    toastr.success(res.message || 'Thêm danh mục thành công!');
                    $('#modalAdd').modal('hide');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(res.message || 'Có lỗi xảy ra khi thêm danh mục');
                }
            }).fail(function() {
                toastr.error('Lỗi kết nối server');
            });
        });

        $('.btn-edit').click(function() {
            let id = $(this).data('id');

            $.get("{{ url('admin/danh-muc') }}/" + id, function(res) {
                if (res.status) {
                    $('#edit_id').val(res.data.id);
                    $('#edit_ten_danh_muc').val(res.data.ten_danh_muc);
                    $('#edit_mo_ta').val(res.data.mo_ta);
                    $('#edit_trang_thai').val(res.data.trang_thai);

                    $('#modalEdit').modal('show');
                } else {
                    toastr.error('Không tìm thấy danh mục');
                }
            }).fail(function() {
                toastr.error('Lỗi khi tải thông tin danh mục');
            });
        });

        $('#formEdit').submit(function(e) {
            e.preventDefault();

            let id = $('#edit_id').val();

            $.ajax({
                url: "{{ url('admin/danh-muc') }}/" + id,
                type: "PUT", // 👈 PUT chuẩn resource
                data: {
                    _token: "{{ csrf_token() }}",
                    ten_danh_muc: $('#edit_ten_danh_muc').val(),
                    mo_ta: $('#edit_mo_ta').val(),
                    trang_thai: $('#edit_trang_thai').val(),
                },
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Cập nhật thành công!');
                        $('#modalEdit').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(res.message || 'Có lỗi khi cập nhật');
                    }
                },
                error: function() {
                    toastr.error('Lỗi kết nối server');
                }
            });
        });


        $('.btn-delete').click(function() {
            if (!confirm('Bạn có chắc muốn xóa danh mục này?')) return;

            let id = $(this).data('id');

            $.ajax({
                url: "{{ url('admin/danh-muc') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Xóa danh mục thành công!');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(res.message || 'Không thể xóa danh mục');
                    }
                },
                error: function() {
                    toastr.error('Lỗi khi xóa danh mục');
                }
            });
        });

    });
</script>
@endsection