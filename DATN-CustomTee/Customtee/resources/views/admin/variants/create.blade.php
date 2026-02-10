@extends('admin.layout.AdminLayout')

@section('AdminContent')

<div class="d-flex justify-content-between align-items-center" style="margin-bottom:20px;">
    <h3 class="mb-0">Thêm biến thể sản phẩm</h3>
    <a href="{{ route('admin.san-pham.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left"></i> Danh sách sản phẩm
    </a>
</div>

<form action="{{ route('variants.store') }}" method="POST" style="max-width:1500px;">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SẢN PHẨM --}}
    <div class="form-group">
        <label>Sản phẩm</label>
        <select name="san_pham_id" id="productSelect" class="form-control" required>
            <option value="">-- Chọn sản phẩm --</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}" {{ (old('san_pham_id', $selectedProductId ?? '') == $p->id) ? 'selected' : '' }}>{{ $p->ten_san_pham }}</option>
            @endforeach
        </select>
    </div>

    {{-- THÔNG TIN SẢN PHẨM --}}
    <div id="productInfo" class="product-info-box">
        <img id="productImage">
        <div><b>Tên:</b> <span id="productName"></span></div>
        <div><b>Danh mục:</b> <span id="productCategory"></span></div>
        <div><b>Mô tả:</b> <span id="productDesc"></span></div>
    </div>

    {{-- BIẾN THỂ HIỆN CÓ --}}
    <div id="existingVariants" class="product-info-box" style="display:none;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="font-weight-bold">Biến thể hiện có</div>
            <div class="text-muted small" id="existingVariantsCount"></div>
        </div>
        <div class="table-responsive mt-2">
            <table class="table table-sm table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>Giá</th>
                        <th>Giá KM</th>
                        <th>Kho</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="existingVariantsBody"></tbody>
            </table>
        </div>
    </div>

    @php
        $oldVariants = old('variants', [
            [
                'mau_sac_id' => '',
                'kich_thuoc_id' => '',
                'gia' => '',
                'gia_khuyen_mai' => '',
                'so_luong' => '',
                'trang_thai' => '1',
            ]
        ]);
    @endphp

    <div class="form-group">
        <label>Danh sách biến thể</label>
        <div class="row variant-header">
            <div class="col-md-3">Màu</div>
            <div class="col-md-2">Size</div>
            <div class="col-md-2">Giá</div>
            <div class="col-md-2">Giá KM</div>
            <div class="col-md-2">Số lượng</div>
            <div class="col-3">Trạng thái</div>
        </div>
        <div id="variantsContainer" data-next-index="{{ count($oldVariants) }}">
            @foreach($oldVariants as $index => $row)
                <div class="variant-row" data-index="{{ $index }}">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="variants[{{ $index }}][mau_sac_id]" class="form-control form-control-sm" required>
                                <option value="">-- Chọn màu --</option>
                                @foreach($colors as $c)
                                    <option value="{{ $c->id }}" {{ ($row['mau_sac_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->ten_mau }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="variants[{{ $index }}][kich_thuoc_id]" class="form-control form-control-sm" required>
                                <option value="">-- Chọn size --</option>
                                @foreach($sizes as $s)
                                    <option value="{{ $s->id }}" {{ ($row['kich_thuoc_id'] ?? '') == $s->id ? 'selected' : '' }}>{{ $s->ten_kich_thuoc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[{{ $index }}][gia]" class="form-control form-control-sm" value="{{ $row['gia'] ?? '' }}" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[{{ $index }}][gia_khuyen_mai]" class="form-control form-control-sm" value="{{ $row['gia_khuyen_mai'] ?? '' }}" min="0">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[{{ $index }}][so_luong]" class="form-control form-control-sm" value="{{ $row['so_luong'] ?? '' }}" min="0" required>
                        </div>
                        <div class="col-md-1">
                            <select name="variants[{{ $index }}][trang_thai]" class="form-control form-control-sm">
                                <option value="1" {{ ($row['trang_thai'] ?? '1') == '1' ? 'selected' : '' }}>Hiện</option>
                                <option value="0" {{ ($row['trang_thai'] ?? '1') == '0' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                    </div>
                    <div class="row variant-actions">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-variant">Xóa dòng</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" id="addVariantRow" class="btn btn-outline-primary btn-sm">Thêm dòng biến thể</button>
    </div>

    <button type="submit" class="btn btn-primary">Thêm biến thể</button>
</form>


{{-- CSS GỌN --}}
<style>
.form-group{
    margin-bottom:15px;
}
.product-info-box{
    display:none;
    margin:15px 0;
    padding:10px;
    border:1px solid #ddd;
    background:#f9f9f9;
}
#productImage{
    width:120px;
    margin-bottom:10px;
    border-radius:6px;
}
#variantsContainer .variant-row{
    padding:10px;
    border:1px dashed #ddd;
    margin-bottom:10px;
    background:#fcfcfc;
}
.variant-header{
    font-size:12px;
    color:#666;
    margin-bottom:6px;
}
.variant-header .col-md-1,
.variant-header .col-md-2,
.variant-header .col-md-3{
    padding-top:2px;
    padding-bottom:2px;
}
.variant-actions{
    margin-top:8px;
}
</style>


{{-- AJAX --}}
<script>
// Load thông tin sản phẩm nếu đã chọn sẵn
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('productSelect');
    if (productSelect.value) {
        productSelect.dispatchEvent(new Event('change'));
    }
});

document.getElementById('productSelect').addEventListener('change', function () {
    let productId = this.value;

    if (!productId) {
        document.getElementById('productInfo').style.display = 'none';
        document.getElementById('existingVariants').style.display = 'none';
        return;
    }

    fetch(`/admin/products/info/${productId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('productInfo').style.display = 'block';
            document.getElementById('productName').innerText = data.name || '';
            document.getElementById('productCategory').innerText = data.category || '';
            document.getElementById('productDesc').innerText = data.desc || '';
            document.getElementById('productImage').src = data.image ? '/storage/' + data.image : '{{ asset("img/shop_01.jpg") }}';
        });

    fetch(`/admin/variants/by-product/${productId}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('existingVariants');
            const body = document.getElementById('existingVariantsBody');
            const count = document.getElementById('existingVariantsCount');
            const variants = Array.isArray(data.variants) ? data.variants : [];

            body.innerHTML = '';
            if (variants.length === 0) {
                body.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Chưa có biến thể</td></tr>';
                count.textContent = '';
            } else {
                count.textContent = variants.length + ' biến thể';
                variants.forEach(variant => {
                    const gia = variant.gia != null ? Number(variant.gia).toLocaleString('vi-VN') + 'đ' : '-';
                    const giaKm = variant.gia_khuyen_mai != null ? Number(variant.gia_khuyen_mai).toLocaleString('vi-VN') + 'đ' : '-';
                    const status = variant.trang_thai ? 'Hiện' : 'Ẩn';
                    const row = `
                        <tr>
                            <td>${variant.mau || '-'}</td>
                            <td>${variant.size || '-'}</td>
                            <td>${gia}</td>
                            <td>${giaKm}</td>
                            <td>${variant.so_luong ?? 0}</td>
                            <td>${status}</td>
                        </tr>
                    `;
                    body.insertAdjacentHTML('beforeend', row);
                });
            }

            container.style.display = 'block';
        });
});

const variantsContainer = document.getElementById('variantsContainer');
const addVariantRowBtn = document.getElementById('addVariantRow');

function buildVariantRow(index) {
    return `
        <div class="variant-row" data-index="${index}">
            <div class="row">
                <div class="col-md-3">
                    <select name="variants[${index}][mau_sac_id]" class="form-control form-control-sm" required>
                        <option value="">-- Chọn màu --</option>
                        @foreach($colors as $c)
                            <option value="{{ $c->id }}">{{ $c->ten_mau }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="variants[${index}][kich_thuoc_id]" class="form-control form-control-sm" required>
                        <option value="">-- Chọn size --</option>
                        @foreach($sizes as $s)
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
            <div class="row variant-actions">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant">Xóa dòng</button>
                </div>
            </div>
        </div>
    `;
}

addVariantRowBtn.addEventListener('click', function () {
    const currentIndex = parseInt(variantsContainer.getAttribute('data-next-index'), 10) || 0;
    variantsContainer.insertAdjacentHTML('beforeend', buildVariantRow(currentIndex));
    variantsContainer.setAttribute('data-next-index', String(currentIndex + 1));
});

variantsContainer.addEventListener('click', function (event) {
    const btn = event.target.closest('.remove-variant');
    if (!btn) return;

    const rows = variantsContainer.querySelectorAll('.variant-row');
    if (rows.length <= 1) {
        return;
    }
    btn.closest('.variant-row').remove();
});
</script>

@endsection
