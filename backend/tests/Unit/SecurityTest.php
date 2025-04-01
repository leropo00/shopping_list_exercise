<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use Tests\TestCase;
use App\Models\PurchaseItem;
use App\Models\User;

class SecurityTest extends TestCase
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

    public function testXssVulnerabilityProtection(): void
    {
        $SANITIZED_OUTPUT = "alert('XSS');";

        $user = User::factory()->create();
        $response =  $this->actingAs($user)->postJson('/api/purchase_items', [
            'item_name' => "<script>alert('XSS');</script>", 
            'quantity' => 1,
        ]);
        $response
            ->assertStatus(ResponseCode::HTTP_CREATED)
            ->assertJson(['item_name' => $SANITIZED_OUTPUT]);

        $bodyResponse = $response->decodeResponseJson();
        $itemId =  $bodyResponse["id"];
        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'id' =>  $itemId,
            'item_name' => $SANITIZED_OUTPUT,
        ]);
        
        
        $response =  $this->actingAs($user)->putJson('/api/purchase_items/' . $itemId, [
            'item_name' => "<body onload=alert('test1')>Text", 
            'quantity' => 1,
        ])->assertStatus(ResponseCode::HTTP_OK);

        $this->assertDatabaseHas(TABLE_PURCHASE_LIST, [
            'id' =>  $itemId,
            'item_name' => 'Text',
        ]);
    }
}
