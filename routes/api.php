<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('v1')->group(function () {


    // Declare unauthenticated routes
    Route::group(['middleware' => 'guest'], function () {
        Route::any('/', function () {
            return response()->json(['message' => 'Welcome to Kulture Api'], 200);
        })->name('welcome');
    });


    // Declare authenticated routes

    //This is my change by Samuel
});
