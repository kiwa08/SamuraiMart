<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\MajorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // カテゴリーの絞り込み機能
        if ($request->category !== null) {
            // whereメソッド＝条件を指定して、フィルタをかける
            // 受け取った絞り込みたいカテゴリーのIDを持つ商品データを取得、ページネーションを表示　sortable()=ソート機能
            $products = Product::where('category_id', $request->category)->sortable()->paginate(15);
            // 当該カテゴリーの商品数を表示
            $total_count = Product::where('category_id', $request->category)->count();
            // カテゴリー名を取得 find()メソッド＝指定したキーの要素だけ取得
            $category = Category::find($request->category);
            $major_category = MajorCategory::find($category->major_category_id);

        // 絞り込みをしないとき
        } else {
            // Productモデルのデータを15件ずつ、ページネーションで表示。取得したデータを変数$productsに代入 sortable()=ソート機能
            $products = Product::sortable()->paginate(15);
            $total_count = "";
            $category = null;
            $major_category = null;
        }

        $categories = Category::all();
        // 親カテゴリーのカラムの値を配列で取得し、配列から、一意 (重複しない) データを返す
        // 削除　$major_category_names = Category::pluck('major_category_name')->unique();

        // 親カテゴリーのテーブルからすべて取得
        $major_categories = MajorCategory::all();

        //変数(compactの中身)を配列にしてビューに渡す　total_count＝商品数
        return view('products.index', compact('products', 'category', 'major_category', 'categories', 'major_categories', 'total_count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 商品登録時にカテゴリのデータを表示
        $categories = Category::all();
        // $categoriesにすべてのカテゴリを保存し、ビューへと渡す
        return view('products.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->save();

        return to_route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        // 商品についての全てのレビューを取得して$reviewsに保存
        $reviews = $product->reviews()->get();

        return view('products.show',compact('product','reviews'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        // 商品の情報を更新する時にカテゴリを選択・保存
        $categories = Category::all();
        // カテゴリの全データが入っている$categories変数をビューに渡す
        return view('products.edit',compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->update();

        return to_route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return to_route('product.index');
    }

    //お気に入り機能 toggleは実行するたびに表示・非表示を繰り返す
     public function favorite(Product $product) {
         Auth::user()->togglefavorite($product);
        return back();
    }
}
