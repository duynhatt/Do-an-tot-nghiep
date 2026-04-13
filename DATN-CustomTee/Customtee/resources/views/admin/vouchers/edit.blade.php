@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="form-w3layouts">
    <section class="panel">
        <header class="panel-heading">CHỈNH SỬA VOUCHER</header>
        <div class="panel-body">
            <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Mã Voucher</label>
                    <input type="text" name="ma" class="form-control" value="{{ $voucher->ma }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Loại</label>
                        <select name="loai" id="voucher-loai" class="form-control">
                            <option value="tien_mat" {{ $voucher->loai == 'tien_mat' ? 'selected' : '' }}>Tiền mặt (đ)</option>
                            <option value="phan_tram" {{ $voucher->loai == 'phan_tram' ? 'selected' : '' }}>Phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Giá trị giảm</label>
                        <input type="number" name="gia_tri" class="form-control" value="{{ (int)$voucher->gia_tri }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Đơn hàng tối thiểu (đ)</label>
                        <input type="number" name="don_hang_toi_thieu" class="form-control" min="0" value="{{ old('don_hang_toi_thieu', $voucher->don_hang_toi_thieu) }}" placeholder="VD: 100000 – đơn từ 100k mới áp dụng">
                        <small class="text-muted">Áp dụng cho cả % và tiền mặt. Để trống nếu không yêu cầu.</small>
                    </div>
                    <div class="col-md-6 form-group" id="giam-toi-da-wrap" style="display: {{ $voucher->loai == 'phan_tram' ? 'block' : 'none' }};">
                        <label>Giảm tối đa (đ)</label>
                        <input type="number" name="giam_toi_da" class="form-control" min="0" value="{{ old('giam_toi_da', $voucher->giam_toi_da) }}" placeholder="VD: 50000">
                        <small class="text-muted">Chỉ áp dụng cho loại Giảm theo % (VD: giảm 10%, tối đa 50.000đ).</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="datetime-local" name="bat_dau" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($voucher->bat_dau)) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Ngày kết thúc</label>
<input type="datetime-local" name="ket_thuc" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($voucher->ket_thuc)) }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="so_luong" class="form-control" value="{{ $voucher->so_luong }}" required>
                </div>
                <div class="form-group">
                    <label>Giới hạn mỗi khách (lần)</label>
                    <input
                        type="number"
                        name="max_per_user"
                        class="form-control"
                        min="1"
                        value="{{ old('max_per_user', $voucher->max_per_user ?? 2) }}"
                        placeholder="VD: 2"
                    >
                    <small class="text-muted">Để trống nếu không giới hạn theo tài khoản.</small>
                </div>
                <button type="submit" class="btn btn-info">Cập nhật thay đổi</button>
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-default">Quay lại</a>
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