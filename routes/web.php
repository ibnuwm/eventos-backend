<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'system' => 'Vendor Event OS (EventOS.id) API Server',
        'version' => '12.0',
        'status' => 'ONLINE',
        'time' => now()->toIso8601String()
    ]);
});
