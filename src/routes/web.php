<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index']);
Route::get('/purchase/::{item_id}', [ItemController::class, 'detail']);

Route::middleware('auth')->group(function () {
    //ログインしないと見れないページの処理はここに記載する
    Route::get('/mypage', [MypageController::class,'index']);

    //プロフィール設定画面の表示
    Route::get('/mypage/profile',[ProfileController::class,'index'])->name('profile.index');

    //プロフィールの更新処理
    Route::patch('/mypage/profile', [ProfileController::class, 'update']);

    // 出品画面の表示
    Route::get('/sell', [SellController::class, 'index']);
    Route::post('/sell', [SellController::class, 'sell']);
});