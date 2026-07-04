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
            'no_telephone'   => 'nullable|digits_between:8,20',
            'address'        => 'nullable|string',
            'payment_method' => 'required|string|max:50',
            'bank_name'      => 'required_if:payment_method,transfer|nullable|in:BNI,BRI,BCA',
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

        $notes = $request->notes;
        if ($request->payment_method === 'transfer' && $request->bank_name) {
            $bankInfo = "Transfer via Bank {$request->bank_name}";
            $notes    = $notes ? "{$bankInfo}\n{$notes}" : $bankInfo;
        }

        foreach ($cart as $productId => $item) {
            $product = Product::where('id', $productId)->where('is_active', true)->first();
            if (!$product) continue;

            $qty       = (int) ($item['qty'] ?? 1);
            $lineTotal = $product->price * $qty;

            Transaction::create([
                'num_factur'     => $numFactur,
                'name_customer'  => $request->name_customer,
                'no_telephone'   => $request->no_telephone,
                'address'        => $request->address,
                'recipient'      => $request->recipient ?: $request->name_customer,
                'id_products'    => $product->id,
                'total_item'     => $qty,
                'total_amount'   => $lineTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'notes'          => $notes,
            ]);

            $product->decrement('stock', $qty);
            $created++;
        }

        if ($created === 0) {
            return back()->withErrors(['cart_items' => 'Tidak ada produk valid.'])->withInput();
        }

        return redirect()->route('shop')
            ->with('order_success', "Pesanan berhasil diproses! {$created} item dengan nomor faktur {$numFactur}.");
    }

    private function generateInvoiceNumber(): string
    {
        $last = Transaction::orderByDesc('id')
            ->pluck('num_factur')
            ->filter(fn($n) => preg_match('/^NF-(\d+)$/', $n))
            ->first();

        if ($last && preg_match('/^NF-(\d+)$/', $last, $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return 'NF-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}