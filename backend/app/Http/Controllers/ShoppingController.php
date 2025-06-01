<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use App\Enums\PurchaseItemStatus;
use App\Models\PurchaseItem;
use App\Models\PurchaseListEvent;

class ShoppingController extends Controller
{
    /**
     * Starts the shipping with purchase list items
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function start(Request $request): Response
    {
        if (PurchaseItem::inShopping()->count() > 0) {
                return response(['message' => 'Items are already in shopping, wait until shopping is done',
                'errors' => [ERROR_ITEMS_ALREADY_IN_SHOPPING],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }

        if (PurchaseItem::editable()->count() == 0) {
            return response(['message' => 'No items are available for shopping',
                'errors' => [ERROR_NO_ITEMS_AVAILABLE_FOR_SHOPPING],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }
        $userId = $request->user()->id;
        DB::transaction(function() use($userId) {
            PurchaseItem::editable()
                ->update(['status' => PurchaseItemStatus::IN_SHOPPING->value, 'shopping_owner' => $userId]);
            $this->triggerShoppingEvent($userId);
        });
        return response(['count_in_shopping' => PurchaseItem::inShopping()->count()], ResponseCode::HTTP_OK);
    }

    /**
     * Change checked quantity for an item that is currently in_shopping
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id): Response
    {
        $request->validate([
            'checked_quantity' => ['required', 'numeric','min:0'],
        ]);

        $existingItem = PurchaseItem::findOrFail($id);

        if ($existingItem->status != PurchaseItemStatus::IN_SHOPPING->value) {
            return response(['message' => 'Item is not in shopping',
                'errors' => [ERROR_ITEM_NOT_IN_SHOPPING],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }
        if ($existingItem->quantity < $request->checked_quantity ) {
            return response(['message' => 'Checked quantity is larger that quantity field',
                'errors' => [ERROR_CHECKED_QUANITY_LARGER_THAN_QUANTITY],
            ], ResponseCode::HTTP_BAD_REQUEST );            
        }

        if ($existingItem->shopping_owner != $request->user()->id) {
            return response(['message' => 'User is not the shopping owner',
                'errors' => [ERROR_USER_NOT_SHOPPING_OWNER],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }

        DB::transaction(function() use($request, $existingItem) {
            $existingItem->checked_quantity = $request->checked_quantity;
            $existingItem->save();
            $this->triggerShoppingEvent($request->user()->id);
        });		

        return response(['remain_quantity' => $existingItem->quantity -  $existingItem->checked_quantity], ResponseCode::HTTP_OK);
    }

    /**
     * Finish shopping with active purchase items, adjust back the quantities, that were not pruchases in whole
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request): Response
    {
        $userId = $request->user()->id;
        $recordCount = $this->prepareQueryStart($userId)->count();

        if ($recordCount == 0) {
            return response(['message' => 'User has no items in shopping',
                'errors' => [ERROR_NO_ITEMS_AVAILABLE_FOR_SHOPPING],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }

        DB::transaction(function() use($userId) {
            // return those without checked quantity, back to checked status
            $this->prepareQueryStart($userId)
                    ->where('checked_quantity', '=', 0)
                    ->update(['status' => PurchaseItemStatus::UNCHECKED->value, 'shopping_owner' => null]);

            // remaning quantity that was not checked is added back to items with same name
            // or a new items if item with same name is not present in unchecked items
            $partialyChecked = $this->prepareQueryStart($userId)
                ->where('checked_quantity', '>', 0)
                ->whereRaw('checked_quantity < quantity')->get();

            foreach ($partialyChecked as $data) {
                $existingItem = PurchaseItem::editable()->where('item_name', $data->item_name)->first();
                $quantityDifference = ($data['quantity'] - $data['checked_quantity']);
                if ($existingItem) {
                    $existingItem->quantity = $existingItem->quantity + $quantityDifference;
                    $existingItem->save();
                    continue;
                }
                PurchaseItem::create([
                    'item_name' => $data->item_name,
                    'quantity' => $quantityDifference,
                    'status' => PurchaseItemStatus::UNCHECKED->value,
                ]);
            }

            $this->prepareQueryStart($userId)
                ->where('checked_quantity', '>', 0)
                ->update(['status' => PurchaseItemStatus::CHECKED->value, 
                'checked_by_user_id' => $userId,
                'checked_date' =>  Carbon::now(),
                'shopping_owner' => null,]);
            $this->triggerShoppingEvent($userId);
        });
        return response(['count_added_to_checkout' => $recordCount], ResponseCode::HTTP_OK);
    }
    
    private function prepareQueryStart(int $userId): Builder
    {
        return PurchaseItem::inShopping()->where('shopping_owner', $userId);
    }

    private function triggerShoppingEvent(int $userId): void
    {
        PurchaseListEvent::create([
            'event' => PURCHASE_LIST_EVENT_SHOPPING,
            'user_id' => $userId,
        ]);
    }
}
