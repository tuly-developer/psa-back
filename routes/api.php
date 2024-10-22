<?php

use App\Http\Controllers\Api\V1\FileManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("v1")->group(function () {
    Route::prefix("file-manager")->group(function () {
        Route::get('files', [FileManagerController::class, 'index']);
        Route::post('safit', [FileManagerController::class, 'processSafitFile']);
        Route::post('payment/upload', [FileManagerController::class, 'uploadPaymentFile']);
        Route::post('payment', [FileManagerController::class, 'processPaymentFile']);
        Route::post('targz/upload', [FileManagerController::class, 'uploadTargzFile']);
        Route::post('targz/xls', [FileManagerController::class, 'processXlsInsideTargzFile']);
    });
});