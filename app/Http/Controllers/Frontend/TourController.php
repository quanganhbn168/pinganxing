<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tour;
use App\Models\TourCategory;

class TourController extends Controller
{
    public function index()
    {
        $categories = TourCategory::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get();
            
        $tours = Tour::where('status', 1)
            ->with(['category.image', 'image', 'tags'])
            ->latest()
            ->paginate(12);

        return view('frontend.tours.index', compact('categories', 'tours'));
    }

    public function category($slug)
    {
        $category = TourCategory::where('slug', $slug)->where('status', 1)->firstOrFail();
        
        $categories = TourCategory::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get();
            
        $tours = Tour::where('status', 1)
            ->where('tour_category_id', $category->id)
            ->with(['category.image', 'image', 'tags'])
            ->latest()
            ->paginate(12);

        return view('frontend.tours.category', compact('category', 'categories', 'tours'));
    }

    public function show($categorySlug, $slug)
    {
        $category = TourCategory::where('slug', $categorySlug)->where('status', 1)->firstOrFail();
        
        $tour = Tour::where('slug', $slug)
            ->where('tour_category_id', $category->id)
            ->where('status', 1)
            ->with(['category.image', 'image', 'banner', 'tags'])
            ->firstOrFail();

        $relatedTours = Tour::where('status', 1)
            ->where('tour_category_id', $category->id)
            ->where('id', '!=', $tour->id)
            ->with(['category.image', 'image', 'tags'])
            ->latest()
            ->take(4)
            ->get();

        return view('frontend.tours.show', compact('tour', 'category', 'relatedTours'));
    }
}
