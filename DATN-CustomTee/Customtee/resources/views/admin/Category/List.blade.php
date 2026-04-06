@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="container-fluid" style="margin-top: 30px;">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary" data-toggle="modal" style="margin-bottom:20px;" data-target="#modalAdd">
            <i class="fas fa-plus"></i> Thêm danh mục
        </button>
    </div>

    <form method="GET" action="{{ route('admin.danh-muc.index') }}" class="mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Tên danh mục</label>
                        <input type="text" name="keyword" class="form-control"
                            placeholder="Nhập tên danh mục..."
                            value="{{ request('keyword') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="font-weight-bold">Trạng thái</label>
                        <select name="trang_thai" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex flex-column">
                        <label class="font-weight-bold invisible">Action</label>
                        <div class="d-flex">
                            <button class="btn btn-primary w-50 mr-2">
                                <i class="fas fa-search"></i> Tìm
                            </button>
                            <a href="{{ route('admin.danh-muc.index') }}" class="btn btn-outline-secondary w-50">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
    <br>

    {{-- Table --}}
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover text-center">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th width="15%">Ảnh</th>
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
                            <img
                                src="{{ $dm->hinh_anh ? asset('storage/' . $dm->hinh_anh) : asset('img/shop_01.jpg') }}"
                                alt="{{ $dm->ten_danh_muc }}"
                                style="width: 90px; height: 60px; object-fit: cover; border-radius: 6px;"
                            >
                        </td>
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
                        <td colspan="6">Chưa có danh mục nào</td>
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
                        <label>Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="ten_danh_muc" class="form-control" required maxlength="255" placeholder="Nhập tên danh mục">
                        <small class="text-muted">Tối đa 255 ký tự, không được trùng với danh mục khác</small>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="3" maxlength="1000" placeholder="Mô tả danh mục (tùy chọn)"></textarea>
                        <small class="text-muted">Tối đa 1000 ký tự</small>
                    </div>

                    <div class="form-group">
                        <label>Ảnh danh mục</label>
                        <input type="file" name="hinh_anh" class="form-control-file" accept="image/*">
                        <small class="text-muted">JPG/PNG/WebP, tối đa 2MB (tùy chọn)</small>
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
                        <label>Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="ten_danh_muc" id="edit_ten_danh_muc" class="form-control" required maxlength="255" placeholder="Nhập tên danh mục">
                        <small class="text-muted">Tối đa 255 ký tự, không được trùng với danh mục khác</small>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="mo_ta" id="edit_mo_ta" class="form-control" rows="3" maxlength="1000" placeholder="Mô tả danh mục (tùy chọn)"></textarea>
                        <small class="text-muted">Tối đa 1000 ký tự</small>
                    </div>

                    <div class="form-group">
                        <label>Ảnh danh mục</label>
                        <div class="mb-2">
                            <img
                                id="edit_category_image_preview"
                                src="{{ asset('img/shop_01.jpg') }}"
                                alt="Ảnh danh mục"
                                style="width: 130px; height: 80px; object-fit: cover; border-radius: 6px;"
                            >
                        </div>
                        <input type="file" id="edit_hinh_anh" name="hinh_anh" class="form-control-file" accept="image/*">
                        <small class="text-muted">Tùy chọn (nếu không chọn sẽ giữ ảnh cũ)</small>
                    </div>

                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="edit_trang_thai" class="form-control">
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

        // Hiển thị lỗi validation từ server (422)
        function showValidationErrors(xhr) {
            if (xhr.status === 422 && xhr.responseJSON) {
                const data = xhr.responseJSON;
                if (data.errors) {
                    Object.keys(data.errors).forEach(function(field) {
                        toastr.error(data.errors[field][0]);
                    });
                    return true;
                }
                if (data.message) {
                    toastr.error(data.message);
                    return true;
                }
            }
            return false;
        }

        $('#formAdd').submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.danh-muc.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Thêm danh mục thành công!');
                        $('#modalAdd').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(res.message || 'Có lỗi xảy ra khi thêm danh mục');
                    }
                },
                error: function(xhr) {
                    if (!showValidationErrors(xhr)) {
                        toastr.error('Lỗi kết nối server');
                    }
                }
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

                    const placeholderImg = '{{ asset('img/shop_01.jpg') }}';
                    const storageBase = '{{ asset('storage') }}';
                    $('#edit_category_image_preview').attr(
                        'src',
                        res.data.hinh_anh ? (storageBase + '/' + res.data.hinh_anh) : placeholderImg
                    );

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

            let formData = new FormData($('#formEdit')[0]);
            // Laravel update route expects PUT; use method spoof to keep multipart upload ổn định.
            formData.append('_method', 'PUT');

            $.ajax({
                url: "{{ url('admin/danh-muc') }}/" + id,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message || 'Cập nhật thành công!');
                        $('#modalEdit').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(res.message || 'Có lỗi khi cập nhật');
                    }
                },
                error: function(xhr) {
                    if (!showValidationErrors(xhr)) {
                        toastr.error('Lỗi kết nối server');
                    }
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
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else if (!showValidationErrors(xhr)) {
                        toastr.error('Lỗi khi xóa danh mục');
                    }
                }
            });
        });

    });
</script>
@endsection