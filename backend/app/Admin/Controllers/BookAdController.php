<?php

namespace App\Admin\Controllers;

use App\Models\BookAd;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Storage;

class BookAdController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '広告';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BookAd());

        $grid->column('id', __('admin.book_ad.id'));
        $grid->column('path', __('admin.book_ad.book_ad'))->setAttributes(['width' => ' 800px'])->display(function ($value) {
            $s3Url = Storage::disk('s3')->url($value);

            return "<img src=\"$s3Url\" width=\"560\" height=\"200\">";
        });
        $grid->column('redirect_path', __('admin.book_ad.url'));
        $grid->export(function ($export) {
            $export->filename(__('admin.content.book_ad'));
            $export->column('path', function ($value, $original) {
                $s3Url = Storage::disk('s3')->url($original);

                return $s3Url;
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
        $show = new Show(BookAd::findOrFail($id));

        $show->field('id', __('admin.book_ad.id'));
        $show->path(__('admin.book_ad.book_ad'))->unescape()->as(function ($book_ad) {
            $s3Url = Storage::disk('s3')->url($book_ad);

            return "<img src=\"$s3Url\" width=\"840\" height=\"300\">";
        });
        $show->field('redirect_path', __('admin.book_ad.url'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BookAd());

        $form->image('path', __('admin.book_ad.book_ad'))
            ->move('public/book_ads')
            ->rules('required|mimes:png,jpg');
        $form->url('redirect_path', __('admin.book_ad.url'))->rules('max:255');

        return $form;
    }
}
