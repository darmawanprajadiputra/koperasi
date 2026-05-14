<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'unit')->latest()->get();
        return view('storage', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name_categories')->get();
        $units      = Unit::orderBy('name_unit')->get();
        return view('add_item', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_product'  => 'required|string|max:255',
            'categories_id' => 'required|integer|exists:categories,id',
            'unit_id'       => 'required|integer|exists:unit,id', // ← ubah nullable jadi required
            'stock'         => 'required|integer|min:0',
            'price'         => 'required|numeric|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        try {
            Product::create([
                'name_product'  => $validated['name_product'],
                'categories_id' => $validated['categories_id'],
                'unit_id'       => $validated['unit_id'],
                'stock'         => $validated['stock'],
                'price'         => $validated['price'],
                'image'         => $imagePath,
                'is_active'     => true,
            ]);

            return redirect()->route('storage')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name_categories')->get();
        $units      = Unit::orderBy('name_unit')->get();
        return view('edit_item', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name_product'  => 'required|string|max:255',
            'categories_id' => 'required|integer|exists:categories,id',
            'unit_id'       => 'nullable|integer|exists:unit,id',
            'stock'         => 'required|integer|min:0',
            'price'         => 'required|numeric|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png|max:5120',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        $product->update($validated);

        return redirect()->route('storage')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('storage')->with('success', 'Produk berhasil dihapus!');
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('storage')->with('success', "Produk berhasil {$status}!");
    }
}
