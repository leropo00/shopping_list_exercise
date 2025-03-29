<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseItem;

class PurchaseListController extends Controller
{
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_name' => ['required', 'max:255'],
            'quantity' => ['required', 'numeric','min:1'],
        ]);

        // TODO: find in not checked exists with the same name
        if (PurchaseItem::editable()->where('item_name', $request->item_name)->count() > 0) {
            // TODO item alredy exists errpr
            return response("ERROR", 400);
        }

        $item = PurchaseItem::create([
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
        ]);

        return response($item, 201);
    }

    /**
     * Update an existing resource.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'item_name' => ['required', 'max:255'],
            'quantity' => ['required', 'numeric','min:1'],
        ]);

        // TODO: find if record by id exits, check for duplicate item
        // check that record is editable
        return response($item, 200);
    }

    /**
     * Delete an existing resource.
     */
    public function destroy(Request $request, string $id)
    {
        PurchaseItem::editable()->where('id', $id)->delete();
        return response(204);
    }

    /**
     * Clear all the items.
     */
    public function empty(Request $request)
    {
        PurchaseItem::editable()->delete();
        return response(204);
    }
    
    /**
     * Show specific items.
     */
    public function show(Request $request, String $id)
    {
        return response(PurchaseItem::where('id', $id)->get());
    }

    /**
     * Get all the items.
     */
    public function list(Request $request)
    {
        return response(PurchaseItem::all());
    }
}