<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // up()メソッド＝データベースに新しいテーブルやカラムなどを生成するための処理
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // 郵便番号のカラム
            $table->string('postal_code')->default('');
            // 住所のカラム
            $table->text('address');
            // 電話番号のカラム
            $table->string('phone')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
