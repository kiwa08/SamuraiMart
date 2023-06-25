<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\Category;
// 管理画面のルート
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
// csvデータを読み込むためのルート
use App\Admin\Extensions\Tools\CsvImport;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Http\Request;

class ProductController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('price', __('Price'))->sortable();
        $grid->column('category.name', __('Category Name'));
        $grid->column('image', __('Image'))->image();
        // おすすめ商品カラム
        $grid->column('recommend_flag', __('Recommend Flag'));
        // 送料あり・なしカラム
        $grid->column('carriage_flag', __('Carriage Flag'));
        $grid->column('created_at', __('Created at'))->sortable();
        $grid->column('updated_at', __('Updated at'))->sortable();

        // フィルター
        $grid->filter(function($filter) {
            $filter->like('name', '商品名');
            $filter->like('description', '商品説明');
            $filter->between('price', '金額');
            // in＝選択肢の中から　ultipleSelect＝複数選択　pluck＝配列にして返す
            $filter->in('category_id', 'カテゴリー')->multipleSelect(Category::all()->pluck('name', 'id'));
            $filter->equal('recommend_flag', 'おすすめフラグ')->select(['0' => 'false', '1' => 'true']);
            $filter->equal('carriage_flag', '送料フラグ')->select(['0' => 'false', '1' => 'true']);
        });

        // CSVデータのインポートボタン
        $grid->tools(function ($tools) {
            $tools->append(new CsvImport());
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    // 詳細表示画面
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('price', __('Price'));
        $show->field('category.name', __('Category Name'));
        $show->field('image', __('Image'))->image();
        $show->field('recommend_flag', __('Recommend Flag'));
        $show->field('carriage_flag', __('Carriage Flag'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    // 編集フォーム画面
    protected function form()
    {
        $form = new Form(new Product());

        $form->text('name', __('Name'));
        $form->textarea('description', __('Description'));
        $form->number('price', __('Price'));
        $form->select('category_id', __('Category Name'))->options(Category::all()->pluck('name', 'id'));
        $form->image('image', __('Image'));
        $form->switch('recommend_flag', __('Recommend Flag'));
        $form->switch('carriage_flag', __('Carriage Flag'));

        return $form;
    }

    // CSVデータをインポート　CSVを解析して、商品データを登録
    public function csvImport(Request $request)
     {
         $file = $request->file('file');
        //  $lexer = 字句解析(Lexical Analyzer) 文字列 (ソースコード) → トークン列 に変換
         $lexer_config = new LexerConfig();
         $lexer = new Lexer($lexer_config);

        //  $interpreter = ソースコードをその場で解釈実行する
         $interpreter = new Interpreter();
        //  unstrict()を呼び出すことで厳密な列数のチェックを無効化
         $interpreter->unstrict();

        //  行数を取得
         $rows = array();
        //  addObserver()メソッドで観察者？閲覧者？を登録
         $interpreter->addObserver(function (array $row) use (&$rows) {
             $rows[] = $row;
         });

        //  $lexerで変換したデータをparse(解析)
         $lexer->parse($file, $interpreter);
        //  すべての行から値を取得
         foreach ($rows as $key => $value) {

            // 行数が7の時、Productテーブルでそれぞれの値を代入して商品を追加
             if (count($value) == 7) {
                 Product::create([
                     'name' => $value[0],
                     'description' => $value[1],
                     'price' => $value[2],
                     'category_id' => $value[3],
                     'image' => $value[4],
                     'recommend_flag' => $value[5],
                     'carriage_flag' => $value[6],
                 ]);
             }
         }

        //  json形式でデータを返す　json([データ],[ステータス],[ヘッダー],[オプション])
        //  JSON_UNESCAPED_UNICODEで文字化けを解消
        //  HTTPステータスコード番号200→成功
         return response()->json(
             ['data' => '成功'],
             200,
             [],
             JSON_UNESCAPED_UNICODE
         );
     }
}
