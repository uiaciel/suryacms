<?php

use Illuminate\Support\Facades\Route;
use Uiaciel\SuryaCms\Http\Controllers\Api\MonitorController;

Route::prefix('api')->group(function () {
    Route::get('/system/monitor', [MonitorController::class, 'index']);
    Route::post('/surya-monitor/backup', [MonitorController::class, 'triggerBackup']);
    Route::post('/posts', [MonitorController::class, 'storePost']);
});
