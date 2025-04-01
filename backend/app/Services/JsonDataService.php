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
    public function getJsonData(): string
    {
        return $jsonContent = PurchaseItem::all()->toJson(JSON_PRETTY_PRINT);
    }

    public function checkErrorsInJsonData(array $json): void
    {
        // validation on element by element, if data is correct
        $validator = Validator::make($json, [
            '*.item_name' => ['required', 'string', 'min:1' ,'max:255'],
            '*.quantity' => ['required', 'numeric', 'min:1'],
            '*.status' => [Rule::enum(PurchaseItemStatus::class)],

            '*.shopping_owner' => ['required_if:*.status,'.PurchaseItemStatus::IN_SHOPPING->value],

            '*.checked_date'  => ['required_if:*.status,'.PurchaseItemStatus::CHECKED->value], 
            '*.checked_quantity'  => ['required_with:checked_date', 'lte:quantity', 'min:1'], 
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

    public function parseJsonData(array $json): void
    {
        // get all the values

        DB::transaction(function() use($json) {
            $existingRecords = PurchaseItem::all()->mapWithKeys(function ($item, $key) {
                return [$item['id'] => false];
            });


            foreach ($json as $item) {

                PurchaseItem::where('id', $item['id']);

                // if unchecked
                // find if item with same_item name
                // update quantity if different


                // if in_shopping
                // find if item with same_item 
                // if found

                // not found create


                // if checked
                // same_name, shopping_owner and checked_data

            }

        });		

    }

    public function triggerEventChanged(): void
    {
        PurchaseListEvent::create([
            'event' => PURCHASE_LIST_EVENT_IMPORT,
        ]);
    }
}
