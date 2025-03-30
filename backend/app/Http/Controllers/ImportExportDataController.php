<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PurchaseItem;
use App\Services\JsonDataService;

class ImportExportDataController extends Controller
{
    protected $jsonDataService;

    public function __construct(JsonDataService $jsonDataService)
    {
        $this->jsonDataService = $jsonDataService;
    }

    public function downloadJson()
    {
        $jsonContent = $this->jsonDataService->getJsonData();
        return response()->stream(
            function () use ($jsonContent) {
                echo $jsonContent;
            },
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="purchase_list.json"',
            ]
        );
    }
}
