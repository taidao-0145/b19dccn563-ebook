<?php

namespace App\Admin\Controllers;

use App\Enums\BookTypeEnum;
use App\Models\Category;
use Encore\Admin\Admin;
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
    protected $title = 'カテゴリ一覧';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->model()->orderByDesc('created_at');
        $grid->column('name', __('admin.category.name'));
        $grid->column('description', __('admin.category.description'))->setAttributes(['width' => ' 700px']);
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', __('admin.category.name'));
        });
        $grid->column('parent_id', __('admin.category.parent'))->display(function ($parentId) {
            $parentCategory = Category::find($parentId);

            return $parentCategory ? $parentCategory->name : '';
        });
        $grid->column('book_type', __('admin.category.book_type'))->display(function ($bookTypes) {
            if (is_null($bookTypes)) {
                return '';
            }
            $typeMappings = [
                BookTypeEnum::BOOK_TYPE => __('admin.book.BOOK'),
                BookTypeEnum::LBOOK_TYPE => __('admin.book.LBOOK'),
                BookTypeEnum::EBOOK_TYPE => __('admin.book.EBOOK'),
                BookTypeEnum::GBOOK_TYPE => __('admin.book.GBOOK'),
            ];
            $labels = array_map(function ($type) use ($typeMappings) {
                return "<span class='label label-success'>" . $typeMappings[$type] . "</span>";
            }, $bookTypes);

            return join('&nbsp;', $labels);
        });

        $grid->export(function ($export) {
            $export->filename(__('admin.content.category'));
            $export->column('book_type', function ($value, $original) {
                if (is_array($original)) {
                    $replaced = array_map(function ($bookType) {
                        return __('admin.book.' . $bookType);
                    }, $original);

                    return implode(', ', $replaced);
                }

                return $original;
            });
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

        $show->field('name', __('admin.category.name'));
        $show->field('description', __('admin.category.description'));
        $show->field('parent_id', __('admin.category.parent'))->as(function ($parentId) {
            $parentCategory = Category::find($parentId);

            return $parentCategory ? $parentCategory->name : '';
        });

        return $show;
    }

    public function destroy($id)
    {
        try {
            Category::destroy($id);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Category());

        $form->text('name', __('admin.category.name'))->rules('required|max:255');
        $form->textarea('description', __('admin.category.description'))->rules('required|max:500');
        $form->multipleSelect('book_type', __('admin.category.book_type'))->options([
            BookTypeEnum::BOOK_TYPE => __('admin.book.BOOK'),
            BookTypeEnum::LBOOK_TYPE => __('admin.book.LBOOK'),
            BookTypeEnum::EBOOK_TYPE => __('admin.book.EBOOK'),
            BookTypeEnum::GBOOK_TYPE => __('admin.book.GBOOK'),
        ])->rules('required');
        if ($form->isCreating()) {
            $form->select('parent_id', __('admin.category.parent'))->options(Category::pluck('name', 'id')->toArray());
        }
        if ($form->isEditing()) {
            $form->editing(function (Form $form) {
                $categories = Category::where('id', '!=', $form->model()->id)
                    ->pluck('name', 'id')
                    ->toArray();
                $form->select('parent_id', __('admin.category.parent'))->options($categories);
            });
        }

        $form->saving(function (Form $form) {
            $form->model()->parent_id = $form->input('parent_id');
            $bookTypes = $form->input('book_type');
            $bookTypes = array_filter($bookTypes, function ($value) {
                return !is_null($value);
            });
            $form->model()->book_type = $bookTypes;
        });

        return $form;
    }
}

