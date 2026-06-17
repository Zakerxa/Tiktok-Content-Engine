<?php

use App\Http\Controllers\TikTokPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ဘာမှမရှိတဲ့အောက်မှာ ဒီကောင်လေး လှမ်းထည့်လိုက်ပါ
Route::post('/posts/upload', [TikTokPostController::class, 'upload']);