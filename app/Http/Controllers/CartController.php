<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ユーザーのIDを元にこれまで追加したカートの中身を$cart変数に保存
        $cart = Cart::instance(Auth::user()->id)->content();

        $total = 0;

        // 送料なしの時0円
        $has_carriage_cost = false;
        $carriage_cost = 0;

        // 合計金額を計算して$total変数に保存　qty＝数量
        foreach ($cart as $c) {
            $total += $c->qty * $c->price;

                // カート内の全ての商品から判断し、一つでも送料ありの時だけ$has_carriage_costフラグをtrue
                if ($c->options->carriage) {
                    $has_carriage_cost = true;
                }
        }

        // 送料ありの商品があった時だけ合計金額（$total）に送料800円を追加
        if($has_carriage_cost) {
            $total += env('CARRIAGE');
            $carriage_cost = env('CARRIAGE');
        }



        return view('carts.index', compact('cart', 'total','carriage_cost'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // ユーザーのIDを元にカートのデータを作成し、add()関数を使って送信されたデータを元に商品を追加
        Cart::instance(Auth::user()->id)->add(
            [
                'id' => $request->id,
                'name' => $request->name,
                'qty' => $request->qty,
                'price' => $request->price,
                'weight' => $request->weight,
                'options' => [
                    'image' => $request->image,
                    // formから送信された送料の有無をカートに保存
                    'carriage' => $request->carriage,
                ]
            ]
        );

        // 商品の個別ページへとリダイレクト
        return to_route('products.show', $request->get('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //購入の処理　購入後にカートを空にする
    public function destroy(Request $request)
    {
        $user_shoppingcarts = DB::table('shoppingcart')->get();
        //  where()でショッピングカートテーブルからユーザーIDでフィルターをかける
        $number = DB::table('shoppingcart')->where('instance', Auth::user()->id)->count();

        // 現在までのユーザーが注文したカートの数を取得
        $count = $user_shoppingcarts->count();

        // 新しくデータベースに登録するカートのデータ用にカートのIDを一つ増やす
        $count += 1;
        // ？？？を一つ増やす
        $number += 1;

        // content()？
        $cart = Cart::instance(Auth::user()->id)->content();

        $price_total = 0;
        $qty_total = 0;
        $has_carriage_cost = false;

        foreach ($cart as $c) {
            // 個数と価格で計算してトータルに追加
            $price_total += $c->qty * $c->price;
            // カートの商品の数を全個数に追加
            $qty_total += $c->qty;
                // 送料の有無の切り替え
                if ($c->options->carriage) {
                 $has_carriage_cost = true;
                }
        }

        if($has_carriage_cost) {
            // 送料がある時、トータルに800円追加
            $price_total += env('CARRIAGE');
        }

        // ユーザーのIDを使ってカート内の商品情報などをデータベースへと保存
        Cart::instance(Auth::user()->id)->store($count);

        //
        DB::table('shoppingcart')->where('instance', Auth::user()->id)
            ->where('number', null)
            ->update(
                [
                    // 購入コードを取得
                    'code' => substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10),
                    'number' => $number,
                    // 支払金額合計
                    'price_total' => $price_total,
                    // 購入個数合計
                    'qty' => $qty_total,
                    // 購入済みフラグをtrue
                    'buy_flag' => true,
                    // 更新日を更新
                    'updated_at' => date("Y/m/d H:i:s")
                ]
            );

        // 決済機能
        $pay_jp_secret = env('PAYJP_SECRET_KEY');
        \Payjp\Payjp::setApiKey($pay_jp_secret);

        $user = Auth::user();

        $res = \Payjp\Charge::create(
            [
                "customer" => $user->token,
                // amount = 量
                "amount" => $price_total,
                // currency = 通貨
                "currency" => 'jpy'
            ]
        );

        // 購入済みのカート内の商品情報などを削除
        Cart::instance(Auth::user()->id)->destroy();

        // カート画面に戻る
        return to_route('carts.index');
    }
}
