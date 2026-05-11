<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display the shop page
     */
    public function index()
    {
        return view('shop');
    }

    /**
     * Get active products via API (for AJAX requests)
     */
    public function getProducts()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->get()
            ->map(function ($product) {
                return [
                    'id'           => $product->id,
                    'name_product' => $product->name_product,
                    'category'     => $product->category?->name_categories ?? 'Produk',
                    'price'        => $product->price,
                    'stock'        => $product->stock,
                    'image'        => $product->image ? asset('storage/' . $product->image) : null,
                    'description'  => $product->description ?? $product->category?->name_categories ?? 'Produk berkualitas dari koperasi',
                ];
            });

        return response()->json([
            'success'  => true,
            'products' => $products,
        ]);
    }

    /**
     * Add product to cart (session-based)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product  = Product::where('id', $request->product_id)->where('is_active', true)->firstOrFail();
        $cart     = session()->get('cart', []);
        $id       = $request->product_id;
        $qty      = $request->quantity;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $qty;
        } else {
            $cart[$id] = [
                'id'           => $product->id,
                'name_product' => $product->name_product,
                'price'        => $product->price,
                'quantity'     => $qty,
                'image'        => $product->image ? asset('storage/' . $product->image) : null,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success'    => true,
            'message'    => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    /**
     * Get current cart contents
     */
    public function getCart()
    {
        $cart  = session()->get('cart', []);
        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        return response()->json([
            'success' => true,
            'cart'    => array_values($cart),
            'total'   => $total,
            'count'   => array_sum(array_column($cart, 'quantity')),
        ]);
    }
}