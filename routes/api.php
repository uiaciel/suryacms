<?php

use Uiaciel\SuryaCms\Http\Controllers\Api\MonitorController;
use Illuminate\Support\Facades\Route;

Route::get('/system/monitor', [MonitorController::class, 'index']);
Route::post('/surya-monitor/backup', [MonitorController::class, 'triggerBackup']);
