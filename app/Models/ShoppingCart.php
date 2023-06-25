<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShoppingCart extends Model
{
    use HasFactory;

    protected $table = 'shoppingcart';

    // 購入履歴(CurrentUserOrders)
    // static = インスタンスを生成せずにアクセスすることができる　クラスメソッド
    public static function getCurrentUserOrders($user_id)
     {
        // 指定したユーザーIDに紐づいたショッピングカートを取得
        $shoppingcarts = DB::table('shoppingcart')->where("instance", "{$user_id}")->get();

         $orders = [];

         foreach ($shoppingcarts as $order) {
            // 注文ID、購入日時、金額、ユーザー名、注文番号を取得して$ordersに配列で格納
             $orders[] = [
                 'id' => $order->number,
                 'created_at' => $order->updated_at,
                 'total' => $order->price_total,
                 'user_name' => User::find($order->instance)->name,
                 'code' => $order->code
             ];
         }

        //  データベースに返す
         return $orders;
     }
}
