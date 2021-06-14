<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\BlogsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    if ($request->user()->email === 'gilbertrrogers@icloud.com') {
        $admin = $request->user();
        $admin['admin'] = true;
        return $admin;
    }
    return $request->user();
});

Route::get('blog', [BlogsController::class, 'index']);
Route::get('blog/{id}', [BlogsController::class, 'show']);
Route::middleware('auth:sanctum')->post('blog/{id}', [BlogsController::class, 'update']);

Route::middleware('auth:sanctum')->resource('blog', BlogsController::class)->except(['index', 'show', 'update']);
