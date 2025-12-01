<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Services\PageService;

class PageController extends Controller
{
    protected PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index()
    {
        // Không cần transform data ở đây nữa vì Model đã lo rồi
        $pages = Page::with('details')->orderBy('position')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        try {
            // Validate sơ bộ nếu cần thiết
            // $request->validate([...]);

            $this->pageService->update($page, $request->all());
            return redirect()->back()->with('success', 'Đã lưu thành công.');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}