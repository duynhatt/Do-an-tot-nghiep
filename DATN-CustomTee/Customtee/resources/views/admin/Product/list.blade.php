@extends('admin.layout.AdminLayout')

@section('AdminContent')
    <div class="container-fluid" style="margin-top: 30px;">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" data-toggle="modal" style="margin-bottom:20px;" data-target="#modalAdd">
                <i class="fas fa-plus"></i> Thêm sản phẩm
            </button>
        </div>
<style>
#modalAdd .modal-dialog{
    
    width: 1269px;
}
   .form-group{
    margin-bottom:1px;
    
}
        #variantsSection .variant-row select[name$="[trang_thai]"] {
            min-width: 60px;
        }
    </style>
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
                            {{-- <th>màu</th>
                        <th>kích cỡ</th>
                        <th>số lượng</th>
                        <th>giá</th> --}}
                            <th>Trạng thái</th>
                            <th width="15%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sanPhams as $key => $sp)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if ($sp->hinh_anh_chinh)
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
                                    @if ($sp->trang_thai)
                                        <span class="badge badge-success">Hiển thị</span>
                                    @else
                                        <span class="badge badge-secondary">Ẩn</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('variants.create', ['san_pham_id' => $sp->id]) }}"
                                        class="btn btn-sm btn-info" title="Thêm biến thể">
                                        <i class="fas fa-palette"></i>
                                    </a>
                                    <a href="{{ route('variants.index', ['san_pham_id' => $sp->id]) }}"
                                        class="btn btn-sm btn-secondary" title="Xem biến thể">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $sp->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $sp->id }}">
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
                                        @foreach ($danhMucs as $dm)
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
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="mb-0">Biến thể ban đầu</label>
                                <div class="form-check">
                                    <input type="checkbox" id="enableInitialVariants" class="form-check-input">
                                    <label class="form-check-label" for="enableInitialVariants">Có biến thể ban đầu</label>
                                </div>
                            </div>
                            <small class="text-muted">Bật để thêm biến thể ngay khi tạo sản phẩm.</small>
                        </div>
                        <div id="variantsSection" class="form-group" style="display:none;">
                            <div class="row small text-muted mb-2">
                                <div class="col-md-3">Màu</div>
                                <div class="col-md-2">Size</div>
                                <div class="col-md-2">Giá</div>
                                <div class="col-md-2">Giá KM</div>
                                <div class="col-md-2">Số lượng</div>
                                <div class="col-md-1">Trạng thái</div>
                            </div>
                            <div id="productVariantsContainer" data-next-index="1">
                                <div class="variant-row" data-index="0">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select name="variants[0][mau_sac_id]" class="form-control form-control-sm"
                                                disabled>
                                                <option value="">-- Chọn màu --</option>
                                                @foreach ($colors as $c)
                                                    <option value="{{ $c->id }}">{{ $c->ten_mau }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select name="variants[0][kich_thuoc_id]" class="form-control form-control-sm"
                                                disabled>
                                                <option value="">-- Chọn size --</option>
                                                @foreach ($sizes as $s)
                                                    <option value="{{ $s->id }}">{{ $s->ten_kich_thuoc }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="variants[0][gia]"
                                                class="form-control form-control-sm" min="0" disabled>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="variants[0][gia_khuyen_mai]"
                                                class="form-control form-control-sm" min="0" disabled>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="variants[0][so_luong]"
                                                class="form-control form-control-sm" min="0" disabled>
                                        </div>
                                        <div class="col-md-1">
                                            <select name="variants[0][trang_thai]" class="form-control form-control-sm"
                                                disabled>
                                                <option value="1">Hiện</option>
                                                <option value="0">Ẩn</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 text-right">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger remove-variant">Xóa dòng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="addVariantRow" class="btn btn-outline-primary btn-sm mt-2">Thêm
                                dòng biến thể</button>
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
                                    <input type="text" id="edit_ten_san_pham" name="ten_san_pham"
                                        class="form-control" required>
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
                                    <input type="file" name="hinh_anh_chinh" class="form-control-file"
                                        accept="image/*">
                                </div>

                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select id="edit_trang_thai" name="trang_thai" class="form-control">
                                        <option value="1">Hiển thị</option>
                                        <option value="0">Ẩn</option>
                                    </select>
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
            function setVariantsEnabled(isEnabled) {
                $('#variantsSection').toggle(isEnabled);
                $('#variantsSection').find('select, input').prop('disabled', !isEnabled);
                $('#variantsSection').find(
                        'select[name$="[mau_sac_id]"], select[name$="[kich_thuoc_id]"], input[name$="[gia]"], input[name$="[so_luong]"]'
                        )
                    .prop('required', isEnabled);
            }

            function buildVariantRow(index) {
                return `
                <div class="variant-row" data-index="${index}">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="variants[${index}][mau_sac_id]" class="form-control form-control-sm" required>
                                <option value="">-- Chọn màu --</option>
                                @foreach ($colors as $c)
                                    <option value="{{ $c->id }}">{{ $c->ten_mau }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="variants[${index}][kich_thuoc_id]" class="form-control form-control-sm" required>
                                <option value="">-- Chọn size --</option>
                                @foreach ($sizes as $s)
                                    <option value="{{ $s->id }}">{{ $s->ten_kich_thuoc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[${index}][gia]" class="form-control form-control-sm" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[${index}][gia_khuyen_mai]" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[${index}][so_luong]" class="form-control form-control-sm" min="0" required>
                        </div>
                        <div class="col-md-1">
                            <select name="variants[${index}][trang_thai]" class="form-control form-control-sm">
                                <option value="1">Hiện</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-variant">Xóa dòng</button>
                        </div>
                    </div>
                </div>
            `;
            }

            $('#enableInitialVariants').on('change', function() {
                setVariantsEnabled($(this).is(':checked'));
            });

            $('#addVariantRow').on('click', function() {
                const container = $('#productVariantsContainer');
                const currentIndex = parseInt(container.attr('data-next-index'), 10) || 0;
                container.append(buildVariantRow(currentIndex));
                container.attr('data-next-index', currentIndex + 1);
            });

            $('#productVariantsContainer').on('click', '.remove-variant', function() {
                const rows = $('#productVariantsContainer .variant-row');
                if (rows.length <= 1) {
                    return;
                }
                $(this).closest('.variant-row').remove();
            });

            $('#modalAdd').on('hidden.bs.modal', function() {
                const container = $('#productVariantsContainer');
                container.html(buildVariantRow(0));
                container.attr('data-next-index', 1);
                $('#enableInitialVariants').prop('checked', false);
                setVariantsEnabled(false);
            });

            setVariantsEnabled(false);

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
                            let option =
                                `<option value="${dm.id}" ${dm.id == sp.danh_muc_id ? 'selected' : ''}>${dm.ten_danh_muc}</option>`;
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
