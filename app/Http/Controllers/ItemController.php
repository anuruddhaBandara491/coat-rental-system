<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // If it's an AJAX request from DataTables
        if ($request->ajax()) {
            $query = Item::with(['category:id,name'])->where('user_id', auth()->user()->id);

            return datatables()->of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];

                        $query->where(function($q) use ($searchValue) {
                            $q->orWhere('name', 'like', "%{$searchValue}%")
                            ->orWhere('coat_no', 'like', "%{$searchValue}%")
                            ->orWhere('material', 'like', "%{$searchValue}%")
                            ->orWhereHas('category', function($sq) use ($searchValue) {
                              $sq->where('name', 'like', "%{$searchValue}%");
                            });
                        });

                    }
                })
                ->addColumn('category_name', function ($item) {
                    return $item->category->name ?? 'N/A';
                })
                ->addColumn('color_display', function ($item) {
                    return '<div class="color-box" style="background-color: ' . $item->color . ';"></div>';
                })
                ->addColumn('action', function ($item) {
                    $viewUrl = route('item.view', $item->id);
                    $editUrl = route('item.edit', $item->id);

                    return '
                        <a href="' . $viewUrl . '"
                        title="View"
                        class="btn btn-sm btn-text-secondary rounded-pill btn-icon">
                            <i class="mdi mdi-eye-arrow-right-outline" style="color: #0a14ad;"></i>
                        </a>
                        <a href="' . $editUrl . '"
                        title="Edit"
                        class="btn btn-sm btn-text-secondary rounded-pill btn-icon">
                            <i class="mdi mdi-pencil-outline" style="color: #ff8000;"></i>
                        </a>
                        <a href="#"
                        title="Delete"
                        class="btn btn-sm btn-text-secondary rounded-pill btn-icon delete-item"
                        data-id="' . $item->id . '">
                            <i class="mdi mdi-delete" style="color: #800000"></i>
                        </a>
                    ';
                })
                ->rawColumns(['category_name', 'color_display', 'action'])
                ->make(true);
        }

        return view('pages.item.item-list');
    }

    public function store(ItemRequest $request)
    {
        $itemId = $this->generateUniqueItemId();
        $item = Item::create([
            'user_id' => auth()->user()->id,
            'name' => $request['name'],
            'coat_no' => $itemId,
            'description' => $request['description'] ?? '',
            'cost' => $request['cost'],
            'material' => $request['material'],
            'color' => $request['color'],
            'size' => $request['size'],
            'item_category_id' => $request['item_category_id'],

        ]);
        if ($request->hasFile('file')) {
            $imageName = time() . '-' . uniqid() . '.' . $request->file->extension();
            $request->file->move(public_path('uploads/item_image'), $imageName);

            $item->image = $imageName;
            $item->save();
        }

        return response()->json(['success' => 'Item created successfully ' . $itemId]);
    }

    public function create()
    {
        $itemCategories = ItemCategory::all();

        return view('pages.item.create-item', compact('itemCategories'));
    }

    public function generateUniqueItemId()
    {
        return DB::transaction(function () {
            $lastItem = Item::where('user_id', auth()->user()->id)
                ->orderByDesc('coat_no')
                ->lockForUpdate()
                ->first();

            if (!$lastItem) {
                $nextNumber = 1;
            } else {
                $lastNumber = intval(substr($lastItem->coat_no, 2));
                $nextNumber = $lastNumber + 1;
            }

            $newItemId = 'ST' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Double-check uniqueness
            while (Item::where('user_id', auth()->user()->id)
                ->where('coat_no', $newItemId)
                ->exists()) {
                $nextNumber++;
                $newItemId = 'ST' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }

            return $newItemId;
        });
    }

    public function view($id)
    {
        $item = Item::with(['category:id,name'])->find($id);
        $itemCategories = ItemCategory::all();

        return view('pages.item.view-item', compact('item', 'itemCategories'));
    }

    public function edit($id)
    {
        $item = Item::with(['category:id,name'])->find($id);
        $itemCategories = ItemCategory::all();

        return view('pages.item.edit-item', compact('item', 'itemCategories'));
    }

    public function update($id, ItemRequest $request)
    {
        $item = Item::find($id);
        $item->update([
            'name' => $request['name'],
            'coat_no' => $request['coat_no'],
            'description' => $request['description'],
            'cost' => $request['cost'],
            'material' => $request['material'],
            'color' => $request['color'],
            'size' => $request['size'],
            'item_category_id' => $request['item_category_id'],
        ]);
        if ($request->hasFile('file')) {
            $imageName = time() . '-' . uniqid() . '.' . $request->file->extension();
            $request->file->move(public_path('uploads/item_image'), $imageName);

            $item->image = $imageName;
            $item->save();
        }

        return back()->with('success', 'Item updated successfully');
    }

    public function destroy($id)
    {
        $item = Item::find($id);
        $item->delete();

        return response()->json(['success' => 'Item deleted successfully']);
    }
}
