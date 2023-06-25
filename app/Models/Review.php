<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // belongsTo=どれか一つのモデルに従属 (レビューを商品に紐づけ)
    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
    // レビューをユーザーに紐づけ
    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
