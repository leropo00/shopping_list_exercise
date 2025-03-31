<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportExportDataController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PurchaseListController;

Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::prefix('/purchase_items')    ->group(function () {
            Route::get('', [PurchaseListController::class, 'list']);

            Route::post('', [PurchaseListController::class, 'store']);

            Route::put('/{id}', [PurchaseListController::class, 'update']);

            Route::delete('', [PurchaseListController::class, 'empty']);
    
            Route::delete('/{id}', [PurchaseListController::class, 'destroy']);
        });

        Route::get('/purchase_list', [ImportExportDataController::class, 'list']);
});

Route::get('/purchase_items/{id}', [PurchaseListController::class, 'show']);

Route::post('/purchase_list', [ImportExportDataController::class, 'store']);

Route::get('/notifications', [NotificationController::class, 'get']);
