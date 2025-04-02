<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PurchaseItemStatus;

class PurchaseItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = TABLE_PURCHASE_LIST;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['item_name', 'quantity', 'status', 'shopping_owner', 'checked_by_user_id', 'checked_date', 'checked_quantity'];

    /**
     * Scope a query to only include items on the list.
     */
    public function scopeEditable(Builder $query): void
    {
        $query->where('status', PurchaseItemStatus::UNCHECKED->value);
    }

   /**
     * Scope a query to only include items that are currenlty in shopping.
     */
    public function scopeInShopping(Builder $query): void
    {
        $query->where('status', PurchaseItemStatus::IN_SHOPPING->value);
    }

   /**
     * Scope a query to only include items on the list.
     */
    public function scopeChecked(Builder $query): void
    {
        $query->where('status', PurchaseItemStatus::CHECKED->value);
    }
}
