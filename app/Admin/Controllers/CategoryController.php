<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\MajorCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Category';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        // idと紐づいた親カテゴリー名を検索して選択　editable()で編集機能を追加
        $grid->column('major_category_id', __('Major category name'))->editable('select', MajorCategory::all()->pluck('name', 'id'));
        $grid->column('created_at', __('Created at'))->sortable();
        $grid->column('updated_at', __('Updated at'))->sortable();

        // フィルター
        $grid->filter(function($filter) {
            // 部分一致のフィルタを追加する関数 $filter->like('カラム名','画面に表示する文字列')
            $filter->like('name', 'カテゴリー名');
            // multipleSelect()を使用して一つの項目で複数選択
            $filter->in('major_category_id', '親カテゴリー名')->multipleSelect(MajorCategory::all()->pluck('name', 'id'));
            // 範囲指定のフィルタを追加する関数 $filter->between() datetime()を付与することで、カレンダーを表示して指定
            $filter->between('created_at', '登録日')->datetime();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Category::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('major_category.name', __('Major category name'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Category());

        $form->text('name', __('Name'));
        $form->textarea('description', __('Description'));
        // options()で選択肢を表示
        $form->select('major_category_id', __('Major Category Name'))->options(MajorCategory::all()->pluck('name', 'id'));

        return $form;
    }
}
