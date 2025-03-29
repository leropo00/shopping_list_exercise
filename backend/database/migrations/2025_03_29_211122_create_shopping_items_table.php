<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->enum('status', [PURCHASE_LIST_STATUS_EDITABLE, PURCHASE_LIST_STATUS_IN_SHOPPING, PURCHASE_LIST_STATUS_CHECKED])->default(PURCHASE_LIST_STATUS_EDITABLE);
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
