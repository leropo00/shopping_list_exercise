<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Exceptions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use App\Enums\PurchaseItemStatus;
use App\Services\JsonDataService;

class JsonValidationTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new JsonDataService();
    }

    public function test_correct_input_each_status(): void
    {
        Exceptions::fake();
        $json = [[
                'item_name' => 'item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::UNCHECKED->value,
            ],
            [
                'item_name' => 'item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
                'shopping_owner' => 1,
            ],
            [
                'item_name' => 'item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::CHECKED->value,
                'checked_by_user_id'  => 1, 
                'checked_date'  => '2025-04-23 22:58:28',
                'checked_quantity'  => 4,     
            ],
        ];
        $this->service->checkErrorsInJsonData($json);
        Exceptions::assertNotReported(ValidationException::class);
    }

    public function test_error_non_existing_status(): void
    {
        Exceptions::fake();
        $json = [[
            'item_name' => 'item',
            'quantity' => 4,
            'status' => 'stolen',
        ]];
        $this->assertThrows(
            fn () => $this->service->checkErrorsInJsonData($json),
            ValidationException::class
        );
    }

    public function test_error_zero_quantity(): void
    {
        Exceptions::fake();
        $json = [[
            'item_name' => 'item',
            'quantity' => 0,
            'status' => PurchaseItemStatus::UNCHECKED->value,
        ]];
        $this->assertThrows(
            fn () => $this->service->checkErrorsInJsonData($json),
            ValidationException::class
        );
    }

    public function test_error_blank_item_name(): void
    {
        Exceptions::fake();
        $json = [[
            'item_name' => '  ',
            'quantity' => 4,
            'status' => PurchaseItemStatus::UNCHECKED->value,
        ]];
        $this->assertThrows(
            fn () => $this->service->checkErrorsInJsonData($json),
            ValidationException::class
        );
    }

    public function test_error_checke_largerl(): void
    {
        Exceptions::fake();
        $json = [[
                'item_name' => 'item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::CHECKED->value,
                'checked_by_user_id'  => 1, 
                'checked_date'  => '2025-04-23 22:58:28',
                'checked_quantity'  => 6,     
            ],
        ];
        $this->assertThrows(
            fn () => $this->service->checkErrorsInJsonData($json),
            ValidationException::class
        );
    }

    public function test_error_duplicates_status_unchecked(): void
    {
        Exceptions::fake();
        $json = [[
                'item_name' => 'same_item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::UNCHECKED->value,
            ],
            [
                'item_name' => 'same_item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::UNCHECKED->value,
            ],
            [
                'item_name' => 'same_item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::UNCHECKED->value,
            ],
            [
                'item_name' => 'predmet',
                'quantity' => 4,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
                'shopping_owner' => 1,
            ],
            [
                'item_name' => 'predmet',
                'quantity' => 4,
                'status' => PurchaseItemStatus::CHECKED->value,
                'checked_by_user_id'  => 1, 
                'checked_date'  => '2025-04-23 22:58:28',
                'checked_quantity'  => 4,     
            ],
        ];
        $this->assertThrows(
            fn () => $this->service->checkErrorsInJsonData($json),
            ValidationException::class
        );
    }

    public function test_error_duplicates_status_in_shopping(): void
    {
        Exceptions::fake();
        $json = [[
                'item_name' => 'predmet',
                'quantity' => 4,
                'status' => PurchaseItemStatus::UNCHECKED->value,
                'shopping_owner' => 1,
            ],
            [
                'item_name' => 'same_item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
            ],
            [
                'item_name' => 'same_item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
            ],
            [
                'item_name' => 'same_item',
                'quantity' => 4,
                'status' => PurchaseItemStatus::IN_SHOPPING->value,
            ],
            [
                'item_name' => 'predmet',
                'quantity' => 4,
                'status' => PurchaseItemStatus::CHECKED->value,
                'checked_by_user_id'  => 1, 
                'checked_date'  => '2025-04-23 22:58:28',
                'checked_quantity'  => 4,     
            ],
        ];
        $this->assertThrows(
            fn () => $this->service->checkErrorsInJsonData($json),
            ValidationException::class
        );
    }
}
