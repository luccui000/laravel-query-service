<?php

use App\Http\Controllers\LucQLController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("lucql", LucQLController::class);
Route::post("login", [LucQLController::class, 'login']);
Route::post("register", [LucQLController::class, 'register']);
