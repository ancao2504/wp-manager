<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Lọc theo site nếu có
        if ($request->has('site_id') && $request->site_id != '') {
            $query->where('site_id', $request->site_id);
        }

        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Lọc theo trạng thái tồn kho nếu có
        if ($request->has('stock_status') && $request->stock_status != '') {
            $query->where('stock_status', $request->stock_status);
        }

        // Tìm kiếm theo tên hoặc SKU
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->with('site')->latest()->paginate(15);
        $sites = Site::where('status', 'active')->get(['id', 'name']);

        return view('products.index', compact('products', 'sites'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $sites = Site::where('status', 'active')->get(['id', 'name']);
        return view('products.create', compact('sites'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'regular_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_status' => 'required|in:instock,outofstock,onbackorder',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'status' => 'required|in:draft,publish,private,trash',
        ]);

        if ($validator->fails()) {
            return redirect()->route('products.create')
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::create($validator->validated());

        return redirect()->route('products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $sites = Site::where('status', 'active')->get(['id', 'name']);
        return view('products.edit', compact('product', 'sites'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'regular_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_status' => 'required|in:instock,outofstock,onbackorder',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'status' => 'required|in:draft,publish,private,trash',
        ]);

        if ($validator->fails()) {
            return redirect()->route('products.edit', $product)
                ->withErrors($validator)
                ->withInput();
        }

        $product->update($validator->validated());

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Show the form for importing products from Excel.
     */
    public function importForm()
    {
        $sites = Site::where('status', 'active')->get(['id', 'name']);
        return view('products.import', compact('sites'));
    }

    /**
     * Import products from Excel file.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->route('products.import')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // TODO: Implement Excel import logic
            // Mock implementation for demo purposes
            $importCount = rand(5, 20);

            return redirect()->route('products.index')
                ->with('success', "Successfully imported $importCount products.");
        } catch (\Exception $e) {
            return redirect()->route('products.import')
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Export products to Excel file.
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'nullable|exists:sites,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('products.index')
                ->withErrors($validator);
        }

        try {
            // TODO: Implement Excel export logic
            // Mock implementation for demo purposes
            $fileName = 'products_export_' . now()->format('Y-m-d_His') . '.xlsx';

            // This will be replaced with actual export logic
            return response()->json([
                'success' => true,
                'message' => 'Export prepared successfully.',
                'filename' => $fileName,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync products from WooCommerce to local database.
     */
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid site selected.',
            ], 400);
        }

        $site = Site::findOrFail($request->site_id);

        try {
            // TODO: Implement the WooCommerce API sync logic
            // Mock implementation for demo purposes
            $syncedCount = rand(3, 15);

            return response()->json([
                'success' => true,
                'message' => "Successfully synced $syncedCount products from {$site->name}.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}