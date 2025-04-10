<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MatchController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Technician Routes
Route::get('/technicians', [TechnicianController::class, 'index']);
Route::post('/technicians', [TechnicianController::class, 'store']);
Route::put('/technicians/{id}', [TechnicianController::class, 'update']); // เพิ่ม PUT สำหรับแก้ไขช่าง
Route::delete('/technicians/{id}', [TechnicianController::class, 'destroy']);

// Task Routes
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::put('/tasks/{id}', [TaskController::class, 'update']); 
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);


Route::post('/match', [MatchController::class, 'match']);

