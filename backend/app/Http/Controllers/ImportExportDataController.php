<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

use App\Models\PurchaseItem;
use App\Services\JsonDataService;

class ImportExportDataController extends Controller
{
    public function __construct(protected JsonDataService $jsonDataService){}

    public function list()
    {
        $jsonContent = $this->jsonDataService->getJsonData();
        return response()->stream(
            function () use ($jsonContent) {
                echo $jsonContent;
            },
            ResponseCode::HTTP_OK,
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
            return response([  'message' => 'File contents are not json',
                'errors' => [ERROR_NOT_JSON_FILE],
            ], ResponseCode::HTTP_BAD_REQUEST );
        }
        $jsonContents = json_decode($fileContents, true); 
        $this->jsonDataService->checkErrorsInJsonData($jsonContents);
        $this->jsonDataService->parseJsonData($jsonContents);
        $this->jsonDataService->triggerEventChanged();
        return response($fileContents, ResponseCode::HTTP_CREATED);
    }
}
