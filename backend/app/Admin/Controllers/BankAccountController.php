<?php

namespace App\Admin\Controllers;

use App\Models\BankAccount;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BankAccountController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '決済情報';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BankAccount());

        $grid->column('account_name', __('admin.bank_account.account_name'));
        $grid->column('branch_id', __('admin.bank_account.branch_id'));
        $grid->column('branch_name', __('admin.bank_account.branch_name'));
        $grid->column('bank_name', __('admin.bank_account.bank_name'));
        $grid->column('account_number', __('admin.bank_account.account_number'));
        $grid->column('note', __('admin.bank_account.note'));
        $grid->export(function ($export) {
            $export->filename(__('admin.content.bank_account'));
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
        $show = new Show(BankAccount::findOrFail($id));

        $show->field('account_name', __('admin.bank_account.account_name'));
        $show->field('branch_id', __('admin.bank_account.branch_id'));
        $show->field('branch_name', __('admin.bank_account.branch_name'));
        $show->field('bank_name', __('admin.bank_account.bank_name'));
        $show->field('account_number', __('admin.bank_account.account_number'));
        $show->field('note', __('admin.bank_account.note'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BankAccount());

        $form->text('account_name', __('admin.bank_account.account_name'))->rules('required|max:255');
        $form->text('branch_id', __('admin.bank_account.branch_id'))->rules('required|max:255');
        $form->text('branch_name', __('admin.bank_account.branch_name'))->rules('required|max:255');
        $form->text('bank_name', __('admin.bank_account.bank_name'))->rules('required|max:255');
        $form->text('account_number', __('admin.bank_account.account_number'))->rules('required|max:255');
        $form->text('note', __('admin.bank_account.note'))->rules('nullable|string|max:255');

        return $form;
    }
}
