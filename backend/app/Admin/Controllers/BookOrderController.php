<?php

namespace App\Admin\Controllers;

use App\Enums\BookOrderStatus;
use App\Enums\BookTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\ShippingStatusEnum;
use App\Models\BookOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Storage;

class BookOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '注文一覧';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BookOrder());

        $grid->model()->orderByDesc('created_at');
        $grid->column('code', __('admin.book_oder.code'))->expand(function ($model) {
            $bookOrderItems  = $model->bookOrderItems()
                ->with(['book' => function ($query) {
                    $query->withTrashed();
                }])
                ->get();
            $data = $bookOrderItems->map(function ($item) {
                $thumbnailUrl = Storage::disk('s3')->url($item->book->thumbnail);
                $bookTypeLabel = function ($item) {
                    $bookType = $item->book->book_type;

                    return match ($bookType) {
                        BookTypeEnum::BOOK_TYPE => "<span class='label label-warning'>". __('admin.book.BOOK') ."</span>",
                        BookTypeEnum::LBOOK_TYPE => "<span class='label label-primary'>". __('admin.book.LBOOK') ."</span>",
                        BookTypeEnum::EBOOK_TYPE => "<span class='label label-info'>". __('admin.book.EBOOK') ."</span>",
                        BookTypeEnum::GBOOK_TYPE => "<span class='label' style='background-color: #16a085'>" . __('admin.book.GBOOK') . "</span>",
                        default => $bookType,
                    };
                };

                return [
                    'タイプ' => $bookTypeLabel($item),
                    'サムネイル' => '<img src="' . $thumbnailUrl . '" style="width: 50px; height: 70px;">',
                    'タイトル' => $item->book->name,
                    'ISBS' => $item->book->isbn,
                    '数量' => $item->quantity,
                    '価格' => $item->price,
                ];
            })->toArray();
            $table = new Table(['タイプ', 'カバー', 'タイトル', 'ISBN', '数量', '価格'], $data);

            return $table;
        });
        $grid->column('receiver_email', __('admin.book_oder.user'));
        $grid->column('total_price', __('admin.book_oder.total_price'));
        $grid->column('receiver_name', __('admin.book_oder.receiver_name'));
        $grid->column('receiver_phone', __('admin.book_oder.receiver_phone'));
        $grid->column('receiver_address', __('admin.book_oder.receiver_address'));
        $grid->column('payment_method', __('admin.book_oder.payment_method'))->display(function ($payment) {
            switch ($payment) {
                case PaymentMethodEnum::CREDIT_CARD:
                    return __('admin.book_oder.CREDIT_CARD');
                case PaymentMethodEnum::DIRECT_PAYMENT:
                    return __('admin.book_oder.DIRECT_PAYMENT');
                case PaymentMethodEnum::BANK_TRANSFER:
                    return __('admin.book_oder.BANK_TRANSFER');
                case PaymentMethodEnum::POSTAL_DELIVERY:
                    return __('admin.book_oder.POSTAL_DELIVERY');
                default:
                    return '';
            }
        });
        $grid->status(__('admin.book_oder.status'))->display(function ($status) {
            switch ($status) {
                case BookOrderStatus::CREATED:
                    return "<span class='label label-default'>". __('admin.book_oder.CREATED') ."</span>";
                case BookOrderStatus::IN_PAYMENT:
                    return "<span class='label label-info'>". __('admin.book_oder.IN_PAYMENT') ."</span>";
                case BookOrderStatus::PAYMENT_ERROR:
                    return "<span class='label label-warning'>". __('admin.book_oder.PAYMENT_ERROR') ."</span>";
                case BookOrderStatus::SUCCESS:
                    return "<span class='label label-success'>". __('admin.book_oder.SUCCESS') ."</span>";
                case BookOrderStatus::PAID:
                    return "<span class='label label-success'>". __('admin.book_oder.PAID') ."</span>";
                case BookOrderStatus::IN_PROGRESS_DELIVERY:
                    return "<span class='label label-primary'>". __('admin.book_oder.IN_PROGRESS_DELIVERY') ."</span>";
                case BookOrderStatus::CANCEL:
                    return "<span class='label label-danger'>". __('admin.book_oder.CANCEL') ."</span>";
                default:
                    return '';
            }
        });
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1/2, function ($filter) {
                $filter->like('code', __('admin.book_oder.code'));
                $filter->equal('payment_method', __('admin.book_oder.payment_method'))->select([
                    PaymentMethodEnum::CREDIT_CARD => __('admin.book_oder.CREDIT_CARD'),
                    PaymentMethodEnum::BANK_TRANSFER => __('admin.book_oder.BANK_TRANSFER'),
                    PaymentMethodEnum::POSTAL_DELIVERY => __('admin.book_oder.POSTAL_DELIVERY'),
                    PaymentMethodEnum::DIRECT_PAYMENT => __('admin.book_oder.DIRECT_PAYMENT'),
                ]);
                $filter->equal('status', __('admin.book_oder.status'))->select([
                    BookOrderStatus::CREATED => __('admin.book_oder.CREATED'),
                    BookOrderStatus::IN_PAYMENT => __('admin.book_oder.IN_PAYMENT'),
                    BookOrderStatus::PAID => __('admin.book_oder.PAID'),
                    BookOrderStatus::IN_PROGRESS_DELIVERY => __('admin.book_oder.IN_PROGRESS_DELIVERY'),
                    BookOrderStatus::SUCCESS => __('admin.book_oder.SUCCESS'),
                    BookOrderStatus::CANCEL => __('admin.book_oder.CANCEL'),
                ]);
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('user.email', __('admin.book_oder.user'));
                $filter->like('receiver_name', __('admin.book_oder.receiver_name'));
                $filter->like('receiver_phone', __('admin.book_oder.receiver_phone'));
                $filter->like('receiver_address', __('admin.book_oder.receiver_address'));
            });
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });
        $grid->disableCreateButton();

        $grid->export(function ($export) {
            $export->filename(__('admin.content.book_order'));
            $export->originalValue(['code', 'shipping_status']);
            $export->column('status', function ($value, $original) {
                return __('admin.book_oder.' . $original);
            });
            $export->column('payment_method', function ($value, $original) {
                return __('admin.book_oder.' . $original);
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     */
    protected function detail($id)
    {
        return redirect()->route('admin.book-orders.edit', ['book_order' => $id]);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BookOrder());

        $form->display('code', __('admin.book_oder.code'));
        $form->display('receiver_email', __('admin.book_oder.user'));
        $form->display('total_price', __('admin.book_oder.total_price'));
        $form->display('receiver_name', __('admin.book_oder.receiver_name'));
        $form->display('receiver_phone', __('admin.book_oder.receiver_phone'));
        $form->display('receiver_address', __('admin.book_oder.receiver_address'));
        $form->display('memo', __('admin.book_oder.memo'))->with(function ($value) {
            if (is_null($value)) {
                return '';
            }
            $posDate = strpos($value, __('admin.book_oder.delivery_date'));
            $posTime = strpos($value, __('admin.book_oder.delivery_time'));

            $memo = substr($value, 0, $posDate);
            $deliveryDate = substr($value, $posDate, $posTime - $posDate);
            $deliveryTime = substr($value, $posTime);

            $note = "$memo\n$deliveryDate\n$deliveryTime";

            return "<pre>{$note}</pre>";
        });

        $form->editing(function (Form $form) {
            $orderItems = $form->model()->bookOrderItems()->get();
            $paymentMethod = $form->model()->payment_method;

            if ($orderItems->isNotEmpty()) {
                $typeBook = $orderItems->every(function ($item) {
                    return $item->book->book_type === BookTypeEnum::BOOK_TYPE;
                });
                if ($typeBook) {
                    // Case: order item check is a paper BOOK and Payment : DIRECT_PAYMENT or BANK_TRANSFER
                    if ($paymentMethod === PaymentMethodEnum::DIRECT_PAYMENT || $paymentMethod === PaymentMethodEnum::BANK_TRANSFER) {
                        $form->radio('status', __('admin.book_oder.status'))->options([
                            BookOrderStatus::IN_PAYMENT => __('admin.book_oder.IN_PAYMENT'),
                            BookOrderStatus::PAID => __('admin.book_oder.PAID'),
                            BookOrderStatus::IN_PROGRESS_DELIVERY => __('admin.book_oder.IN_PROGRESS_DELIVERY'),
                            BookOrderStatus::SUCCESS => __('admin.book_oder.SUCCESS'),
                            BookOrderStatus::CANCEL => __('admin.book_oder.CANCEL'),
                        ]);
                    }
                    // Case: order item check is a paper BOOK and Payment : CREDIT_CARD or POSTAL_DELIVERY
                    else {
                        $form->radio('status', __('admin.book_oder.status'))->options([
                            BookOrderStatus::PAID => __('admin.book_oder.PAID'),
                            BookOrderStatus::IN_PROGRESS_DELIVERY => __('admin.book_oder.IN_PROGRESS_DELIVERY'),
                            BookOrderStatus::SUCCESS => __('admin.book_oder.SUCCESS'),
                            BookOrderStatus::CANCEL => __('admin.book_oder.CANCEL'),
                        ]);
                    }
                } elseif ($paymentMethod !== PaymentMethodEnum::CREDIT_CARD && !$typeBook) {
                    // Case: order item check is a digital book and payment method is CREDIT_CARD
                    $form->radio('status', __('admin.book_oder.status'))->options([
                        BookOrderStatus::IN_PAYMENT => __('admin.book_oder.IN_PAYMENT'),
                        BookOrderStatus::SUCCESS => __('admin.book_oder.SUCCESS'),
                        BookOrderStatus::CANCEL => __('admin.book_oder.CANCEL'),
                    ]);
                } else {
                    $form->hidden('status', __('admin.book_oder.status'));
                }
            }
        });

        $form->saving(function (Form $form) {
            $form->model()->status = $form->input('status');
        });
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->confirm('更新してもよろしいですか?', 'edit');

        return $form;
    }
}
