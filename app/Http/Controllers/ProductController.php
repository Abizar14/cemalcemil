<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');
        $categoryId = (int) $request->integer('category_id');
        $menuGroup = trim((string) $request->string('menu_group'));
        $sellingUnit = trim((string) $request->string('selling_unit'));

        $products = Product::query()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->when($categoryId > 0, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($menuGroup !== '', function ($query) use ($menuGroup) {
                $query->where('menu_group', $menuGroup);
            })
            ->when($sellingUnit !== '', function ($query) use ($sellingUnit) {
                $query->where('selling_unit', $sellingUnit);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'menuGroups' => Product::query()->whereNotNull('menu_group')->where('menu_group', '!=', '')->select('menu_group')->distinct()->orderBy('menu_group')->pluck('menu_group'),
            'sellingUnits' => Product::query()->whereNotNull('selling_unit')->where('selling_unit', '!=', '')->select('selling_unit')->distinct()->orderBy('selling_unit')->pluck('selling_unit'),
            'search' => $search,
            'status' => $status,
            'categoryId' => $categoryId,
            'menuGroup' => $menuGroup,
            'sellingUnit' => $sellingUnit,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('products.create', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'menu_group' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'selling_unit' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'track_stock' => ['nullable', 'boolean'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'stock_alert_threshold' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['track_stock'] = $request->boolean('track_stock');
        $validated['menu_group'] = $validated['menu_group'] !== null && trim($validated['menu_group']) !== ''
            ? trim($validated['menu_group'])
            : null;
        $validated['selling_unit'] = $validated['selling_unit'] !== null && trim($validated['selling_unit']) !== ''
            ? trim($validated['selling_unit'])
            : null;
        $validated['stock_quantity'] = $validated['track_stock']
            ? (int) ($validated['stock_quantity'] ?? 0)
            : null;
        $validated['stock_alert_threshold'] = $validated['track_stock']
            ? (int) ($validated['stock_alert_threshold'] ?? 3)
            : 3;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['image_path'] = $this->storeProductImage($request);

        if ($validated['track_stock'] && $validated['stock_quantity'] <= 0) {
            $validated['is_active'] = false;
        }

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name,'.$product->id],
            'menu_group' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'selling_unit' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'track_stock' => ['nullable', 'boolean'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'stock_alert_threshold' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['track_stock'] = $request->boolean('track_stock');
        $validated['menu_group'] = $validated['menu_group'] !== null && trim($validated['menu_group']) !== ''
            ? trim($validated['menu_group'])
            : null;
        $validated['selling_unit'] = $validated['selling_unit'] !== null && trim($validated['selling_unit']) !== ''
            ? trim($validated['selling_unit'])
            : null;
        $validated['stock_quantity'] = $validated['track_stock']
            ? (int) ($validated['stock_quantity'] ?? 0)
            : null;
        $validated['stock_alert_threshold'] = $validated['track_stock']
            ? (int) ($validated['stock_alert_threshold'] ?? 3)
            : 3;
        $validated['is_active'] = $request->boolean('is_active');
        unset($validated['remove_image']);

        if ($request->boolean('remove_image')) {
            $this->deleteUploadedImage($product->image_path);
            $validated['image_path'] = null;
        }

        $newImagePath = $this->storeProductImage($request);

        if ($newImagePath !== null) {
            $this->deleteUploadedImage($product->image_path);
            $validated['image_path'] = $newImagePath;
        }

        if ($validated['track_stock'] && $validated['stock_quantity'] <= 0) {
            $validated['is_active'] = false;
        }

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->transactionDetails()->exists()) {
            return redirect()
                ->route('products.index')
                ->withErrors([
                    'delete' => 'Produk ini sudah dipakai dalam transaksi dan tidak bisa dihapus.',
                ]);
        }

        $this->deleteUploadedImage($product->image_path);
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('status', 'Produk berhasil dihapus.');
    }

    /**
     * Store the uploaded product image in the public directory.
     */
    protected function storeProductImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $directory = public_path('images/products/uploads');
        File::ensureDirectoryExists($directory);

        $extension = $request->file('image')->getClientOriginalExtension();
        $filename = Str::uuid()->toString().'.'.$extension;
        $request->file('image')->move($directory, $filename);

        return 'images/products/uploads/'.$filename;
    }

    /**
     * Delete uploaded product images without touching seeded assets.
     */
    protected function deleteUploadedImage(?string $imagePath): void
    {
        if (! is_string($imagePath) || ! str_starts_with($imagePath, 'images/products/uploads/')) {
            return;
        }

        $absolutePath = public_path($imagePath);

        if (is_file($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
