<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingcartTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('cart.database.table'), function (Blueprint $table) {
            $table->string('identifier');
            $table->string('instance');
            $table->longText('content');
            // 購入履歴の番号を補完するカラムを追加 integer=整数型 nullable=null許容型
            $table->integer('number')->nullable();
            $table->nullableTimestamps();

            $table->primary(['identifier', 'instance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop(config('cart.database.table'));
    }
}
