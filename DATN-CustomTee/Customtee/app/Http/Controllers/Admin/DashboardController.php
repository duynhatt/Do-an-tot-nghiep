<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\User;
use App\Models\SanPham;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function home(Request $request)
    {
        $period = $request->input('period', '7days');

        $endDate   = Carbon::now();
        $startDate = match ($period) {
            '30days'  => Carbon::now()->subDays(30),
            '90days'  => Carbon::now()->subDays(90),
            'thisyear' => Carbon::now()->startOfYear(),
            default   => Carbon::now()->subDays(7), 
        };

        $stats = $this->getQuickStats($startDate, $endDate);

        $revenueByDate = $this->getRevenueByDate($startDate, $endDate);

        $revenueByCategory = $this->getRevenueByCategory($startDate, $endDate);

        $topCustomers = $this->getTopCustomers(8, $startDate, $endDate);

        return view('admin.dashboard.index', compact(
            'stats',
            'revenueByDate',
            'revenueByCategory',
            'topCustomers',
            'period',
            'startDate',
            'endDate'
        ));
    }


    private function getQuickStats($start, $end)
    {
        $revenue = DonHang::whereBetween('created_at', [$start, $end])
            ->whereIn('trang_thai', [
                DonHang::TRANG_THAI_DA_GIAO,
                DonHang::TRANG_THAI_DA_HOAN_THANH
            ])
            ->sum('tong_tien');

        $ordersCount = DonHang::whereBetween('created_at', [$start, $end])
            ->where('trang_thai', '!=', DonHang::TRANG_THAI_DA_HUY)
            ->count();

        $newCustomers = User::whereBetween('created_at', [$start, $end])
            ->where('role', '!=', 'admin')
            ->count();

        $conversionRate = $newCustomers > 0 ? round(($ordersCount / $newCustomers) * 100, 2) : 0;

        return [
            'revenue'         => number_format($revenue, 0, ',', '.'),
            'orders_count'    => number_format($ordersCount),
            'new_customers'   => number_format($newCustomers),
            'conversion_rate' => $conversionRate . '%',
        ];
    }


    private function getRevenueByDate($start, $end)
    {
        $completedCount = DonHang::whereBetween('created_at', [$start, $end])
            ->whereIn('trang_thai', [
                DonHang::TRANG_THAI_DA_GIAO,
                DonHang::TRANG_THAI_DA_HOAN_THANH
            ])
            ->count();

        $totalRevenue = DonHang::whereBetween('created_at', [$start, $end])
            ->whereIn('trang_thai', [
                DonHang::TRANG_THAI_DA_GIAO,
                DonHang::TRANG_THAI_DA_HOAN_THANH
            ])
            ->sum('tong_tien');

        $data = DonHang::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('trang_thai', [
                DonHang::TRANG_THAI_DA_GIAO,
                DonHang::TRANG_THAI_DA_HOAN_THANH
            ])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(tong_tien) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];

        $current = Carbon::parse($start)->startOfDay();
        $endDay  = Carbon::parse($end)->endOfDay();

        while ($current->lte($endDay)) {
            $dateStr = $current->format('d/m');
            $labels[] = $dateStr;

            $found = $data->firstWhere('date', $current->format('Y-m-d'));
            $values[] = $found ? (float) $found->total : 0;

            $current->addDay();
        }
        return [
            'labels' => $labels,
            'data'   => $values,
        ];
    }

    private function getRevenueByCategory($start, $end)
    {
        $data = ChiTietDonHang::query()
            ->join('don_hangs', 'don_hang_chi_tiets.don_hang_id', '=', 'don_hangs.id')
            ->join('san_phams', 'don_hang_chi_tiets.san_pham_id', '=', 'san_phams.id')
            ->join('danh_mucs', 'san_phams.danh_muc_id', '=', 'danh_mucs.id')
            ->whereBetween('don_hangs.created_at', [$start, $end])
            ->whereIn('don_hangs.trang_thai', [
                DonHang::TRANG_THAI_DA_GIAO,
                DonHang::TRANG_THAI_DA_HOAN_THANH
            ])
            ->select(
                'danh_mucs.ten_danh_muc',
                DB::raw('SUM(don_hang_chi_tiets.thanh_tien) as total')
            )
            ->groupBy('danh_mucs.id', 'danh_mucs.ten_danh_muc')
            ->orderByDesc('total')
            ->get();

        $labels = $data->pluck('ten_danh_muc')->toArray();
        $values = $data->pluck('total')->toArray();

        return [
            'labels' => $labels ?: ['Chưa có dữ liệu'],
            'data'   => $values ?: [0],
        ];
    }

    private function getTopCustomers($limit = 8, $start, $end)
    {
        return DonHang::query()
            ->whereBetween('don_hangs.created_at', [$start, $end])
            ->whereIn('don_hangs.trang_thai', [
                DonHang::TRANG_THAI_DA_GIAO,
                DonHang::TRANG_THAI_DA_HOAN_THANH
            ])
            ->join('users', 'don_hangs.nguoi_dung_id', '=', 'users.id')
            ->select(
                'users.name',
                'users.phone',
                DB::raw('COUNT(don_hangs.id) as order_count'),
                DB::raw('SUM(don_hangs.tong_tien) as total_revenue')
            )
            ->groupBy('users.id', 'users.name', 'users.phone')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }
}
