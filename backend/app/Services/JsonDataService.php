<?php

namespace App\Services;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Enums\PurchaseItemStatus;
use App\Models\PurchaseItem;
use App\Models\PurchaseListEvent;

class JsonDataService
{
    /**
     * Export all the shopping list records into formatted json string
     *
     *  @return string
     */
    public function getJsonData(): string
    {
        return $jsonContent = PurchaseItem::all()->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Validate if data in json file
     *
     *  @param  array  $json
     */
    public function checkErrorsInJsonData(array $json): void
    {
        // validation on element by element, if data is correct
        $validator = Validator::make($json, [
            '*.item_name' => ['required', 'string', 'min:1' ,'max:255'],
            '*.quantity' => ['required', 'numeric', 'min:1'],
            '*.status' => [Rule::enum(PurchaseItemStatus::class)],
            '*.shopping_owner' => ['required_if:*.status,'.PurchaseItemStatus::IN_SHOPPING->value, 'nullable'],
            '*.checked_by_user_id'  => ['required_if:*.status,'.PurchaseItemStatus::CHECKED->value, 'nullable'], 
            '*.checked_date'  => ['required_if:*.status,'.PurchaseItemStatus::CHECKED->value, 'nullable'], 
            '*.checked_quantity'  => ['required_with:checked_date', 'lte:*.quantity', 'nullable'], 
        ]);
        $validator->validate();

        // unchecked items or items in shopping must have unique item name
        $collection = collect($json);
        $duplicateUnchecked = $collection->where('status', PurchaseItemStatus::UNCHECKED->value)->groupBy('item_name')->map->count()->filter(function (int $value, string $key) {
            return $value >= 2;
        });
        $duplicateInShopping = $collection->where('status', PurchaseItemStatus::IN_SHOPPING->value)->groupBy('item_name')->map->count()->filter(function (int $value, string $key) {
            return $value >= 2;
        });

        $duplicationErrors = [];
        if ($duplicateUnchecked->count() > 0) {
            $duplicationErrors[] = [ 'duplicate_unchecked_items' => $duplicateUnchecked->keys()->join(', ')];
        }
        if ($duplicateInShopping->count() > 0) {
            $duplicationErrors[] = [ 'duplicate_in_shopping_items' => $duplicateInShopping->keys()->join(', ')];
        }
        if (!empty($duplicationErrors)) {
            throw ValidationException::withMessages($duplicationErrors);
        }
    }

    /**
     *  Parses the json data and based on current database state,
     *  either updates the existing records or inserts new record.
     *  This way no duplicated records are inserted, rules are described in readme file.
     *  This does not remove existing records, if you would like a clear state,
     *  you need to remove all the purchase list items before.
     * 
     *  @param  array  $json
     */
    public function parseJsonData(array $json): void
    { 
        DB::transaction(function() use($json) {

            /*
                my initial idea was to delete all the records,
                that are no longer present in the imported file
                but since action to delete all the records is available to each user in interface
                I have decided to leave records, so that you can append previous records to exisiting state
             */

            foreach ($json as $item) {
                /*
                    id fields are ignored, because we may import previous state from some time ago,
                    but the autoincrement field for id has increased in the meantime
                    so records may be present with identical data, but different id
                 */

                $result = PurchaseItem::where('item_name', $item['item_name'])->where('status', $item['status'])->
                when($item['status'] == PurchaseItemStatus::CHECKED->value, function ($q) use ($item) {
                    return $q->where('checked_date', $item['checked_date'])->where('checked_by_user_id', $item['checked_by_user_id']);
                })->first();

                if ($result) {
                    $this->updateExistingItem($result, $item);
                }
                else {
                    $this->createNewPurchaseItem($item);
                }
            }

        });		

    }

    /**
     *  Triggers an event when import is performed,
     *  so that users application is refreshed with new data
     *
     */
    public function triggerEventChanged(): void
    {
        PurchaseListEvent::create([
            'event' => PURCHASE_LIST_EVENT_IMPORT,
        ]);
    }

    private function createNewPurchaseItem(array $item): void
    {
        $newItem = new PurchaseItem;
        $newItem->item_name = $item['item_name'];
        $newItem->quantity = $item['quantity'];
        $newItem->status = $item['status'];

        // certain fields are not copied, even if data is present, based on the status
        // as item should not have these fields based on state
        if ($newItem->status== PurchaseItemStatus::IN_SHOPPING->value) {
            $newItem->shopping_owner = $item['shopping_owner'];
            $newItem->checked_quantity = $item['checked_quantity'];
        }
        else if ($newItem->status == PurchaseItemStatus::CHECKED->value) {
            $newItem->checked_by_user_id = $item['checked_by_user_id'];
            $newItem->checked_date = $item['checked_date'];
            $newItem->checked_quantity = $item['checked_quantity'];
        }
        $newItem->save();
    }

    private function updateExistingItem(PurchaseItem $result, array $item): void
    {
        $saveChanges = false;

        if ($result->quantity != $item['quantity'])  {
            $result->quantity =  $item['quantity'];
            $saveChanges = true;
        }

        if ($item['status'] == PurchaseItemStatus::IN_SHOPPING->value) {
            if ($result->checked_quantity != $item['checked_quantity'])  {
                $result->checked_quantity = $item['checked_quantity'];
                $saveChanges = true;
            } 
            if ($result->shopping_owner != $item['shopping_owner'])  {
                $result->shopping_owner = $item['shopping_owner'];
                $saveChanges = true;
            } 
        } 
        else if ($item['status'] == PurchaseItemStatus::CHECKED->value) {
            if ($result->checked_quantity != $item['checked_quantity'])  {
                $result->checked_quantity = $item['checked_quantity'];
                $saveChanges = true;
            } 
        } 

        if ($saveChanges) {
            $result->save();
        }
    }
}