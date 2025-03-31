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
        Schema::create(TABLE_PURCHASE_LIST_EVENTS, function (Blueprint $table) {
            $table->id();
            $table->text("event");
            $table->integer("user_id");
            $table->integer("record_id")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TABLE_PURCHASE_LIST_EVENTS);
    }
};
