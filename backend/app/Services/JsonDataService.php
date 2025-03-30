<?php

namespace App\Services;

use App\Models\PurchaseItem;

class JsonDataService
{
    public function getJsonData(): string
    {
        return $jsonContent = PurchaseItem::all()->toJson(JSON_PRETTY_PRINT);
    }

    public function checkErrorsInJsonData(array $json): array
    {
        $errors = [];
        return $errors;
    }

    public function parseJsonData(array $json): void
    {
        
    }
}
