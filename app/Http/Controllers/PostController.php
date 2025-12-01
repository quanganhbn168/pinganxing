<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Models\PostCategory;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {}

    public function index()
    {
        $posts = $this->postService->getAll();
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = $this->postService->getParentOptions();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->postService->create($request);

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
        $this->postService->update($request, $post);
        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công.');
    }

    public function destroy(Post $post)
    {
        $this->postService->delete($post);
        return redirect()->route('admin.posts.index')->with('success', 'Xoá bài viết thành công.');
    }

    public function detail(Post $post)
    {
        // Eager load category nếu chưa có
        $post->load('category');

        // Bài viết liên quan cùng danh mục, loại trừ chính bài viết hiện tại
        $relatedPosts = Post::where('status', 1)
            ->where('post_category_id', $post->post_category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.post.detail', compact('post', 'relatedPosts'));
    }
    public function postByCate(PostCategory $postCategory)
    {
        if($postCategory->slug == "tieng-trung-doanh-nghiep")
        {
            return redirect()->route("home");
        }
    // Lấy tất cả danh mục để hiển thị ở sidebar
        $allCategories = PostCategory::where('status', 1)->get();

    // Nếu là danh mục cha thì lấy thêm bài viết từ danh mục con
        if ($postCategory->parent_id === 0) {
            $categoryIds = PostCategory::where('parent_id', $postCategory->id)->pluck('id')->toArray();
            $categoryIds[] = $postCategory->id;
        } else {
            $categoryIds = [$postCategory->id];
        }

    // Lấy danh sách bài viết thuộc các danh mục đã xác định
        $posts = Post::whereIn('post_category_id', $categoryIds)
        ->where('status', 1)
        ->latest()
        ->paginate(10);

    // Các bài viết nổi bật: lấy 5 bài mới nhất (tùy chỉnh thêm điều kiện nếu cần)
        $featuredPosts = Post::where('status', 1)
        ->latest('updated_at')
        ->limit(5)
        ->get();

        return view('frontend.post.postByCate', [
            'category' => $postCategory,
            'posts' => $posts,
            'allCategories' => $allCategories,
            'featuredPosts' => $featuredPosts,
        ]);
    }

}
