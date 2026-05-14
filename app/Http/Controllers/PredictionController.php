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
        $products = Product::where('is_active', true)->get();
        return view('prediction', compact('products'));
    }

    public function predict(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'lead_time'  => 'required|integer|min:1|max:90',
        ]);

        $product = Product::findOrFail($request->product_id);

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
                produk: strtolower(str_replace(' ', '_', $product->name_product)),
                history: $historyInput,
                leadTime: (int) $request->lead_time,
                forecastDays: 30,
            );

            Prediction::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'rekomendasi_stok' => round($hasil['recommended_order']),
                    'rop'              => round($hasil['rop']),
                    'tanggal_prediksi' => today(),
                ]
            );

            return response()->json([
                'success' => true,
                'data'    => [
                    'product_name'     => $product->name_product,
                    'rekomendasi_stok' => round($hasil['recommended_order']),
                    'rop'              => round($hasil['rop']),
                    'tanggal'          => today()->translatedFormat('d F Y'),
                ],
            ]);
        } catch (Exception $e) {
            // Log raw output untuk debug
            Log::error('[Prediction] ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}