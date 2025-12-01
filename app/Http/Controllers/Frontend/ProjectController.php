<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Hiển thị trang danh sách TẤT CẢ dự án.
     */
    public function index()
    {
        $projectFeature = Project::where("is_home",1)->where("status",1)->first();
        if (!$projectFeature) { 
            $projectFeature = Project::where("status", 1)->first();
        }
        $projects = Project::where('status', 1)->latest()->paginate(10);
        return view('frontend.projects.index', compact(
            'projectFeature',
            'projects',
        ));
    }

    /**
     * (Hàm này giữ nguyên để SlugController sử dụng)
     * Hiển thị trang CHI TIẾT một dự án.
     * @param Project $project
     * @return \Illuminate\View\View
     */
    public function detail(Project $project)
    {
        // Lấy các dự án liên quan (trừ dự án đang xem)
        $relatedProjects = Project::where("status", 1)
            ->where("id", '!=', $project->id)
            ->latest()
            ->limit(6)
            ->get();

        $pageTitle = $project->name;
        $breadcrumbItems = [
            ['label' => 'Dự án', 'url' => route('frontend.projects.index')],
            ['label' => $project->name],
        ];

        return view("frontend.projects.detail", compact(
            "project",
            "relatedProjects",
            "pageTitle",
            "breadcrumbItems"
        ));
    }
}