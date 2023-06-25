<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// お気に入り機能
use Overtrue\LaravelFavorite\Traits\Favoriteable;
// ソート機能
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory, Favoriteable, Sortable;

    // CSVデータからの商品登録
    // $fillable = Laravel側から触ってもよいカラム”を指定(不正な変更を防ぐ)
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
        'recommend_flag',
        'carriage_flag',
    ];

    // belongsTo=どれか一つのモデルに従属 (商品１つに１つのカテゴリー)
    public function category() {
        return $this->belongsTo('App\Models\Category');
    }
    // hasMany=一対多の関係性をモデルに追加　(商品１つに複数のレビュー)
    public function reviews() {
        return $this->hasMany('App\Models\Review');
    }
}
