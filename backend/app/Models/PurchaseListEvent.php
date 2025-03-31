<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseListEvent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = TABLE_PURCHASE_LIST_EVENTS;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['event', 'user_id', 'record_id'];

}
