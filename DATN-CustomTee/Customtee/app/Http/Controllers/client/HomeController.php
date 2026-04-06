<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\Category;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\SanPham;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Danh mục nổi bật: số lượng sản phẩm đang hiển thị trong danh mục, nhiều → ít
        $productCountByCategory = SanPham::query()
            ->select('danh_muc_id', DB::raw('COUNT(*) as product_count'))
            ->where('trang_thai', true)
            ->groupBy('danh_muc_id');

        $danhMucs = Category::hienThi()
            ->leftJoinSub($productCountByCategory, 'pc', function ($join) {
                $join->on('pc.danh_muc_id', '=', 'danh_mucs.id');
            })
            ->orderByDesc(DB::raw('COALESCE(pc.product_count, 0)'))
            ->orderByDesc('danh_mucs.id')
            ->select('danh_mucs.*')
            ->take(4)
            ->get();

        $sanPhamsMoiNhat = SanPham::with('category')
            ->where('trang_thai', true)
            ->whereHas('danhMuc', fn ($q) => $q->where('trang_thai', 1))
            ->withMin(['variants' => function ($q) {
                $q->where('trang_thai', 1);
            }], 'gia')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        // -----------------------------
        // Sản phẩm hot: top 3 theo số lượng đã mua
        // -----------------------------
        $topHotProductIds = ChiTietDonHang::query()
            ->join('don_hangs', 'don_hang_chi_tiets.don_hang_id', '=', 'don_hangs.id')
            ->whereBetween('don_hangs.created_at', [$startDate, $endDate])
            ->whereIn('don_hangs.trang_thai', [
                DonHang::TRANG_THAI_DA_GIAO,
                DonHang::TRANG_THAI_DA_HOAN_THANH,
            ])
            ->select(
                'don_hang_chi_tiets.san_pham_id',
                DB::raw('SUM(don_hang_chi_tiets.so_luong) as total_quantity')
            )
            ->groupBy('don_hang_chi_tiets.san_pham_id')
            ->orderByDesc('total_quantity')
            ->limit(6)
            ->pluck('san_pham_id')
            ->values();

        $hotIds = $topHotProductIds->map(fn ($id) => (int) $id)->all();

        $sanPhamsHot = collect();
        if (!empty($hotIds)) {
            $sanPhamsHot = SanPham::with('category')
                ->where('trang_thai', true)
                ->whereIn('id', $hotIds)
                ->whereHas('variants', fn ($q) => $q->where('trang_thai', 1))
                ->withMin(['variants' => function ($q) {
                    $q->where('trang_thai', 1);
                }], 'gia')
                ->orderByRaw('FIELD(id,' . implode(',', $hotIds) . ')')
                ->get();
        }

        // -----------------------------
        // Sản phẩm đang giảm giá
        // -----------------------------
        $discountVariantsConstraint = function ($q) {
            $q->where('trang_thai', 1)
                ->whereNotNull('gia_khuyen_mai')
                ->whereColumn('gia_khuyen_mai', '<', 'gia');
        };

        $sanPhamsGiamGia = SanPham::with('category')
            ->where('trang_thai', true)
            ->whereHas('danhMuc', fn ($q) => $q->where('trang_thai', 1))
            ->whereHas('variants', $discountVariantsConstraint)
            ->withMin(['variants' => $discountVariantsConstraint], 'gia')
            ->withMin(['variants' => $discountVariantsConstraint], 'gia_khuyen_mai')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        // Đánh giá nổi bật (ví dụ lấy 3 đánh giá mới nhất)
        $danhGias = BinhLuan::with('user')
            ->where('trang_thai', 1)
            ->where('hien_thi_trang_chu', 1)
            ->where('so_sao', '>=', 4)
            ->latest()
            ->take(6)
            ->get();

        return view('client.Home', compact('sanPhamsMoiNhat', 'danhMucs', 'sanPhamsHot', 'sanPhamsGiamGia', 'danhGias'));
    }
}
