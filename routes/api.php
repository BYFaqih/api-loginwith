<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\MainController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('auth', [GoogleController::class, 'redirectToGoogle']);
// Route::get('auth/callback', [GoogleController::class, 'handleAuthCallback']);

Route::POST('/register', [MainController::class, 'register']);
Route::POST('/login', [MainController::class, 'login']);
Route::GET('/index', [MainController::class, 'index']);
Route::GET('/me', [MainController::class, 'me']);
