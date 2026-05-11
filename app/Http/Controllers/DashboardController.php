<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard for both admin and user roles.
     * 
     * Satu dashboard yang sama untuk semua role tanpa menghilangkan role.
     * Data bisa ditampilkan berbeda berdasarkan role di view jika diperlukan.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Dashboard data yang sama untuk semua role
        $dashboardData = [
            'user' => $user,
            'role' => $user->role, // Tetap menyimpan role untuk digunakan di view
            'totalOrders' => 1284,
            'revenue' => 'Rp 42.8M',
            'totalCustomers' => 8421,
            'orderTrend' => [65, 85, 45, 95, 75, 40, 55],
            'activities' => [
                [
                    'type' => 'secondary',
                    'title' => 'Restock Beras Merah Organik',
                    'detail' => 'Gudang Utama • 12 menit yang lalu'
                ],
                [
                    'type' => 'tertiary',
                    'title' => 'Pesanan Baru #ORD-9921',
                    'detail' => 'Bpk. Ahmad Subarjo • 45 menit yang lalu'
                ],
                [
                    'type' => 'outline',
                    'title' => 'Update Profil Anggota Baru',
                    'detail' => 'Admin System • 2 jam yang lalu'
                ],
                [
                    'type' => 'secondary',
                    'title' => 'Laporan Bulanan Diterbitkan',
                    'detail' => 'Keuangan • 5 jam yang lalu'
                ],
            ]
        ];

        return view('dashboard', $dashboardData);
    }
}
