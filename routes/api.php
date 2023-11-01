<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


if (!function_exists('restfulRoutes')) {
    function restfulRoutes($path, $controller)
    {
        Route::get($path . '/', $controller . '@index');
        Route::get($path . '/{id}', $controller . '@show');
        Route::post($path, $controller . '@create');
        Route::put($path . '/{id}', $controller . '@update');
        Route::delete($path . '/{id}', $controller . '@delete');
    }
}

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
