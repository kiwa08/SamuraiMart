<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
// use Laravel\Ui\Presets\React;
use App\Models\ShoppingCart;
use Illuminate\Pagination\LengthAwarePaginator;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function mypage()
    {
        // Auth::user()を使い、ユーザー情報を取得して$userに保存
        $user = Auth::user();

        return view('users.mypage', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user = Auth::user();

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

    // 名前、メールアドレス、郵便番号、住所、電話番号の変更機能
    public function update(Request $request, User $user)
    {
        $user = Auth::user();

        $user->name = $request->input('name') ? $request->input('name') : $user->name;
        $user->email = $request->input('email') ? $request->input('email') : $user->email;
        $user->postal_code = $request->input('postal_code') ? $request->input('postal_code') : $user->postal_code;
        $user->address = $request->input('address') ? $request->input('address') : $user->address;
        $user->phone = $request->input('phone') ? $request->input('phone') : $user->phone;
        $user->update();

        return to_route('mypage');
    }

    // パスワードの変更・更新機能
    public function update_password(Request $request)
    {
        $validatedData = $request->validate([
            // 必須項目　かつ　確認入力
           'password' => 'required|confirmed',
        ]);

        $user = Auth::user();

        // 送信されたリクエスト内のpasswordとpassword_confirmation(確認入力)が同一のものであるかを確認し、同じである場合のみパスワードを暗号化しデータベースへと保存
        if ($request->input('password') == $request->input('password_confirmation')) {
            // bcrypt＝暗号化関数
            $user->password = bcrypt($request->input('password'));
            $user->update();
        } else {
            return to_route('mypage.edit_password');
        }

        return to_route('mypage');
    }

    // パスワード変更画面を表示する
    public function edit_password()
    {
        return view('users.edit_password');
    }

    // お気に入りした商品を取得し、ユーザ情報のビューに渡す処理
    public function favorite()
    {
        $user = Auth::user();

        $favorites = $user->favorites(Product::class)->get();

        return view('users.favorite', compact('favorites'));
    }

    // 退会用画面
    public function destroy(Request $request)
     {
         $user = Auth::user();

         if ($user->deleted_flag) {
             $user->deleted_flag = false;
         } else {
             $user->deleted_flag = true;
         }
         $user->update();

         Auth::logout();
         return redirect('/');
     }

    //  購入履歴を呼び出す
     public function cart_history_index(Request $request)
     {
         $page = $request->page != null ? $request->page : 1;
         $user_id = Auth::user()->id;
         //  ModelのShoppingCart.phpから関数を呼び出して、取得した配列を$billingsに代入
         $billings = ShoppingCart::getCurrentUserOrders($user_id);
         $total = count($billings);
        //  渡された配列を元にして、ペジネーションインスンタンスを作成
        //  array_slice() = 配列の一部を切り取る
        //  array('path' => $request->url()???
         $billings = new LengthAwarePaginator(array_slice($billings, ($page - 1) * 15, 15), $total, 15, $page, array('path' => $request->url()));

         return view('users.cart_history_index', compact('billings', 'total'));
     }

    //  注文履歴の詳細ページ
     public function cart_history_show(Request $request)
     {
         $num = $request->num;
         $user_id = Auth::user()->id;
        //  ユーザーIDと注文番号を指定して、shoppingcartの中の1つ目のデータを取得　$cart_infoに代入
         $cart_info = DB::table('shoppingcart')->where('instance', $user_id)->where('number', $num)->get()->first();
        //  restore()保存した状態を復元する　　$cart_infoの購入IDを復元？
         Cart::instance($user_id)->restore($cart_info->identifier);
         $cart_contents = Cart::content();
        //  cart_infoの購入IDを更新？
         Cart::instance($user_id)->store($cart_info->identifier);
         Cart::destroy();

        //  number(ユーザーごとの購入数) が無いものをアップデート？
         DB::table('shoppingcart')->where('instance', $user_id)
             ->where('number', null)
             ->update(
                 [
                     'code' => $cart_info->code,
                     'number' => $num,
                     'price_total' => $cart_info->price_total,
                     'qty' => $cart_info->qty,
                     'buy_flag' => $cart_info->buy_flag,
                     'updated_at' => $cart_info->updated_at
                 ]
             );

         return view('users.cart_history_show', compact('cart_contents', 'cart_info'));
     }

    //　カード情報入力
    public function register_card(Request $request)
    {
         $user = Auth::user();

        //  env.に設定したPay.jpの秘密鍵をセット
         $pay_jp_secret = env('PAYJP_SECRET_KEY');
         \Payjp\Payjp::setApiKey($pay_jp_secret);

         $card = [];
         $count = 0;

        //  入力があるとき
         if ($user->token != "") {
            // result=結果　retrieve = (データを)取り込む
            // (array("limit"=>1))対象を1件にする　limit＝取得数を制限
             $result = \Payjp\Customer::retrieve($user->token)->cards->all(array("limit"=>1))->data[0];
            //  登録してあるカードの数？
             $count = \Payjp\Customer::retrieve($user->token)->cards->all()->count;

             $card = [
                // カードの会社
                 'brand' => $result["brand"],
                //  利用期限
                 'exp_month' => $result["exp_month"],
                 'exp_year' => $result["exp_year"],
                //  下4桁
                 'last4' => $result["last4"]
             ];
         }

         return view('users.register_card', compact('card', 'count'));

    }

    // 決済トークン
    public function token(Request $request)
    {
         $pay_jp_secret = env('PAYJP_SECRET_KEY');
         \Payjp\Payjp::setApiKey($pay_jp_secret);

         $user = Auth::user();
         $customer = $user->token;

        //  決済トークンがすでにある時
         if ($customer != "") {
             $cu = \Payjp\Customer::retrieve($customer);
             //  以前の情報を消去
             $delete_card = $cu->cards->retrieve($cu->cards->data[0]["id"]);
             $delete_card->delete();
             //  カード情報作成
             $cu->cards->create(array(
                 "card" => request('payjp-token')
             ));
         } else {
            //  カード情報作成
             $cu = \Payjp\Customer::create(array(
                 "card" => request('payjp-token')
             ));
            //  決済idを作成してユーザー情報のアップデート
             $user->token = $cu->id;
             $user->update();
         }

         return to_route('mypage');
    }
}
