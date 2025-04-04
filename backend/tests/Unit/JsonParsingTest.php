<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use App\Enums\PurchaseItemStatus;
use App\Models\PurchaseItem;
use App\Services\JsonDataService;

class JsonParsingTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new JsonDataService();
    }

    public function test_data_is_imported_correctly(): void
    {
        $json = [[
                "id" => 1,
                "item_name" => "unchecked_item",
                "quantity"  => 1,
                'status' => PurchaseItemStatus::UNCHECKED->value,
            ],
            [
                "id"  => 2,
                "item_name"  => "in_shopping_item",
                "quantity"  => 2,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
                "shopping_owner"  => 1,
                "checked_quantity"  => 0,
            ],
            [
                "id"  => 3,
                "item_name" => "checked_item",
                "quantity" => 3,
                "status" => PurchaseItemStatus::CHECKED->value,
                "checked_by_user_id" => 1,
                "checked_date" => "2025-04-23 22:58:28",
                "checked_quantity"  => 1,
            ],
        ];
        $this->service->parseJsonData($json);

        $this->assertDatabaseCount(TABLE_PURCHASE_LIST, 3);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "item_name" => "unchecked_item",
            "quantity"  => 1,
            'status' => PurchaseItemStatus::UNCHECKED->value,
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "item_name"  => "in_shopping_item",
            "quantity"  => 2,
            'status' => PurchaseItemStatus::IN_SHOPPING->value,
            "shopping_owner"  => 1,
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "item_name" => "checked_item",
            "quantity" => 3,
            "status" => PurchaseItemStatus::CHECKED->value,
            "checked_by_user_id" => 1,
            "checked_date" => "2025-04-23 22:58:28",
            "checked_quantity"  => 1,
        ]);
    }

    public function test_existing_data_is_refreshed(): void
    {
        $this->service->parseJsonData($this->initialData());
        $this->assertDatabaseCount(TABLE_PURCHASE_LIST, 3);
        // get record ids for later comparisons
        $idItemUnchecked  = PurchaseItem::
            where('item_name', 'inital_item')->
            where('status', PurchaseItemStatus::UNCHECKED->value)->
            first()->id;
        $idItemInShopping  = PurchaseItem::
            where('item_name', 'inital_item')->
            where('status', PurchaseItemStatus::IN_SHOPPING->value)->
            first()->id;
        $idItemChecked  = PurchaseItem::
            where('item_name', 'inital_item')->
            where('status', PurchaseItemStatus::CHECKED->value)->
            first()->id;

        $this->service->parseJsonData($this->updatedData());

        $this->assertDatabaseCount(TABLE_PURCHASE_LIST, 6);
        // existing data was updated
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "id" => $idItemUnchecked,
            "item_name" => "inital_item",
            "quantity"  => 4,
            'status' => PurchaseItemStatus::UNCHECKED->value,
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "id" => $idItemInShopping,
            "item_name"  => "inital_item",
            "quantity"  => 5,
            'status' => PurchaseItemStatus::IN_SHOPPING->value,
            "shopping_owner"  => 3,
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "id" => $idItemChecked,
            "item_name" => "inital_item",
            "quantity" => 6,
            "status" => PurchaseItemStatus::CHECKED->value,
            "checked_by_user_id" => 1,
            "checked_quantity"  => 3,
        ]);
        // new data was also created correctly
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "item_name" => "new_item",
            "quantity"  => 1,
            'status' => PurchaseItemStatus::UNCHECKED->value,    
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "item_name" => "new_item",
            "quantity"  => 1,
            'status' => PurchaseItemStatus::UNCHECKED->value,    
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            "item_name" => "new_item",
            "quantity"  => 1,
            'status' => PurchaseItemStatus::UNCHECKED->value,    
        ]);
    }

    private function initialData(): array
    {
        return [[
                "id" => 1,
                "item_name" => "inital_item",
                "quantity"  => 1,
                'status' => PurchaseItemStatus::UNCHECKED->value,
            ],
            [
                "id"  => 2,
                "item_name"  => "inital_item",
                "quantity"  => 2,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
                "shopping_owner"  => 1,
                "checked_quantity"  => 0,
            ],
            [
                "id"  => 3,
                "item_name" => "inital_item",
                "quantity" => 3,
                "status" => PurchaseItemStatus::CHECKED->value,
                "checked_by_user_id" => 1,
                "checked_date" => "2025-04-23 22:58:28",
                "checked_quantity"  => 1,
            ],
        ];
    }
    private function updatedData(): array
    {
        return [[
                "id" => 11,
                "item_name" => "inital_item",
                "quantity"  => 4,
                'status' => PurchaseItemStatus::UNCHECKED->value,
            ],
            [
                "id"  => 12,
                "item_name"  => "inital_item",
                "quantity"  => 5,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
                "shopping_owner"  => 3,
                "checked_quantity"  => 0,
            ],
            [
                "id"  => 13,
                "item_name" => "inital_item",
                "quantity" => 6,
                "status" => PurchaseItemStatus::CHECKED->value,
                "checked_by_user_id" => 1,
                "checked_date" => "2025-04-23 22:58:28",
                "checked_quantity"  => 3,
            ],
            [
                "id" => 1,
                "item_name" => "new_item",
                "quantity"  => 1,
                'status' => PurchaseItemStatus::UNCHECKED->value,
            ],
            [
                "id"  => 2,
                "item_name"  => "new_item",
                "quantity"  => 2,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
                "shopping_owner"  => 1,
                "checked_quantity"  => 0,
            ],
            [
                "id"  => 3,
                "item_name" => "new_item",
                "quantity" => 3,
                "status" => PurchaseItemStatus::CHECKED->value,
                "checked_by_user_id" => 1,
                "checked_date" => "2025-04-23 22:58:28",
                "checked_quantity"  => 1,
            ],
        ];
    }
}
