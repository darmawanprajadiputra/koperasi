<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PredictionController extends Controller
{
    /**
     * Display the prediction page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $seasonal_products = [
            [
                'name' => 'Padi Musim Hujan',
                'image' => 'https://via.placeholder.com/200x150?text=Padi+Hujan',
                'best_period' => 'Nov - Feb',
                'expected_yield' => '5-6 ton/ha'
            ],
            [
                'name' => 'Jagung Musim Kemarau',
                'image' => 'https://via.placeholder.com/200x150?text=Jagung+Kemarau',
                'best_period' => 'Mar - Oct',
                'expected_yield' => '4-5 ton/ha'
            ],
            [
                'name' => 'Kacang Tanah',
                'image' => 'https://via.placeholder.com/200x150?text=Kacang+Tanah',
                'best_period' => 'May - Aug',
                'expected_yield' => '2-3 ton/ha'
            ],
            [
                'name' => 'Kedelai Organik',
                'image' => 'https://via.placeholder.com/200x150?text=Kedelai',
                'best_period' => 'Apr - Sep',
                'expected_yield' => '2-2.5 ton/ha'
            ],
        ];

        return view('prediksi', ['seasonal_products' => $seasonal_products]);
    }

    /**
     * Predict stock based on AI analysis
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function predict(Request $request)
    {
        $product_name = $request->input('product_name');

        // TODO: Integrate with actual AI/ML model for prediction

        // Simulated prediction results
        $prediction = [
            'product_name' => $product_name,
            'trend_demand' => rand(-15, 35),  // percentage change
            'recommended_qty' => rand(50, 200),  // units
            'reorder_cycle' => rand(7, 30),  // days
            'confidence' => rand(75, 98),  // confidence percentage
            'analysis_date' => now()->format('Y-m-d'),
        ];

        return response()->json([
            'success' => true,
            'prediction' => $prediction,
            'message' => 'Prediction completed'
        ]);
    }

    /**
     * Analyze seasonal product
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeProduct(Request $request)
    {
        $product_name = $request->input('product_name');

        // TODO: Fetch actual analysis from database/AI service

        $analysis = [
            'product_name' => $product_name,
            'seasonal_trend' => 'Increasing',
            'market_price' => rand(15000, 100000),
            'supply_chain' => 'Stable',
            'recommendations' => [
                'Increase stock allocation by 20%',
                'Monitor competitor pricing',
                'Plan logistics for upcoming season'
            ]
        ];

        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }

    /**
     * Get prediction history
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory()
    {
        $history = [
            'Beras Premium',
            'Jagung Kuning',
            'Bawang Merah',
            'Cabai Rawit'
        ];

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Save prediction to history
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePrediction(Request $request)
    {
        $product_name = $request->input('product_name');

        // TODO: Save prediction to database

        return response()->json([
            'success' => true,
            'message' => 'Prediction saved to history',
            'product_name' => $product_name
        ]);
    }
}
