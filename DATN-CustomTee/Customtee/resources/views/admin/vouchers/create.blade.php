@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="form-w3layouts">
    <section class="panel">
        <header class="panel-heading">THÊM VOUCHER MỚI</header>
        <div class="panel-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-bottom: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Mã Voucher</label>
                    <input type="text" name="ma" class="form-control" value="{{ old('ma') }}" maxlength="50" required>
                    @error('ma')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Loại</label>
                        <select name="loai" id="voucher-loai" class="form-control">
                            <option value="tien_mat" {{ old('loai') === 'tien_mat' ? 'selected' : '' }}>Tiền mặt (đ)</option>
                            <option value="phan_tram" {{ old('loai') === 'phan_tram' ? 'selected' : '' }}>Phần trăm (%)</option>
                        </select>
                        @error('loai')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Giá trị giảm</label>
                        <input type="number" name="gia_tri" class="form-control" min="1" max="999999999999" value="{{ old('gia_tri') }}" required>
                        @error('gia_tri')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Đơn hàng tối thiểu (đ)</label>
                        <input type="number" name="don_hang_toi_thieu" class="form-control" min="0" max="999999999999" value="{{ old('don_hang_toi_thieu') }}" placeholder="VD: 100000 – đơn từ 100k mới áp dụng">
                        @error('don_hang_toi_thieu')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group" id="giam-toi-da-wrap" style="display: none;">
                        <label>Giảm tối đa (đ)</label>
                        <input type="number" name="giam_toi_da" class="form-control" min="0" max="999999999999" value="{{ old('giam_toi_da') }}" placeholder="VD: 50000">
                        <small class="text-muted">Chỉ áp dụng cho loại Giảm theo % (VD: giảm 10%, tối đa 50.000đ).</small>
                        @error('giam_toi_da')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="datetime-local" name="bat_dau" class="form-control" value="{{ old('bat_dau') }}" required>
                        @error('bat_dau')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Ngày kết thúc</label>
                        <input type="datetime-local" name="ket_thuc" class="form-control" value="{{ old('ket_thuc') }}" required>
                        @error('ket_thuc')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="so_luong" class="form-control" min="1" max="2147483647" value="{{ old('so_luong') }}" required>
                    @error('so_luong')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Giới hạn mỗi khách (lần)</label>
                    <input
                        type="number"
                        name="max_per_user"
                        class="form-control"
                        min="1"
                        max="255"
                        value="{{ old('max_per_user') }}"
                        placeholder="VD: 2"
                    >
                    @error('max_per_user')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
<button type="submit" class="btn btn-info">Lưu Voucher</button>
            </form>
        </div>
    </section>
</div>
<script>
(function() {
    var loai = document.getElementById('voucher-loai');
    var wrap = document.getElementById('giam-toi-da-wrap');
    function toggle() {
        wrap.style.display = loai.value === 'phan_tram' ? 'block' : 'none';
    }
    loai.addEventListener('change', toggle);
    toggle();
})();
</script>
@endsection