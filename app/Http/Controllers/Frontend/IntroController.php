<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Intro;

class IntroController extends Controller
{
    public function index(\App\Settings\PageSettings $pageSetting)
    {
        return view('frontend.intro', compact('pageSetting'));
    }
}
