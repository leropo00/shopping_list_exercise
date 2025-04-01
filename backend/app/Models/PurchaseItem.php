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
    protected $fillable = ['item_name', 'quantity'];

    /**
     * Scope a query to only include items on the list.
     */
    public function scopeEditable(Builder $query): void
    {
        $query->where('status', PurchaseItemStatus::UNCHECKED->value);
    }
}
