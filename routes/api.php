<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperationsController;

Route::post('/', function () { return view('welcome'); });
Route::post('/transfer', [OperationsController::class, 'transfer']);

