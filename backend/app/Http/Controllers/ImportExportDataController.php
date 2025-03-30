<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\PurchaseItem;
use App\Services\JsonDataService;

class ImportExportDataController extends Controller
{
    protected $jsonDataService;

    public function __construct(JsonDataService $jsonDataService)
    {
        $this->jsonDataService = $jsonDataService;
    }

    public function list()
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

	
	/**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        $fileContents = $request->file('file')->get();
        // workaround as validation inside request->validate didn't work   mimetypes:application/json,text/plain
        if (!Str::isJson($fileContents)) {
            return response("File contents are not json", 400);
        }

        $jsonContents = json_decode($fileContents, true); 
        $errors = $this->jsonDataService->checkErrorsInJsonData($jsonContents);
        if (!empty($errors)) {
            return response($errors, 400);
        }
        
        $this->jsonDataService->parseJsonData($jsonContents);
        return response(201);
    }
}
