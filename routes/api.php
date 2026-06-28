<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// authentication routes
Route::post('register', [UserController::class, 'register'])->name('register');
Route::post('login', [UserController::class, 'login'])->name('login');


// shortcut for creating all the CRUD api routes
Route::apiResource('tasks', TaskController::class)->middleware('auth:sanctum');
