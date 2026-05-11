<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('order');
    }

    /**
     * API: return transactions grouped by num_factur
     * Setiap faktur bisa punya banyak item (produk), kita group jadi 1 card.
     */
    public function getOrders(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = Transaction::with('product')
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            // Map filter label → payment_status value
            $map = [
                'process'   => 'pending',
                'completed' => 'completed',
            ];
            if (isset($map[$status])) {
                $query->where('payment_status', $map[$status]);
            }
        }

        $rows = $query->get();

        // Group by num_factur so multi-item orders appear as one card
        $grouped = $rows->groupBy('num_factur')->map(function ($items) {
            $first = $items->first();

            return [
                'num_factur'     => $first->num_factur,
                'name_customer'  => $first->name_customer,
                'no_telephone'   => $first->no_telephone,
                'address'        => $first->address,
                'recipient'      => $first->recipient,
                'payment_method' => $first->payment_method,
                'payment_status' => $first->payment_status,
                'notes'          => $first->notes,
                'total_amount'   => $items->sum('total_amount'),
                'total_items'    => $items->count(),
                'date'           => $first->created_at->format('d M Y'),
                'created_at'     => $first->created_at->toISOString(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'orders'  => $grouped,
            'total'   => $grouped->count(),
        ]);
    }

    public function show($order_id)
    {
        $transactions = Transaction::with('product')
            ->where('num_factur', $order_id)
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'success'      => true,
            'num_factur'   => $order_id,
            'transactions' => $transactions,
        ]);
    }

    public function updateStatus(Request $request, $order_id)
    {
        $request->validate(['status' => 'required|string']);

        $updated = Transaction::where('num_factur', $order_id)
            ->update(['payment_status' => $request->status]);

        return response()->json([
            'success'    => true,
            'message'    => 'Status pesanan diperbarui',
            'num_factur' => $order_id,
            'new_status' => $request->status,
            'updated'    => $updated,
        ]);
    }
}