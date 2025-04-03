<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use Tests\TestCase;
use App\Enums\PurchaseItemStatus;
use App\Models\PurchaseItem;
use App\Models\User;

class ShoppingTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * Test that checks that input name, that input that could result in XSS vulnerability is removed
     * .
     */
     protected function setUp(): void
     {
         parent::setUp();
     } 

     public function test_item_in_shopping_checkout_can_not_be_updated(): void
     {
        $user = User::factory()->create();
        $response =  $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => 'item_name', 
            'quantity' => 1,
        ]);
        $response
            ->assertStatus(ResponseCode::HTTP_CREATED)
            ->assertJson(['item_name' => 'item_name']);
        $bodyResponse = $response->decodeResponseJson();
        $itemId =  $bodyResponse["id"];
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'id' =>  $itemId,
            'item_name' => 'item_name',
        ]);

        // prepare item for shopping
        $this->actingAs($user)->postJson('/api/shopping_list')->assertStatus(ResponseCode::HTTP_OK);
        $this->actingAs($user)->putJson('/api/shopping_list/' . $itemId , [
            'checked_quantity' => 1,
        ])->assertStatus(ResponseCode::HTTP_OK);

        // when in shopping can't update the item
        $this->actingAs($user)->putJson('/api/purchase_items/' . $itemId , [
            'item_name' => 'item_name', 
            'quantity' => 2,
        ])->assertStatus(ResponseCode::HTTP_BAD_REQUEST)
            ->assertJson(['errors' => [ERROR_NON_EDITABLE_ITEM]]);

        // when checked out also can't update the item
        $this->actingAs($user)->postJson('/api/shopping_list/finish')->assertStatus(ResponseCode::HTTP_OK);
        $this->actingAs($user)->putJson('/api/purchase_items/' . $itemId , [
            'item_name' => 'item_name', 
            'quantity' => 2,
        ])->assertStatus(ResponseCode::HTTP_BAD_REQUEST)
            ->assertJson(['errors' => [ERROR_NON_EDITABLE_ITEM]]);
     }

     public function test_item_checked_quantity_larger_than_quantity(): void
     { 
        // prepare item for shopping
        $user = User::factory()->create();
        $response =  $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => 'item_name', 
            'quantity' => 1,
        ]);
        $response
            ->assertStatus(ResponseCode::HTTP_CREATED)
            ->assertJson(['item_name' => 'item_name']);
        $bodyResponse = $response->decodeResponseJson();
        $itemId =  $bodyResponse["id"];
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
             'id' =>  $itemId,
             'item_name' => 'item_name',
        ]);
        $this->actingAs($user)->postJson('/api/shopping_list')->assertStatus(ResponseCode::HTTP_OK);


        $this->actingAs($user)->putJson('/api/shopping_list/' . $itemId , [
            'checked_quantity' => 2,
        ])->assertStatus(ResponseCode::HTTP_BAD_REQUEST)
          ->assertJson(['errors' => [ERROR_CHECKED_QUANITY_LARGER_THAN_QUANTITY]]);
     }

     public function test_items_in_shopping_not_checked_are_added_back(): void
     { 
        // prepare the data
        $user = User::factory()->create();
        PurchaseItem::create([
            "item_name" => "unchecked_item",
            "quantity" => 1,
            "status" => PurchaseItemStatus::IN_SHOPPING->value,
            "shopping_owner" => $user->id,
        ]);
        $idPartialyChecked = PurchaseItem::create([
            "item_name" => "partialy_checked_item",
            "quantity" => 2,
            "status" => PurchaseItemStatus::IN_SHOPPING->value,
            "shopping_owner" =>  $user->id,
        ])->id;
        $idCompletelyChecked = PurchaseItem::create([
            "item_name" => "checked_item",
            "quantity" => 3,
            "status" => PurchaseItemStatus::IN_SHOPPING->value,
            "shopping_owner" => $user->id,
         ])->id;


        // send into shopping and do some checkout
        $this->actingAs($user)->putJson('/api/shopping_list/' . $idPartialyChecked , [
            'checked_quantity' => 1,
        ])->assertStatus(ResponseCode::HTTP_OK);
        $this->actingAs($user)->putJson('/api/shopping_list/' . $idCompletelyChecked , [
            'checked_quantity' => 3,
        ])->assertStatus(ResponseCode::HTTP_OK);


        // prepare another record and finish shopping
        PurchaseItem::create([
            "item_name" => "partialy_checked_item",
            "quantity" => 3,
            "status" => PurchaseItemStatus::UNCHECKED->value,
        ]);

        $this->actingAs($user)->postJson('/api/shopping_list/finish')->assertStatus(ResponseCode::HTTP_OK);

        //  check that data is consistent
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' => 'unchecked_item',
            'quantity' => 1,
            "status" => PurchaseItemStatus::UNCHECKED->value,
        ]);
        $this->assertDatabaseMissing(TABLE_PURCHASE_LIST, [
            'item_name' => 'unchecked_item',
            "status" => PurchaseItemStatus::CHECKED->value,
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' => 'checked_item',
            'quantity' => 3,
            'checked_quantity' => 3,
            "status" => PurchaseItemStatus::CHECKED->value,
        ]);

        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' => 'partialy_checked_item',
            'quantity' => 4,
            'checked_quantity' => 0,
            "status" => PurchaseItemStatus::UNCHECKED->value,
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' => 'partialy_checked_item',
            'quantity' => 2,
            'checked_quantity' => 1,
            "status" => PurchaseItemStatus::CHECKED->value,
        ]);
     }
}
