<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
// カート
use App\Http\Controllers\CartController;
// トップページ
use App\Http\Controllers\WebController;
use App\Http\Controllers\ReviewController;

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
// トップページ
Route::get('/', [WebController::class, 'index']);

// カートの中身を確認するページへのURLを設定し、CartControllerでグルーピング
Route::controller(CartController::class)->group(function () {
    Route::get('users/carts', 'index')->name('carts.index');
    // カートへ追加する処理
    Route::post('users/carts', 'store')->name('carts.store');
    // カートから消去する処理
    Route::delete('users/carts', 'destroy')->name('carts.destroy');
});

// ユーザー情報関連の各ルーティングをUserControllerでグルーピング
Route::controller(UserController::class)->group(function () {
    Route::get('users/mypage', 'mypage')->name('mypage');
    Route::get('users/mypage/edit', 'edit')->name('mypage.edit');
    Route::put('users/mypage', 'update')->name('mypage.update');
    // パスワード変更・更新
    Route::get('users/mypage/password/edit', 'edit_password')->name('mypage.edit_password');
    Route::put('users/mypage/password', 'update_password')->name('mypage.update_password');
    // お気に入り一覧
    Route::get('users/mypage/favorite', 'favorite')->name('mypage.favorite');
    // 退会
    Route::delete('users/mypage/delete', 'destroy')->name('mypage.destroy');
    // 注文履歴
    Route::get('users/mypage/cart_history', 'cart_history_index')->name('mypage.cart_history');
    // 注文履歴詳細
    Route::get('users/mypage/cart_history/{num}', 'cart_history_show')->name('mypage.cart_history_show');
    // 決済機能
    Route::get('users/mypage/register_card', 'register_card')->name('mypage.register_card');
    Route::post('users/mypage/token', 'token')->name('mypage.token');
});

// レビューが送信されて更新する
Route::post('reviews',[ReviewController::class,'store'])->name('reviews.store');

// お気に入り機能
Route::get('products/{product}/favorite', [ProductController::class, 'favorite'])->name('products.favorite');

// Route::resourceでCRUD用のURLを一度に定義
// メール送信後に認証されていない場合に、->middlewareを使って別の送信画面へとリダイレクトさせる
Route::resource('products', ProductController::class)->middleware(['auth', 'verified']);
// auth=認証、verify=確かめる
Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
