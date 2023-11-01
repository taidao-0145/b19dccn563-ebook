<?php

namespace App\Admin\Controllers;

use App\Models\BookResource;
use App\Services\Admin\AdminBookChapterService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BookResourceController extends AdminController
{
    public function __construct(protected AdminBookChapterService $bookChapterService)
    {
        //
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '本の章';

    /**
     * Make a grid builder.
     */
    protected function grid()
    {
        //
    }

    /**
     * Make a show builder.
     */
    protected function detail($id)
    {
        return redirect()->route('admin.book-resources.edit', ['book_resource' => $id]);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $form = new Form(new BookResource());
        $form->editing(function (Form $form) {
            $bookResourceUrl = Storage::disk('s3')->temporaryUrl(
                $form->model()->path,
                now()->addMinutes(60)
            );
            $bookChapters = $form->model()->bookChapters;

            $form->html(view('admin.bookChapter.update', compact('bookResourceUrl', 'bookChapters')));
        });
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
            $tools->disableList();
        });
        $form->footer(function ($footer) {
            $footer->disableReset();
        });

        $form->disableReset();

        return $form;
    }

    /**
     * Update Book Chapters
     * @param $id
     *
     * @return RedirectResponse
     */
    public function update($id): RedirectResponse
    {
        $request = request();
        try {
            $request->validate([
                'bookChapters' => 'array',
                'bookChapters.*.name' => 'required|string|max:255',
                'bookChapters.*.index' => 'required|numeric|max:999999',
            ]);

            $result = $this->bookChapterService->updateBookChapters(request(), $id);

            if ($result) {
                admin_toastr(__('admin.save_succeeded'));

                return redirect()->route('admin.book-resources.edit', ['book_resource' => $id]);
            }
        } catch (ValidationException $e) {
            admin_error($e->getMessage());
        }

        return redirect()->back()->withInput();
    }
}


