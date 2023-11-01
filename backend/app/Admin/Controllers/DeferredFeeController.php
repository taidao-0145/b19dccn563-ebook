<?php

namespace App\Admin\Controllers;

use App\Models\DeferredFee;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\MessageBag;

class DeferredFeeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '代金引換料金';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DeferredFee());

        $grid->model()->orderBy('from_price');
        $grid->column('from_price', __('admin.deferred_fee.from_price'));
        $grid->column('to_price', __('admin.deferred_fee.to_price'));
        $grid->column('fee', __('admin.deferred_fee.fee'));
        $grid->export(function ($export) {
            $export->filename(__('admin.content.deferred_fee'));
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
        $show = new Show(DeferredFee::findOrFail($id));

        $show->field('from_price', __('admin.deferred_fee.from_price'));
        $show->field('to_price', __('admin.deferred_fee.to_price'));
        $show->field('fee', __('admin.deferred_fee.fee'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DeferredFee());

        $form->decimal('from_price', __('admin.deferred_fee.from_price'))
            ->rules('min:0|max:999999');
        $form->decimal('to_price', __('admin.deferred_fee.to_price'))
            ->rules('required|numeric|min:0|max:999999');
        $form->decimal('fee', __('admin.deferred_fee.fee'))
            ->rules('required|numeric|min:0|max:999999');
        $form->saving(function (Form $form) {
            function error($title, $message): RedirectResponse
            {
                $error = new MessageBag([
                    'title' => $title,
                    'message' => $message,
                ]);

                return back()->with(compact('error'))->withInput();
            }

            if (is_null($form->from_price)) {
                $form->from_price = 0;
            }
            $fromPrice = $form->from_price;
            $toPrice = $form->to_price;

            if ($fromPrice < 0) {
                return error(__('admin.message.error'), __('admin.message.from_price'));
            }
            if ($fromPrice >= $toPrice) {
                return error(__('admin.message.error'), __('admin.message.greater'));
            }
            $existingFee = DeferredFee::where(function ($query) use ($fromPrice, $toPrice, $form) {
                $query->where('from_price', '<=', $toPrice)
                    ->where('to_price', '>=', $fromPrice);
                if ($form->model()->exists) {
                    $query->where('id', '!=', $form->model()->id);
                }
            })->first();
            if ($existingFee) {
                return error(__('admin.message.error'), __('admin.message.exist'));
            }
        });

        return $form;
    }
}
