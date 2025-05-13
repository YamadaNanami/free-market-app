<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TopController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

// メール認証誘導画面の表示
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール確認のハンドラ
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 確認メールの再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// トップ画面表示
Route::get('/', [TopController::class, 'index'])->name('top.index');

// 商品検索
Route::get('/search', [TopController::class, 'storeTempKeyword']);

// 商品詳細画面の表示
Route::get('/item/:{item_id}', [ItemController::class, 'index'])->name('item.detail');

Route::middleware('auth')->group(function () {
    //ログインしないと見れないページの処理はここに記載する

    Route::group(['prefix' => 'mypage'], function () {
        // マイページ画面の表示
        Route::get('/', [MypageController::class, 'index'])->name('mypage.index');

        // プロフィール設定画面の表示
        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');

        // プロフィール画像の一時保存
        Route::post('profile/img', [ProfileController::class, 'saveTempImg']);

        // プロフィールの登録処理
        Route::post('profile', [ProfileController::class, 'store']);

        // プロフィールの更新処理
        Route::patch('profile', [ProfileController::class, 'update']);
    });

    Route::group(['prefix' => 'sell'], function () {
        // 出品画面の表示
        Route::get('/', [SellController::class, 'index'])->name('sell.index');

        // 商品画像の一時保存
        Route::post('img', [SellController::class, 'saveTempImg']);

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
        Route::get(':{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');

        // 支払い方法の表示
        Route::post('payment', [PurchaseController::class, 'selectPayment']);

        // 送付先住所変更画面の表示
        Route::get('address/:{item_id}', [PurchaseController::class, 'editAddress'])->name('address.edit');

        // 送付先住所変更処理
        Route::post('address/:{item_id}', [PurchaseController::class, 'storeTempAddress'])->name('address.store');

        // 商品購入処理（stripe)
        Route::post('/checkout:{item_id}', [StripeController::class, 'checkout'])->name('stripe.checkout');

        // 決済成功時の処理
        Route::get('/checkout/success', [StripeController::class, 'success'])->name('stripe.success');

    });
});