<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Intro;

class IntroController extends Controller
{
    public function index()
    {
        $intro = Intro::findOrFail(1);
        return view('frontend.intro',compact('intro'));
    }

    public function getBySlug($intro)
    {
        return view('frontend.intro.introDetail', compact('intro'));
    }
}
