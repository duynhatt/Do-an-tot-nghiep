@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="container-fluid" style="margin-top: 30px;">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary" data-toggle="modal" style="margin-bottom:20px;" data-target="#modalAdd">
            <i class="fas fa-plus"></i> Thêm sản phẩm
        </button>
    </div>

    {{-- Table --}}
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover text-center">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Trạng thái</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sanPhams as $key => $sp)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            @if($sp->hinh_anh_chinh)
                            <img src="{{ asset('storage/' . $sp->hinh_anh_chinh) }}"
                                alt="{{ $sp->ten_san_pham }}"
                                style="max-width:60px; height:auto; border-radius:4px;">
                            @else
                            <span class="text-muted">Chưa có ảnh</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $sp->ten_san_pham }}</td>
                        <td>{{ $sp->danhMuc->ten_danh_muc ?? '—' }}</td>
                        <td>
                            @if($sp->trang_thai)
                            <span class="badge badge-success">Hiển thị</span>
                            @else
                            <span class="badge badge-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-edit"
                                data-id="{{ $sp->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete"
                                data-id="{{ $sp->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">Chưa có sản phẩm nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ================= MODAL THÊM SẢN PHẨM ================= --}}
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog modal-lg">
        <form id="formAdd" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sản phẩm mới</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" name="ten_san_pham" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Danh mục <span class="text-danger">*</span></label>
                                <select name="danh_muc_id" class="form-control" required>
                                    <option value="">--- Chọn danh mục ---</option>
                                    @foreach($danhMucs as $dm)
                                    <option value="{{ $dm->id }}">{{ $dm->ten_danh_muc }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Mô tả ngắn</label>
                                <textarea name="mo_ta_ngan" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Mô tả chi tiết</label>
                                <textarea name="mo_ta_chi_tiet" class="form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Hình ảnh chính</label>
                                <input type="file" name="hinh_anh_chinh" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">jpg, png, gif - tối đa 2MB</small>
                            </div>

                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="trang_thai" class="form-control">
                                    <option value="1" selected>Hiển thị</option>
                                    <option value="0">Ẩn</option>
                                </select>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" name="cho_phep_thiet_ke" value="1" class="form-check-input">
                                <label class="form-check-label">Cho phép tùy chỉnh / thiết kế</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL SỬA SẢN PHẨM ================= --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog modal-lg">
        <form id="formEdit" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa sản phẩm</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" id="edit_ten_san_pham" name="ten_san_pham" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Danh mục <span class="text-danger">*</span></label>
                                <select id="edit_danh_muc_id" name="danh_muc_id" class="form-control" required>
                                    <option value="">--- Chọn danh mục ---</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Mô tả ngắn</label>
                                <textarea id="edit_mo_ta_ngan" name="mo_ta_ngan" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Mô tả chi tiết</label>
                                <textarea id="edit_mo_ta_chi_tiet" name="mo_ta_chi_tiet" class="form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Hình ảnh hiện tại</label>
                                <div id="current_image" class="mb-2"></div>
                                <label>Thay hình ảnh mới (nếu muốn)</label>
                                <input type="file" name="hinh_anh_chinh" class="form-control-file" accept="image/*">
                            </div>

                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select id="edit_trang_thai" name="trang_thai" class="form-control">
                                    <option value="1">Hiển thị</option>
                                    <option value="0">Ẩn</option>
                                </select>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" name="cho_phep_thiet_ke" value="1" id="edit_cho_phep_thiet_ke" class="form-check-input">
                                <label class="form-check-label" for="edit_cho_phep_thiet_ke">Cho phép tùy chỉnh / thiết kế</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
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
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.san-pham.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Thêm sản phẩm thành công!');
                        $('#modalAdd').modal('hide');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        toastr.error(res.message || 'Có lỗi xảy ra');
                    }
                },
                error: function() {
                    toastr.error('Lỗi kết nối server');
                }
            });
        });

        $('.btn-edit').click(function() {
            let id = $(this).data('id');

            $.get("{{ url('admin/san-pham') }}/" + id + "/edit", function(res) {
                if (res.status) {
                    let sp = res.data;

                    $('#edit_id').val(sp.id);
                    $('#edit_ten_san_pham').val(sp.ten_san_pham);
                    $('#edit_mo_ta_ngan').val(sp.mo_ta_ngan);
                    $('#edit_mo_ta_chi_tiet').val(sp.mo_ta_chi_tiet);
                    $('#edit_trang_thai').val(sp.trang_thai ? 1 : 0);
                    $('#edit_cho_phep_thiet_ke').prop('checked', sp.cho_phep_thiet_ke);

                    let select = $('#edit_danh_muc_id');
                    select.empty();
                    select.append('<option value="">--- Chọn danh mục ---</option>');
                    res.danh_mucs.forEach(dm => {
                        let option = `<option value="${dm.id}" ${dm.id == sp.danh_muc_id ? 'selected' : ''}>${dm.ten_danh_muc}</option>`;
                        select.append(option);
                    });

                    let imgHtml = sp.hinh_anh_chinh ?
                        `<img src="{{ asset('storage') }}/${sp.hinh_anh_chinh}" style="max-width:140px; border-radius:6px;">` :
                        '<span class="text-muted">Chưa có ảnh</span>';
                    $('#current_image').html(imgHtml);

                    $('#modalEdit').modal('show');
                } else {
                    toastr.error('Không tìm thấy sản phẩm');
                }
            }).fail(() => toastr.error('Lỗi tải thông tin sản phẩm'));
        });

        $('#formEdit').submit(function(e) {
            e.preventDefault();
            let id = $('#edit_id').val();
            let formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: "{{ url('admin/san-pham') }}/" + id,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Cập nhật thành công!');
                        $('#modalEdit').modal('hide');
                        setTimeout(() => location.reload(), 1200);
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
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;

            let id = $(this).data('id');

            $.ajax({
                url: "{{ url('admin/san-pham') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Xóa sản phẩm thành công!');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        toastr.error(res.message || 'Không thể xóa sản phẩm');
                    }
                },
                error: function() {
                    toastr.error('Lỗi khi xóa');
                }
            });
        });

    });
</script>
@endsection