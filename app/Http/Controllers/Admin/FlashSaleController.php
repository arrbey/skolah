<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Course;
use App\Models\Bootcamp;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::withCount('items')->latest()->paginate(10);
        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        return view('admin.flash-sales.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at'    => 'required|date',
            'end_at'      => 'required|date|after:start_at',
            'is_active'   => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);
        $data['is_active'] = $request->has('is_active');

        FlashSale::create($data);

        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash Sale berhasil dibuat.');
    }

    public function edit(FlashSale $flashSale)
    {
        $flashSale->load('items.itemable');
        
        $courses = Course::published()->get();
        $bootcamps = Bootcamp::upcoming()->get();
        $books = Book::published()->get();

        return view('admin.flash-sales.edit', compact('flashSale', 'courses', 'bootcamps', 'books'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at'    => 'required|date',
            'end_at'      => 'required|date|after:start_at',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $flashSale->update($data);

        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash Sale berhasil diperbarui.');
    }

    /**
     * Add an item to the flash sale.
     */
    public function addItem(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'item_type'        => 'required|in:Course,Bootcamp,Book',
            'item_id'          => 'required|integer',
            'flash_sale_price' => 'required|integer|min:0',
            'limit_quantity'   => 'nullable|integer|min:1',
        ]);

        $typeMap = [
            'Course'   => \App\Models\Course::class,
            'Bootcamp' => \App\Models\Bootcamp::class,
            'Book'     => \App\Models\Book::class,
        ];

        $fullType = $typeMap[$request->item_type];

        // Check if already exists
        $exists = $flashSale->items()
            ->where('itemable_type', $fullType)
            ->where('itemable_id', $request->item_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Item ini sudah ada dalam Flash Sale.');
        }

        $flashSale->items()->create([
            'itemable_type'    => $fullType,
            'itemable_id'      => $request->item_id,
            'flash_sale_price' => $request->flash_sale_price,
            'limit_quantity'   => $request->limit_quantity,
        ]);

        return back()->with('success', 'Item berhasil ditambahkan ke Flash Sale.');
    }

    public function removeItem(FlashSaleItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item berhasil dihapus dari Flash Sale.');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();
        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash Sale berhasil dihapus.');
    }
}
