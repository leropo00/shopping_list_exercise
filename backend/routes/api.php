<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportExportDataController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PurchaseListController;
use App\Http\Controllers\ShoppingController;

Route::middleware(['auth:sanctum', 'XssSanitizer'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::prefix('/purchase_items')    ->group(function () {
            Route::get('', [PurchaseListController::class, 'list']);
            Route::get('/{id}', [PurchaseListController::class, 'show'])->whereNumber('id');
            Route::post('', [PurchaseListController::class, 'store']);
            Route::put('/{id}', [PurchaseListController::class, 'update'])->whereNumber('id');
            Route::delete('', [PurchaseListController::class, 'empty']);
            Route::delete('/{id}', [PurchaseListController::class, 'destroy'])->whereNumber('id');
        });
        Route::prefix('/purchase_list_data')    ->group(function () {
            Route::get('', [ImportExportDataController::class, 'list']);
            Route::post('', [ImportExportDataController::class, 'store']);
        });
        Route::prefix('/shopping_list')    ->group(function () {
            Route::post('', [ShoppingController::class, 'start']);
            Route::put('/{id}', [ShoppingController::class, 'update'])->whereNumber('id');
            Route::post('/finish', [ShoppingController::class, 'complete']);
        });
        Route::get('/notifications', [NotificationController::class, 'get']);
});



