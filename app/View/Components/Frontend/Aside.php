<?php

namespace App\View\Components\Frontend;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Category;
use App\Models\Post;

class Aside extends Component
{
    public $productCategories;
    public $latestPosts;

    public function __construct()
    {
        $this->productCategories = Category::whereNull('parent_id')
                                ->with('children')
                                ->where('status', 1)
                                ->orderBy('position')
                                ->get();
    
        $this->latestPosts = Post::where('status', 1)
                            ->with('image')
                            ->latest()
                            ->limit(5)
                            ->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.frontend.aside');
    }
}
