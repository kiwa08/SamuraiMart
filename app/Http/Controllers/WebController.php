<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\MajorCategory;
use App\Models\Product;

class WebController extends Controller
{
    public function index()
    {
        // 親カテゴリーをソートしてすべて取得　sortByメソッド＝指定したキーでコレクションをソート
        // 削除　$categories = Category::all()->sortBy('major_category_name');

        $categories = Category::all();

        // pluck()メソッド=特定の(親カテゴリーの)カラムを配列にして返す
        // unique()メソッド=配列の要素の中で重複している要素を削除して、削除後の配列として返す
        // 削除　$major_category_names = Category::pluck('major_category_name')->unique();

        // 親カテゴリーテーブルからすべて取得
        $major_categories = MajorCategory::all();

        // 商品の登録日時（created_at）でソートして、新しい順に4つ取得してビューに渡しています。
        // orderByは、取得したデータのソート　take(4)で4件の新着情報
        $recently_products = Product::orderBy('created_at', 'desc')->take(4)->get();

        // おすすめ商品を3件まで表示　recommend_flagがtrueになっているもののどれかを取得
        $recommend_products = Product::where('recommend_flag', true)->take(3)->get();

        return view('web.index', compact('major_categories', 'categories','recently_products', 'recommend_products'));
    }
}
