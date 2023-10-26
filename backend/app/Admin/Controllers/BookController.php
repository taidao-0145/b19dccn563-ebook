<?php

namespace App\Admin\Controllers;

use App\Models\Book;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BookController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Book';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Book());

        $grid->column('id', __('Id'));
        $grid->column('isbn', __('Isbn'));
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('thumbnail', __('Thumbnail'));
        $grid->column('author_name', __('Author name'));
        $grid->column('page_number', __('Page number'));
        $grid->column('relase_date', __('Relase date'));
        $grid->column('inventory_number', __('Inventory number'));
        $grid->column('price', __('Price'));
        $grid->column('sample_path', __('Sample path'));
        $grid->column('book_type', __('Book type'));
//        $grid->column('deleted_at', __('Deleted at'));
//        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));
        $grid->filter(function ($filter) {
            $filter->like('isbn','ISBN');
            $filter->equal('book_type')->select(['LBOOK' => 'LBOOK', 'EBOOK' => 'EBOOK', 'BOOK' => 'BOOK']);
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
        $show = new Show(Book::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('isbn', __('Isbn'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('author_name', __('Author name'));
        $show->field('page_number', __('Page number'));
        $show->field('relase_date', __('Relase date'));
        $show->field('inventory_number', __('Inventory number'));
        $show->field('price', __('Price'));
        $show->field('sample_path', __('Sample path'));
        $show->field('book_type', __('Book type'));
//        $show->field('deleted_at', __('Deleted at'));
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
        $form = new Form(new Book());

        $form->text('isbn', __('Isbn'))->rules('required');
        $form->text('name', __('Name'))->rules('required');
        $form->text('description', __('Description'))->rules('required');
        $form->text('thumbnail', __('Thumbnail'))->rules('required');
        $form->text('author_name', __('Author name'))->rules('required');
        $form->number('page_number', __('Page number'))->rules('required');
        $form->datetime('relase_date', __('Relase date'))->default(date('Y-m-d H:i:s'))->rules('required');
        $form->number('inventory_number', __('Inventory number'))->rules('required');
        $form->decimal('price', __('Price'))->rules('required');
//        $form->text('sample_path', __('Sample path'))->rules('required');
        $form->file('sample_path',__('Sample path'))->rules('mimes:pdf');
        $form->select('book_type',__('Book type'))->options(['LBOOK' => 'LBOOK', 'EBOOK' => 'EBOOK', 'BOOK' => 'BOOK']);
        return $form;
    }
}
