<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use App\Enums\PurchaseItemStatus;
use App\Models\PurchaseItem;
use App\Models\PurchaseListEvent;

class ShoppingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (PurchaseItem::inShopping()->count() > 0) {
                return response([  'message' => 'Items are already in shopping, wait until shopping is done',
                'errors' => [ERROR_ITEMS_ALREADY_IN_SHOPPING],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }

        if (PurchaseItem::editable()->count() == 0) {
            return response([  'message' => 'No items are available for shopping',
                'errors' => [ERROR_NOT_ITEMS_FOR_SHOPPING],
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

    private function triggerShoppingEvent($userId) 
    {
        PurchaseListEvent::create([
            'event' => PURCHASE_LIST_EVENT_SHOPPING,
            'user_id' => $userId,
        ]);
    }
}
