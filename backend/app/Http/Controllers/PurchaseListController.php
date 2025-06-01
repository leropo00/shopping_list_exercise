<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use App\Enums\PurchaseItemStatus;
use App\Models\PurchaseItem;
use App\Models\PurchaseListEvent;

class PurchaseListController extends Controller
{    
    /**
     * Creates a new purchase item record
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'item_name' => ['required', 'min:1', 'max:255'],
            'quantity' => ['required', 'numeric','min:1'],
        ]);

        if (PurchaseItem::editable()->where('item_name', $request->item_name)->count() > 0) {
            return response([  'message' => 'Item with the same name alredy exists',
                'errors' => [ERROR_EXISTING_ITEM],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }

        $item = DB::transaction(function() use($request) {
            $userId = $request->user()->id;
            $item = PurchaseItem::create([
                'item_name' => $request->item_name,
                'quantity' => $request->quantity,
            ]);
            PurchaseListEvent::create([
                'event' => PURCHASE_LIST_EVENT_ADD,
                'user_id' => $userId,
            ]);
            return $item;
        });		

        return response($item, ResponseCode::HTTP_CREATED);
    }

    /**
     * Update purchase item data that is editable, if item is currently in editable status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id): Response
    {
        $request->validate([
            'item_name' => ['required', 'min:1', 'max:255'],
            'quantity' => ['required', 'numeric','min:1'],
        ]);

        $existingItem = PurchaseItem::findOrFail($id);
        if ($existingItem->status != PurchaseItemStatus::UNCHECKED->value) {
            return response([  'message' => 'Item is not editable',
                'errors' => [ERROR_NON_EDITABLE_ITEM],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }

        if ($existingItem->item_name != $request->item_name && 
             PurchaseItem::editable()->whereNot('id', $id)->where('item_name', $request->item_name)->count() > 0) {
                return response([  'message' => 'Item with the same name alredy exists',
                    'errors' => [ERROR_EXISTING_ITEM],
                ], ResponseCode::HTTP_BAD_REQUEST );            
        }

        $item = DB::transaction(function() use($request, $existingItem) {
            $userId = $request->user()->id;
            $existingItem->item_name = $request->item_name;
            $existingItem->quantity = $request->quantity;
            $item = $existingItem->save();
            PurchaseListEvent::create([
                'event' => PURCHASE_LIST_EVENT_UPDATE,
                'user_id' => $userId,
                'record_id' => $existingItem->id,
            ]);
            return $item;
        });		
        return response($item, ResponseCode::HTTP_OK);
    }


    /**
     * Export all of the current data in application and download them as json file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $recordId
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, string $recordId): Response
    {
        $userId = $request->user()->id;
        DB::transaction(function() use($recordId, $userId) {
            PurchaseItem::editable()->where('id', $recordId)->delete();
            PurchaseListEvent::create([
                'event' => PURCHASE_LIST_EVENT_DELETE,
                'user_id' => $userId,
                'record_id' => $recordId,
            ]);
        });
        return response(null, ResponseCode::HTTP_NO_CONTENT);
    }

    /**
     * Clear all the records of a purchase list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function empty(Request $request): Response
    {
        $userId = $request->user()->id;
        DB::transaction(function() use($userId) {
            // PurchaseItem::editable()->delete();
            DB::table(TABLE_PURCHASE_LIST)->delete();

            PurchaseListEvent::create([
                'event' => PURCHASE_LIST_EVENT_DELETE_ALL,
                'user_id' => $userId,
            ]);
        });		
        return response(null, ResponseCode::HTTP_NO_CONTENT );
    }
    
    /**
     * Remove a purchase list item
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, String $id): Response
    {
        return response(PurchaseItem::where('id', $id)->get());
    }

    /**
     * List all the purchase list items
     *
     * @param  \Illuminate\Http\Request  $request    
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request): Response
    {
        return response(PurchaseItem::all());
    }
}