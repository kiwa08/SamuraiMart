<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;
use Overtrue\LaravelFavorite\Traits\Favoriter;

// Authenticatable=認証可能　implements=実装する　Verify=確認　MustVerifyEmail=インターフェース名
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Favoriter;

    // アカウントを作成する際に、メールを送信する notify=通知
    public function sendEmailVerificationNotification()
     {
         $this->notify(new CustomVerifyEmail());
     }

    // パスワード再発行メール
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     *
     */
    // アカウント作成時に保存　protected=保護　$fillable=書き込める
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // hasMany=一対多の関係性をモデルに追加 (１人のユーザーが複数のレビューを投稿)
    public function reviews() {
        return $this->hasMany('App\Models\Review');
    }
}
