<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // レビューの内容（content）必須入力チェック バリデーション=検証
        $request->validate([
            'content' => 'required'
        ]);

        $review = new Review();
        // レビューの内容（content）とレビュー対象商品のID（product_id）を、変数$requestから取得
        $review->content = $request->input('content');
        $review->product_id = $request->input('product_id');
        // レビューを投稿したユーザーIDを取得
        $review->user_id = Auth::user()->id;
        // フォームから送信された評価をデータベースに保存
        $review->score = $request->input('score');
        $review->save();
        // 商品ページに戻る
        return back();
    }

}
