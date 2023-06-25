<?php

use Illuminate\Routing\Router;
use App\Admin\Controllers\CategoryController;
use App\Admin\Controllers\ProductController;
use App\Admin\Controllers\MajorCategoryController;
use App\Admin\Controllers\UserController;
use App\Admin\Controllers\ShoppingCartController;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    // カテゴリーコントローラーのルーティング
    $router->resource('categories', CategoryController::class);
    // 商品ページコントローラーのルーティング
    $router->resource('products', ProductController::class);
    // 親カテゴリのコントローラーのルーティング
    $router->resource('major-categories', MajorCategoryController::class);
    // ユーザー画面のコントローラーのルーティング
    $router->resource('users', UserController::class);
    // 購入履歴のコントローラーのルーティング
    $router->resource('shopping-carts', ShoppingCartController::class)->only('index');
    // CSVデータをインポートして商品登録するルーティング
    $router->post('products/import', [ProductController::class, 'csvImport']);




});
