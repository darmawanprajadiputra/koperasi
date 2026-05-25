<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Prediction;
use App\Services\PredictionStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PredictionController extends Controller
{
    public function __construct(protected PredictionStockService $predictionService) {}

    public function index()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name_product', 'asc')
            ->get();

        // Ambil semua prediksi terakhir per produk (sudah pakai updateOrCreate, jadi 1 per produk)
        $predictions = Prediction::with('product')
            ->whereHas('product', fn($q) => $q->where('is_active', true))
            ->get()
            ->keyBy('product_id');

        // Gabungkan: semua produk aktif + data prediksi jika ada
        $historyRows = $products->map(function ($product) use ($predictions) {
            $pred = $predictions->get($product->id);

            return [
                'product_id'   => $product->id,
                'product_name' => $product->name_product,
                'stock'        => (int) ($product->stock ?? 0),
                'safety_stock' => $pred ? (int) $pred->rekomendasi_stok : null,
                'rop'          => $pred ? (int) $pred->rop : null,
                'tanggal'      => $pred ? $pred->tanggal_prediksi : null,
                'mape'         => $pred ? $pred->mape : null,
            ];
        });

        return view('prediction', compact('products', 'historyRows'));
    }

    public function predict(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'lead_time'  => 'required|integer|min:1|max:90',
        ]);

        $product   = Product::findOrFail($request->product_id);
        $produkKey = strtolower(str_replace(' ', '_', $product->name_product));

        $modelsPath = base_path('ml/models');
        $modelKeras = $modelsPath . '/lstm_' . $produkKey . '_model.keras';
        $modelH5    = $modelsPath . '/lstm_' . $produkKey . '_model.h5';
        $scaler     = $modelsPath . '/scaler_' . $produkKey . '.pkl';
        $lastSeq    = $modelsPath . '/last_seq_' . $produkKey . '.npy';

        $pesanError = "Data transaksi untuk \"{$product->name_product}\" belum mencukupi (minimal 30 hari).";

        if ((!file_exists($modelKeras) && !file_exists($modelH5)) ||
            !file_exists($scaler) ||
            !file_exists($lastSeq)) {
            return response()->json([
                'success' => false,
                'message' => $pesanError,
            ], 422);
        }

        $currentStock = (int) ($product->stock ?? 0);

        $history = DB::table('transactions')
            ->where('id_products', $product->id)
            ->whereNotNull('total_item')
            ->orderBy('created_at', 'desc')
            ->limit(60)
            ->pluck('total_item')
            ->reverse()
            ->values()
            ->map(fn($v) => (float) $v)
            ->toArray();

        $historyInput = count($history) >= 30 ? $history : [];

        try {
            $hasil = $this->predictionService->prediksi(
                produk      : $produkKey,
                history     : $historyInput,
                leadTime    : (int) $request->lead_time,
                forecastDays: 30,
            );

            // mape bisa null jika history tidak cukup untuk evaluasi
            $mape = isset($hasil['mape']) ? round((float) $hasil['mape'], 2) : null;

            Prediction::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'rekomendasi_stok' => round($hasil['recommended_order']),
                    'rop'              => round($hasil['rop']),
                    'tanggal_prediksi' => today(),
                    'mape'             => $mape,
                ]
            );

            return response()->json([
                'success' => true,
                'data'    => [
                    'product_id'       => $product->id,
                    'product_name'     => $product->name_product,
                    'rekomendasi_stok' => round($hasil['recommended_order']),
                    'rop'              => round($hasil['rop']),
                    'current_stock'    => $currentStock,
                    'tanggal'          => today()->translatedFormat('d F Y'),
                    'tanggal_raw'      => today()->toDateString(),
                    'mape'             => $mape,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('[Prediction] ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $pesanError,
            ], 500);
        }
    }
}