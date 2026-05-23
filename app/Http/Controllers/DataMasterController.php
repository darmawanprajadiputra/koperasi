<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Str;

class DataMasterController extends Controller
{
    /**
     * Display the Data Master portal.
     */
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name_categories')->get();
        $units      = Unit::withCount('products')->orderBy('name_unit')->get();

        return view('datamaster', compact('categories', 'units'));
    }

    /* ============================================================
       CATEGORY CRUD
       ============================================================ */

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name_categories' => ['required', 'string', 'max:100', 'unique:categories,name_categories'],
        ]);

        Category::create([
            'name_categories' => $request->name_categories,
            'slug'            => Str::slug($request->name_categories),
        ]);

        return redirect()->route('datamaster')
                         ->with('success', 'Kategori "' . $request->name_categories . '" berhasil ditambahkan.');
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name_categories' => ['required', 'string', 'max:100', 'unique:categories,name_categories,' . $category->id],
        ]);

        $category->update([
            'name_categories' => $request->name_categories,
            'slug'            => Str::slug($request->name_categories),
        ]);

        return redirect()->route('datamaster')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(Category $category)
    {
        // Prevent deletion if category still has products
        if ($category->products()->exists()) {
            return redirect()->route('datamaster')
                             ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki produk.');
        }

        $name = $category->name_categories;
        $category->delete();

        return redirect()->route('datamaster')
                         ->with('success', 'Kategori "' . $name . '" berhasil dihapus.');
    }

    /* ============================================================
       UNIT CRUD
       ============================================================ */

    /**
     * Store a new unit.
     */
    public function storeUnit(Request $request)
    {
        $request->validate([
            'name_unit' => ['required', 'string', 'max:50', 'unique:unit,name_unit'],
        ]);

        Unit::create([
            'name_unit' => $request->name_unit,
            'slug'      => Str::slug($request->name_unit),
        ]);

        return redirect()->route('datamaster')
                         ->with('success', 'Satuan "' . $request->name_unit . '" berhasil ditambahkan.');
    }

    /**
     * Update an existing unit.
     */
    public function updateUnit(Request $request, Unit $unit)
    {
        $request->validate([
            'name_unit' => ['required', 'string', 'max:50', 'unique:unit,name_unit,' . $unit->id],
        ]);

        $unit->update([
            'name_unit' => $request->name_unit,
            'slug'      => Str::slug($request->name_unit),
        ]);

        return redirect()->route('datamaster')
                         ->with('success', 'Satuan berhasil diperbarui.');
    }

    /**
     * Delete a unit.
     */
    public function destroyUnit(Unit $unit)
    {
        if ($unit->products()->exists()) {
            return redirect()->route('datamaster')
                             ->with('error', 'Tidak dapat menghapus satuan yang masih dipakai oleh produk.');
        }

        $name = $unit->name_unit;
        $unit->delete();

        return redirect()->route('datamaster')
                         ->with('success', 'Satuan "' . $name . '" berhasil dihapus.');
    }
}