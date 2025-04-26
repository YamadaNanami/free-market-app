<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\TopController;
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

Route::get('/', [TopController::class, 'index'])->name('top.index');

Route::get('/search', [TopController::class, 'saveKeyword']);

// 商品詳細画面の表示
Route::get('/item/:{item_id}', [ItemController::class, 'index'])->name('item.detail');

Route::middleware('auth')->group(function () {
    //ログインしないと見れないページの処理はここに記載する

    Route::group(['prefix' => 'mypage'], function () {
        // マイページ画面の表示
        Route::get('/', [MypageController::class, 'index']);

        // プロフィール設定画面の表示
        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');

        // プロフィールの更新処理
        Route::patch('profile', [ProfileController::class, 'update']);
    });

    Route::group(['prefix' => 'sell'], function () {
        // 出品画面の表示
        Route::get('/', [SellController::class, 'index']);

        // 商品の出品（登録）処理
        Route::post('/', [SellController::class, 'sell']);
    });

    Route::group(['prefix' => 'item/:{item_id}'], function () {
        // いいね機能
        Route::post('like', [ItemController::class, 'toggleLike'])->name('like');

        // コメント送信処理
        Route::post('comment', [ItemController::class, 'createComment'])->name('comment');
    });

    Route::group(['prefix' => 'purchase'], function () {
        // 商品購入画面の表示
        Route::get(':{item_id}', [PurchaseController::class, 'index']);

        // 支払い方法の表示
        Route::get('payment', [PurchaseController::class, 'selectPayment']);

        // 送付先住所変更画面の表示
        Route::get('address/:{item_id}', [PurchaseController::class, 'edit'])->name('address.edit');

        // 送付先住所変更処理
        Route::post('address/:{item_id}', [PurchaseController::class, 'store'])->name('address.store');
    });
});