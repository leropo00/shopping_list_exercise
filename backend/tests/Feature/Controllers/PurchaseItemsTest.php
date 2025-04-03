<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use Tests\TestCase;
use App\Enums\PurchaseItemStatus;
use App\Models\PurchaseItem;
use App\Models\User;

class PurchaseItemsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    } 

    public function test_create_with_duplicated_name(): void
    {
        $user = User::factory()->create();
        $response =  $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => "item_name", 
            'quantity' => 1,
        ]);
        $response
            ->assertStatus(ResponseCode::HTTP_CREATED)
            ->assertJson(['item_name' => "item_name"]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' => 'item_name',
        ]);
    
        $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => "item_name", 
            'quantity' => 1,
        ])->assertStatus(ResponseCode::HTTP_BAD_REQUEST)
          ->assertJson(['errors' => [ERROR_EXISTING_ITEM]]);
    }

    public function test_update_item_to_existing_name(): void
    {
        $user = User::factory()->create();
        $UNIQUE_ITEM_NAME = 'unique_item_name';

        $response =  $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => $UNIQUE_ITEM_NAME, 
            'quantity' => 1,
        ]);
        $response
            ->assertStatus(ResponseCode::HTTP_CREATED)
            ->assertJson(['item_name' => $UNIQUE_ITEM_NAME]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' =>  $UNIQUE_ITEM_NAME,
        ]);
    
        $response =  $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => 'first_item_name', 
            'quantity' => 1,
        ]);
        $response->assertStatus(ResponseCode::HTTP_CREATED);
        $bodyResponse = $response->decodeResponseJson();
        $itemId =  $bodyResponse["id"];
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'id' => $itemId,
            'item_name' =>  'first_item_name',
        ]);
    
        $this->actingAs($user)->putJson('/api/purchase_items/' . $itemId, [
            'item_name' => 'second_item_name', 
            'quantity' => 1,
        ])->assertStatus(ResponseCode::HTTP_OK);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'id' => $itemId,
            'item_name' =>  'second_item_name',
        ]);

    
        $this->actingAs($user)->putJson('/api/purchase_items/' . $itemId, [
            'item_name' => $UNIQUE_ITEM_NAME, 
            'quantity' => 1,
        ])->assertStatus(ResponseCode::HTTP_BAD_REQUEST)
          ->assertJson(['errors' => [ERROR_EXISTING_ITEM]]);
    }

    public function test_can_create_same_name_when_in_shopping(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => "item_name", 
            'quantity' => 1,
          ])->assertStatus(ResponseCode::HTTP_CREATED)
            ->assertJson(['item_name' => "item_name"]);

        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' => 'item_name',
        ]);
    
        // still fails
        $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => "item_name", 
            'quantity' => 1,
        ])->assertStatus(ResponseCode::HTTP_BAD_REQUEST)
          ->assertJson(['errors' => [ERROR_EXISTING_ITEM]]);

        $this->actingAs($user)->postJson('/api/shopping_list')->assertStatus(ResponseCode::HTTP_OK);

        // now it works
        $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => "item_name", 
            'quantity' => 1,
        ])->assertStatus(ResponseCode::HTTP_CREATED)
          ->assertJson(['item_name' => "item_name"]);

        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' => 'item_name',
            'status' => PurchaseItemStatus::UNCHECKED->value,
        ]);
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'item_name' => 'item_name',
            'status' => PurchaseItemStatus::IN_SHOPPING->value,
        ]);
    }
}
