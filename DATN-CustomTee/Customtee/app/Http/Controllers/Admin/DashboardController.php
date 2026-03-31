<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BienThe;
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
        $groupBy = $request->input('group', 'day');
        $groupBy = in_array($groupBy, ['day', 'week', 'month', 'year'], true) ? $groupBy : 'day';

        $from = $request->input('from');
        $to   = $request->input('to');

        // Pagination cho bảng "Doanh thu theo thời gian"
        $timePage = max(1, (int) $request->input('time_page', 1));
        $timePerPage = (int) $request->input('time_per_page', 10);
        $timePerPage = max(5, min(20, $timePerPage));

        // Nếu có truyền from/to thì ưu tiên sử dụng phạm vi tùy chọn.
        // `from/to` định dạng ISO: YYYY-MM-DD (type="date" trên UI).
        $resolved = false;
        if (!empty($from) || !empty($to)) {
            try {
                $startDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::today()->startOfDay();
                $endDate = $to ? Carbon::parse($to)->endOfDay() : Carbon::parse($from)->endOfDay();

                // Đảm bảo start <= end
                if ($startDate->gt($endDate)) {
                    [$startDate, $endDate] = [$endDate->startOfDay(), $startDate->endOfDay()];
                }

                $period = 'custom';
                $resolved = true;
            } catch (\Throwable $e) {
                Log::warning('Invalid revenue filter from/to', [
                    'from' => $from,
                    'to' => $to,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!$resolved) {
            $endDate = Carbon::now();
            $startDate = match ($period) {
                'today'    => Carbon::today()->startOfDay(),
                '30days'   => Carbon::now()->subDays(30)->startOfDay(),
                '90days'   => Carbon::now()->subDays(90)->startOfDay(),
                'thisyear' => Carbon::now()->startOfYear(),
                default    => Carbon::now()->subDays(7)->startOfDay(),
            };

            if ($period === 'today') {
                $endDate = Carbon::today()->endOfDay();
            }
        }

        $stats = $this->getQuickStats($startDate, $endDate);

        $revenueByDate = $this->getRevenueByDate($startDate, $endDate, $groupBy);

        // Cắt dữ liệu bảng theo trang (chart vẫn dùng dữ liệu đầy đủ)
        $timeTotalBuckets = count($revenueByDate['labels'] ?? []);
        $timeLastPage = $timeTotalBuckets > 0 ? (int) ceil($timeTotalBuckets / $timePerPage) : 1;
        $timePage = min($timePage, $timeLastPage);
        $timeOffset = ($timePage - 1) * $timePerPage;
        $revenueByDateTable = [
            'labels' => array_slice($revenueByDate['labels'] ?? [], $timeOffset, $timePerPage),
            'data'   => array_slice($revenueByDate['data'] ?? [], $timeOffset, $timePerPage),
        ];

        $revenueByCategory = $this->getRevenueByCategory($startDate, $endDate);

        $topCustomers = $this->getTopCustomers($startDate, $endDate, 8);

        $ordersByStatus = $this->getOrdersByStatus($startDate, $endDate);

        $topProducts = $this->getTopProducts($startDate, $endDate, 10);

        $lowStockVariants = BienThe::with('sanPham')
            ->where('so_luong', '<', 10)
            ->orderBy('so_luong')
            ->take(5)
            ->get();

        $lowStockCount = BienThe::where('so_luong', '<', 10)->count();

        return view('admin.dashboard.index', compact(
            'stats',
            'revenueByDate',
            'revenueByDateTable',
            'revenueByCategory',
            'topCustomers',
            'ordersByStatus',
            'topProducts',
            'period',
            'groupBy',
            'startDate',
            'endDate',
            'lowStockVariants',
            'lowStockCount',
            'timePage',
            'timeLastPage',
            'timePerPage'
        ));
    }

    // Route /admin/dashboard đang trỏ tới Dashboard() trong web.php,
    // nên tạo alias để tránh lỗi khi bấm lọc theo period.
    public function Dashboard(Request $request)
    {
        return $this->home($request);
    }

    private function getQuickStats($start, $end)
    {
        // Doanh thu chỉ tính khi khách xác nhận nhận hàng
        $revenue = DonHang::whereBetween('updated_at', [$start, $end])
            ->where('trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
            ->where('trang_thai_thanh_toan', 'da_thanh_toan')
            ->sum('tong_tien');

        // Đếm số đơn "hoàn thành" theo thời điểm xác nhận
        $ordersCount = DonHang::whereBetween('updated_at', [$start, $end])
            ->where('trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
            ->where('trang_thai_thanh_toan', 'da_thanh_toan')
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

    private function getRevenueByDate($start, $end, string $groupBy = 'day')
    {
        $labels = [];
        $values = [];

        if ($groupBy === 'day') {
            $data = DonHang::query()
                ->whereBetween('updated_at', [$start, $end])
                ->where('trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
                ->where('trang_thai_thanh_toan', 'da_thanh_toan')
                ->select(
                    DB::raw('DATE(updated_at) as date'),
                    DB::raw('SUM(tong_tien) as total')
                )
                ->groupBy('date')
                ->get();

            $totals = $data->pluck('total', 'date');

            $current = Carbon::parse($start)->startOfDay();
            $endDay  = Carbon::parse($end)->endOfDay();

            while ($current->lte($endDay)) {
                $labels[] = $current->format('d/m');

                $key = $current->format('Y-m-d');
                $values[] = isset($totals[$key]) ? (float) $totals[$key] : 0;

                $current->addDay();
            }

            return [
                'labels' => $labels,
                'data'   => $values,
            ];
        }

        if ($groupBy === 'week') {
            $data = DonHang::query()
                ->whereBetween('updated_at', [$start, $end])
                ->where('trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
                ->where('trang_thai_thanh_toan', 'da_thanh_toan')
                ->select(
                    DB::raw('YEARWEEK(updated_at, 1) as bucket'),
                    DB::raw('SUM(tong_tien) as total')
                )
                ->groupBy('bucket')
                ->get();

            $totals = $data->pluck('total', 'bucket');

            $cursor = Carbon::parse($start)->startOfWeek(Carbon::MONDAY);
            $endCursor = Carbon::parse($end)->endOfWeek(Carbon::SUNDAY);

            while ($cursor->lte($endCursor)) {
                $weekStart = $cursor->copy()->startOfWeek(Carbon::MONDAY);
                $weekEnd = $cursor->copy()->endOfWeek(Carbon::SUNDAY);

                $labels[] = $weekStart->format('d/m') . ' - ' . $weekEnd->format('d/m');

                // YEARWEEK(..., 1) khớp ISO week => dùng format oW
                $bucketKey = (string) (int) $weekStart->format('oW');
                $values[] = isset($totals[$bucketKey]) ? (float) $totals[$bucketKey] : 0;

                $cursor->addWeek();
            }

            return [
                'labels' => $labels,
                'data'   => $values,
            ];
        }

        if ($groupBy === 'month') {
            $data = DonHang::query()
                ->whereBetween('updated_at', [$start, $end])
                ->where('trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
                ->where('trang_thai_thanh_toan', 'da_thanh_toan')
                ->select(
                    DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as bucket'),
                    DB::raw('SUM(tong_tien) as total')
                )
                ->groupBy('bucket')
                ->get();

            $totals = $data->pluck('total', 'bucket');

            $cursor = Carbon::parse($start)->startOfMonth();
            $endCursor = Carbon::parse($end)->endOfMonth();

            while ($cursor->lte($endCursor)) {
                $labels[] = $cursor->format('m/Y');
                $bucketKey = $cursor->format('Y-m');
                $values[] = isset($totals[$bucketKey]) ? (float) $totals[$bucketKey] : 0;
                $cursor->addMonth();
            }

            return [
                'labels' => $labels,
                'data'   => $values,
            ];
        }

        // year
        $data = DonHang::query()
            ->whereBetween('updated_at', [$start, $end])
            ->where('trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
            ->where('trang_thai_thanh_toan', 'da_thanh_toan')
            ->select(
                DB::raw('YEAR(updated_at) as bucket'),
                DB::raw('SUM(tong_tien) as total')
            )
            ->groupBy('bucket')
            ->get();

        $totals = $data->pluck('total', 'bucket');

        $cursor = Carbon::parse($start)->startOfYear();
        $endCursor = Carbon::parse($end)->endOfYear();

        while ($cursor->lte($endCursor)) {
            $labels[] = (string) $cursor->year;
            $bucketKey = (string) $cursor->year;
            $values[] = isset($totals[$bucketKey]) ? (float) $totals[$bucketKey] : 0;
            $cursor->addYear();
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
            ->whereBetween('don_hangs.updated_at', [$start, $end])
            ->where('don_hangs.trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
            ->where('don_hangs.trang_thai_thanh_toan', 'da_thanh_toan')
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

    private function getTopCustomers($start, $end, $limit = 8)
    {
        return DonHang::query()
            ->whereBetween('don_hangs.updated_at', [$start, $end])
            ->where('don_hangs.trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
            ->where('don_hangs.trang_thai_thanh_toan', 'da_thanh_toan')
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

    private function getOrdersByStatus($start, $end)
    {
        $statuses = [
            DonHang::TRANG_THAI_CHO_XAC_NHAN,
            DonHang::TRANG_THAI_DANG_XU_LY,
            DonHang::TRANG_THAI_DANG_GIAO,
            DonHang::TRANG_THAI_DA_GIAO,
            DonHang::TRANG_THAI_DA_HOAN_THANH,
            DonHang::TRANG_THAI_DA_HUY,
            'tra_hang',
        ];

        $counts = DonHang::query()
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($query) {
                $query->where('yeu_cau_tra', false)->orWhereNull('yeu_cau_tra');
            })
            ->select('trang_thai', DB::raw('COUNT(*) as count'))
            ->groupBy('trang_thai')
            ->pluck('count', 'trang_thai')
            ->toArray();

        $result = [];
        foreach ($statuses as $status) {
            $result[$status] = (int) ($counts[$status] ?? 0);
        }
        $result['tra_hang'] = DonHang::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('yeu_cau_tra', true)
            ->count();
        return $result;
    }

    private function getTopProducts($start, $end, $limit = 10)
    {
        $totalRevenue = ChiTietDonHang::query()
            ->join('don_hangs', 'don_hang_chi_tiets.don_hang_id', '=', 'don_hangs.id')
            ->join('san_phams', 'don_hang_chi_tiets.san_pham_id', '=', 'san_phams.id')
            ->whereBetween('don_hangs.updated_at', [$start, $end])
            ->where('don_hangs.trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
            ->where('don_hangs.trang_thai_thanh_toan', 'da_thanh_toan')
            ->sum('don_hang_chi_tiets.thanh_tien');

        $products = ChiTietDonHang::query()
            ->join('don_hangs', 'don_hang_chi_tiets.don_hang_id', '=', 'don_hangs.id')
            ->join('san_phams', 'don_hang_chi_tiets.san_pham_id', '=', 'san_phams.id')
            ->whereBetween('don_hangs.updated_at', [$start, $end])
            ->where('don_hangs.trang_thai', DonHang::TRANG_THAI_DA_HOAN_THANH)
            ->where('don_hangs.trang_thai_thanh_toan', 'da_thanh_toan')
            ->select(
                'san_phams.id',
                'san_phams.ten_san_pham',
                'san_phams.hinh_anh_chinh',
                DB::raw('SUM(don_hang_chi_tiets.so_luong) as total_quantity'),
                DB::raw('SUM(don_hang_chi_tiets.thanh_tien) as total_revenue')
            )
            ->groupBy('san_phams.id', 'san_phams.ten_san_pham', 'san_phams.hinh_anh_chinh')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();

        $totalRevenueValue = (float) $totalRevenue;
        foreach ($products as $product) {
            $revenueValue = (float) $product->total_revenue;
            $product->percent_total_revenue = $totalRevenueValue > 0
                ? round(($revenueValue / $totalRevenueValue) * 100, 2)
                : 0;
        }

        return $products;
    }

    public function ordersByStatus(Request $request)
    {
        $status = $request->status;
        $from = $request->input('from');
        $to = $request->input('to');

        $query = DonHang::with('nguoiDung');

        if (!empty($from) || !empty($to)) {
            try {
                $startDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::today()->startOfDay();
                $endDate = $to ? Carbon::parse($to)->endOfDay() : Carbon::parse($from)->endOfDay();

                if ($startDate->gt($endDate)) {
                    [$startDate, $endDate] = [$endDate->startOfDay(), $startDate->endOfDay()];
                }

                $query->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Throwable $e) {
                Log::warning('Invalid orders-by-status filter from/to', [
                    'from' => $from,
                    'to' => $to,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($status === 'tra_hang') {
            $query->where('yeu_cau_tra', true);
        } else {
            $query->where('trang_thai', $status)
                ->where(function ($q) {
                    $q->where('yeu_cau_tra', false)->orWhereNull('yeu_cau_tra');
                });
        }

        $donHangs = $query->latest()
            ->paginate(5, [
                'id',
                'ma_don_hang',
                'ten_nguoi_nhan',
                'so_dien_thoai_nhan_hang',
                'tong_tien',
                'trang_thai',
                'trang_thai_thanh_toan',
                'created_at',
                'yeu_cau_tra',
            ]);

        return response()->json($donHangs);
    }

    public function lowStockVariants(Request $request)
    {
        $variants = BienThe::with([
            'product.category',
            'color',
            'size'
        ])
            ->where('so_luong', '<', 10)
            ->orderBy('so_luong')
            ->paginate(5);

        return response()->json($variants);
    }

    /**
     * AJAX endpoint: trả JSON cho bảng "Doanh thu theo thời gian"
     * để phân trang không reload trang.
     */
    public function revenueTimeTable(Request $request)
    {
        $period = $request->input('period', '7days');
        $groupBy = $request->input('group', 'day');
        $groupBy = in_array($groupBy, ['day', 'week', 'month', 'year'], true) ? $groupBy : 'day';

        $from = $request->input('from');
        $to   = $request->input('to');

        $resolved = false;
        if (!empty($from) || !empty($to)) {
            try {
                $startDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::today()->startOfDay();
                $endDate = $to ? Carbon::parse($to)->endOfDay() : Carbon::parse($from)->endOfDay();

                if ($startDate->gt($endDate)) {
                    [$startDate, $endDate] = [$endDate->startOfDay(), $startDate->endOfDay()];
                }

                $period = 'custom';
                $resolved = true;
            } catch (\Throwable $e) {
                Log::warning('Invalid revenue-time-table filter from/to', [
                    'from' => $from,
                    'to' => $to,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!$resolved) {
            $endDate = Carbon::now();
            $startDate = match ($period) {
                'today'    => Carbon::today()->startOfDay(),
                '30days'   => Carbon::now()->subDays(30)->startOfDay(),
                '90days'   => Carbon::now()->subDays(90)->startOfDay(),
                'thisyear' => Carbon::now()->startOfYear(),
                default    => Carbon::now()->subDays(7)->startOfDay(),
            };

            if ($period === 'today') {
                $endDate = Carbon::today()->endOfDay();
            }
        }

        $timePage = max(1, (int) $request->input('time_page', 1));
        $timePerPage = (int) $request->input('time_per_page', 10);
        $timePerPage = max(5, min(20, $timePerPage));

        $revenueByDate = $this->getRevenueByDate($startDate, $endDate, $groupBy);

        $labels = $revenueByDate['labels'] ?? [];
        $data = $revenueByDate['data'] ?? [];

        $totalRevenue = array_sum($data);
        $totalBuckets = count($labels);
        $lastPage = $totalBuckets > 0 ? (int) ceil($totalBuckets / $timePerPage) : 1;
        $timePage = min($timePage, $lastPage);
        $offset = ($timePage - 1) * $timePerPage;

        $sliceLabels = array_slice($labels, $offset, $timePerPage);
        $sliceData = array_slice($data, $offset, $timePerPage);

        $rows = [];
        foreach ($sliceLabels as $idx => $label) {
            $value = isset($sliceData[$idx]) ? (float) $sliceData[$idx] : 0.0;
            $percent = $totalRevenue > 0 ? round(($value / $totalRevenue) * 100, 2) : 0.0;
            $rows[] = [
                'row_no' => $offset + $idx + 1,
                'label' => $label,
                'value' => $value,
                'percent' => $percent,
            ];
        }

        return response()->json([
            'rows' => $rows,
            'meta' => [
                'current_page' => $timePage,
                'last_page' => $lastPage,
                'per_page' => $timePerPage,
                'total_buckets' => $totalBuckets,
            ],
        ]);
    }
}
