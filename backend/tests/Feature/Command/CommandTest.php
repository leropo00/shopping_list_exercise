<?php

namespace Tests\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;
use App\Enums\PurchaseItemStatus;
use App\Models\PurchaseItem;

class CommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_fails_no_parameter(): void
    {
        $this->assertThrows(
            fn () => $this->artisan('app:export-shopping-list')->assertExitCode(0),
            RuntimeException::class
        );
    }

    public function test_import_fails_no_parameter(): void
    {
        $this->assertThrows(
            fn () => $this->artisan('app:import-shopping-list')->assertExitCode(0),
            RuntimeException::class
        );
    }

    public function test_both_commands_work(): void
    {
        $outfile = 'test_file_command_' . time() . '.json';
        $this->prepareInputData();
        $this->assertDatabaseCount(TABLE_PURCHASE_LIST, 3);

        $this->artisan('app:export-shopping-list '.$outfile)->assertExitCode(0);

        DB::table(TABLE_PURCHASE_LIST)->delete();
        $this->assertDatabaseEmpty(TABLE_PURCHASE_LIST);

        $this->artisan('app:import-shopping-list '.$outfile)->assertExitCode(0);
        $this->assertDatabaseCount(TABLE_PURCHASE_LIST, 3);

        Storage::delete($outfile);
    }

    private function prepareInputData(): void
    {
        PurchaseItem::create([
            "item_name" => "unchecked_item",
            "quantity" => 1,
            "status" => PurchaseItemStatus::UNCHECKED->value,
        ]);
        PurchaseItem::create([
            "item_name" => "in_shopping_item",
            "quantity" => 2,
            "status" => PurchaseItemStatus::IN_SHOPPING->value,
            "shopping_owner" => 1,
        ]);
        PurchaseItem::create([
            "item_name" => "checked_item",
            "quantity" => 3,
            "status" => PurchaseItemStatus::CHECKED->value,
            "checked_quantity" => 1,
            "checked_by_user_id" => 2,
            "checked_date" => "2025-04-23 22:58:28",
        ]);
    }
}