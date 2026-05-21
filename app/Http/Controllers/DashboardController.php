<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Prediction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the dashboard for both admin and user roles.
     *
     * Menampilkan data mingguan: pesanan baru, pendapatan, jumlah pemesan unik,
     * dan tren pesanan 7 hari terakhir.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // ── Rentang minggu ini (Senin s.d. hari ini) ──────────────────────────
        $startOfWeek = Carbon::now()->startOfWeek();   // Senin 00:00
        $endOfWeek   = Carbon::now()->endOfWeek();     // Minggu 23:59

        // ── 1. Total pesanan (num_factur unik) minggu ini ──────────────────────
        $totalOrders = Transaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->distinct('num_factur')
            ->count('num_factur');

        // ── 2. Pendapatan minggu ini ───────────────────────────────────────────
        $revenue = Transaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->sum('total_amount');

        // ── 3. Jumlah pemesan unik (berdasarkan name_customer) minggu ini ──────
        $totalCustomers = Transaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->distinct('name_customer')
            ->count('name_customer');

        // ── 4. Tren pesanan 7 hari terakhir (per hari) ────────────────────────
        //    Mengambil jumlah num_factur unik per hari selama 7 hari terakhir
        $last7Days = collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->toDateString());

        $dailyCounts = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(DISTINCT num_factur) as total')
            )
            ->whereBetween('created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->groupBy('date')
            ->pluck('total', 'date');

        // Pastikan semua 7 hari ada nilainya (0 jika tidak ada transaksi)
        $orderTrend = $last7Days->map(fn($date) => (int) ($dailyCounts[$date] ?? 0))->values()->toArray();

        // Label hari dalam Bahasa Indonesia
        $dayLabels = $last7Days->map(fn($date) => Carbon::parse($date)->translatedFormat('D'))->values()->toArray();

        // ── 5. Aktivitas terkini — maks. 5 pesanan unik terbaru ────────────
        // Pesanan lama otomatis tergantikan oleh yang baru (FIFO, max 5).
        $recentTransactions = Transaction::with('product')
            ->orderByDesc('created_at')
            ->limit(50)                // ambil cukup banyak agar unique() dapat 5 faktur unik
            ->get()
            ->unique('num_factur')
            ->take(5);

        $activities = $recentTransactions->map(function ($trx) {
            $diffHuman = $trx->created_at->diffForHumans();

            return [
                'title'  => "Pesanan #{$trx->num_factur}",
                'detail' => "{$trx->name_customer} • {$diffHuman}",
                'status' => $trx->payment_status, // 'pending' | 'completed'
            ];
        })->values()->toArray();

        // ── Format pendapatan ke Rupiah ringkas ───────────────────────────────
        $revenueFormatted = $this->formatRupiah($revenue);

        // ── Perbandingan minggu lalu untuk persentase perubahan ───────────────
        $prevStart = Carbon::now()->subWeek()->startOfWeek();
        $prevEnd   = Carbon::now()->subWeek()->endOfWeek();

        $prevOrders    = Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->distinct('num_factur')->count('num_factur');
        $prevRevenue   = Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->sum('total_amount');
        $prevCustomers = Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->distinct('name_customer')->count('name_customer');

        $orderChange    = $this->percentChange($prevOrders, $totalOrders);
        $revenueChange  = $this->percentChange($prevRevenue, $revenue);
        $customerChange = $this->percentChange($prevCustomers, $totalCustomers);

        // ── 6. Notifikasi produk yang stoknya menyentuh/di bawah ROP ─────────
        // Hitung total terjual per produk dari transactions (sum total_item),
        // lalu kurangi dari initial_stock / stock produk untuk dapat stok sisa.
        $totalTerjualPerProduk = DB::table('transactions')
            ->select('id_products', DB::raw('SUM(total_item) as total_terjual'))
            ->whereNotNull('total_item')
            ->groupBy('id_products')
            ->pluck('total_terjual', 'id_products');

        $restockAlerts = Prediction::with('product')
            ->whereNotNull('rop')
            ->get()
            ->filter(function ($pred) use ($totalTerjualPerProduk) {
                $product = $pred->product;
                if (!$product) return false;

                // Coba ambil stok awal: initial_stock -> stock -> fallback rekomendasi_stok
                $initialStock = $product->initial_stock
                    ?? $product->stock
                    ?? $pred->rekomendasi_stok
                    ?? 0;

                $totalTerjual        = (int) ($totalTerjualPerProduk[$pred->product_id] ?? 0);
                $currentStock        = max(0, (int) $initialStock - $totalTerjual);

                $pred->current_stock = $currentStock;

                return $currentStock <= (int) $pred->rop;
            })
            ->map(function ($pred) {
                return [
                    'title'         => "Restock: {$pred->product->name_product}",
                    'detail'        => "Stok {$pred->current_stock} unit sudah mencapai ROP {$pred->rop} unit",
                    'status'        => 'restock',
                    'product_name'  => $pred->product->name_product,
                    'current_stock' => $pred->current_stock,
                    'rop'           => (int) $pred->rop,
                ];
            })
            ->values()
            ->toArray();

        $dashboardData = [
            'user'            => $user,
            'role'            => $user->role,

            // Stats cards
            'totalOrders'     => number_format($totalOrders),
            'revenue'         => $revenueFormatted,
            'totalCustomers'  => number_format($totalCustomers),

            // Persentase perubahan vs minggu lalu
            'orderChange'     => $orderChange,
            'revenueChange'   => $revenueChange,
            'customerChange'  => $customerChange,

            // Chart
            'orderTrend'      => $orderTrend,   // array int [7 nilai]
            'dayLabels'       => $dayLabels,    // array string [7 label]

            // Aktivitas
            'activities'      => $activities,

            // Notifikasi restock
            'restockAlerts'   => $restockAlerts,
        ];

        return view('dashboard', $dashboardData);
    }

    // ── Helper: format Rupiah ringkas (M = juta, K = ribu) ───────────────────
    private function formatRupiah(float $amount): string
    {
        if ($amount >= 1_000_000_000) {
            return 'Rp ' . number_format($amount / 1_000_000_000, 1) . 'M';
        }
        if ($amount >= 1_000_000) {
            return 'Rp ' . number_format($amount / 1_000_000, 1) . 'jt';
        }
        if ($amount >= 1_000) {
            return 'Rp ' . number_format($amount / 1_000, 1) . 'rb';
        }
        return 'Rp ' . number_format($amount);
    }

    // ── Helper: hitung persentase perubahan ──────────────────────────────────
    private function percentChange(float $old, float $new): array
    {
        if ($old == 0) {
            $pct = $new > 0 ? 100.0 : 0.0;
        } else {
            $pct = round((($new - $old) / $old) * 100, 1);
        }

        return [
            'value'    => abs($pct),
            'positive' => $pct >= 0,
            'label'    => ($pct >= 0 ? '+' : '-') . abs($pct) . '%',
        ];
    }
}