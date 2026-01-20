<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Post;

use Illuminate\Http\Request;

use App\Services\PostService;

use App\Services\PostCategoryService;
use App\Http\Requests\PostRequest;
use App\Traits\UploadImageTrait;

class PostController extends Controller

{
    use UploadImageTrait;

    public function __construct(

        protected PostService $postService

    ) {}



    /** Index: lọc + phân trang (chuẩn với view index mới) */

    public function index(Request $request)

    {

        [$posts, $filterCategories] = $this->postService->list($request);

        return view('admin.posts.index', compact('posts', 'filterCategories'));

    }

    

    public function create()

    {

        $categories = PostCategory::pluck("name","id");

        return view('admin.posts.create', compact('categories'));

    }

    public function store(PostRequest $request) 

    {

        $validatedData = $request->validated(); 

        $validatedData['image_original_path'] = $this->processImageInput($request, 'image_original_path', null, 'posts', false);

        $validatedData['banner_original_path'] = $this->processImageInput($request, 'banner_original_path', null, 'posts/banner', false);

        $this->postService->create($validatedData);

        return $request->has('save_new')

        ? redirect()->route('admin.posts.create')->with('success', 'Thêm bài viết mới thành công.')

        : redirect()->route('admin.posts.index')->with('success', 'Thêm bài viết thành công.');

    }

    public function edit(Post $post)

    {

        $categories = PostCategory::pluck("name","id");

        return view('admin.posts.edit', compact('post', 'categories'));

    }

    public function update(PostRequest $request, Post $post)

    {

        $validatedData = $request->validated();

        // 1. Image
        $currentImage = optional($post->mainImage())->original_path ?? $post->image;
        $newImage = $this->processImageInput($request, 'image_original_path', $currentImage, 'posts', false);

        if ($newImage !== $currentImage) {
            $validatedData['image_original_path'] = $newImage;
        } else {
            unset($validatedData['image_original_path']);
        }

        // 2. Banner
        $currentBanner = optional($post->bannerImage())->original_path ?? $post->banner;
        $newBanner = $this->processImageInput($request, 'banner_original_path', $currentBanner, 'posts/banner', false);

        if ($newBanner !== $currentBanner) {
            $validatedData['banner_original_path'] = $newBanner;
        } else {
            unset($validatedData['banner_original_path']);
        }

        $this->postService->update($post, $validatedData);

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công.');

    }

    public function destroy(Post $post)

    {

        $this->postService->delete($post);

        return redirect()->route('admin.posts.index')->with('success', 'Xoá bài viết thành công.');

    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:posts,id',
            'action' => 'required|string|in:delete,active,inactive',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                $this->postService->bulkDelete($ids);
                $message = "Đã xóa thành công $count bài viết.";
                break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
}