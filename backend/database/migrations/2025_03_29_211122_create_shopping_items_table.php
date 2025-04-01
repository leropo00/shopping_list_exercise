<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Enums\PurchaseItemStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TABLE_PURCHASE_LIST, function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->integer('quantity');
            $table->enum('status',array_column(PurchaseItemStatus::cases(), 'value'))->default(PurchaseItemStatus::UNCHECKED->value);
            $table->string('shopping_owner')->nullable();
            $table->dateTime('checked_date')->nullable();
            $table->integer('checked_quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TABLE_PURCHASE_LIST);
    }
};
