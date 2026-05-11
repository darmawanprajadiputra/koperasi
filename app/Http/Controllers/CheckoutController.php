<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_customer'  => 'required|string|max:255',
            'recipient'      => 'nullable|string|max:255',
            'no_telephone'   => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'payment_method' => 'required|string|max:50',
            'total_amount'   => 'required|numeric|min:0',
            'cart_items'     => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        $cart = json_decode($request->cart_items, true);

        if (empty($cart)) {
            return back()->withErrors(['cart_items' => 'Keranjang kosong.'])->withInput();
        }

        $numFactur = $this->generateInvoiceNumber();
        $created   = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::where('id', $productId)->where('is_active', true)->first();
            if (!$product) continue;

            $lineTotal = $product->price * (int) ($item['qty'] ?? 1);
            $factur    = $created === 0 ? $numFactur : $numFactur . '-' . ($created + 1);

            Transaction::create([
                'num_factur'     => $factur,
                'name_customer'  => $request->name_customer,
                'no_telephone'   => $request->no_telephone,
                'address'        => $request->address,
                'recipient'      => $request->recipient ?: $request->name_customer,
                'id_products'    => $product->id,
                'total_amount'   => $lineTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'notes'          => $request->notes,
            ]);

            $product->decrement('stock', (int) ($item['qty'] ?? 1));
            $created++;
        }

        if ($created === 0) {
            return back()->withErrors(['cart_items' => 'Tidak ada produk valid.'])->withInput();
        }

        return redirect()->route('order')
            ->with('success', "Pesanan berhasil dibuat! {$created} item dengan nomor faktur {$numFactur}.");
    }

    private function generateInvoiceNumber(): string
    {
        $last = Transaction::orderByDesc('id')->value('num_factur');

        if ($last && preg_match('/NF-(\d+)/', $last, $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return 'NF-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}