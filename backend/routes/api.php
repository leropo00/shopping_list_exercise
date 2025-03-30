<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportExportDataController;
use App\Http\Controllers\PurchaseListController;

Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
});

Route::get('/purchase_items', [PurchaseListController::class, 'list']);

Route::get('/purchase_items/{id}', [PurchaseListController::class, 'show']);

Route::post('/purchase_items', [PurchaseListController::class, 'store']);

Route::put('/purchase_items/{id}', [PurchaseListController::class, 'update']);

Route::delete('/purchase_items', [PurchaseListController::class, 'empty']);

Route::delete('/purchase_items/{id}', [PurchaseListController::class, 'destroy']);

Route::get('/purchase_list', [ImportExportDataController::class, 'list']);

Route::post('/purchase_list', [ImportExportDataController::class, 'store']);
