<?php

namespace App\Enums;

enum PurchaseItemStatus: string {
    case UNCHECKED = 'unchecked';
    case IN_SHOPPING = 'in_shopping';
    case CHECKED = 'checked';
}
