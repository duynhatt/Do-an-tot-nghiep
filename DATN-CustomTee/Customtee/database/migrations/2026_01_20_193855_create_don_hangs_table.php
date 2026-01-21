<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('don_hangs', function (Blueprint $table) {
            $table->id();

            // Người đặt hàng
            $table->foreignId('nguoi_dung_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Địa chỉ nhận hàng
            $table->foreignId('dia_chi_id')
                ->constrained('dia_chis')
                ->cascadeOnDelete();

            // Voucher áp dụng
            $table->foreignId('voucher_id')
                ->nullable()
                ->constrained('vouchers')
                ->nullOnDelete();

            // Mã đơn hàng
            $table->string('ma_don_hang')
                ->unique();

            // Tổng tiền trước giảm
            $table->decimal('tam_tinh', 12, 2);

            // Tiền giảm giá
            $table->decimal('tien_giam', 12, 2)
                ->default(0);

            // Phí vận chuyển
            $table->decimal('phi_van_chuyen', 12, 2)
                ->default(0);

            // Tổng tiền thanh toán
            $table->decimal('tong_tien', 12, 2);

            // Phương thức thanh toán
            $table->enum('phuong_thuc_thanh_toan', [
                'cod',
                'zalo_pay',
                'momo',
                'vnpay'
            ])->default('cod');

            // Trạng thái thanh toán
            $table->enum('trang_thai_thanh_toan', [
                'chua_thanh_toan',
                'da_thanh_toan',
                'that_bai'
            ])->default('chua_thanh_toan');

            // Trạng thái đơn hàng
            $table->enum('trang_thai', [
                'cho_xac_nhan',
                'dang_xu_ly',
                'dang_giao',
                'da_giao',
                'da_huy'
            ])->default('cho_xac_nhan');

            // Ghi chú khách hàng
            $table->text('ghi_chu')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('don_hangs');
    }
};
