<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Intro;
use App\Services\IntroService;
use Illuminate\Http\Request;

class IntroController extends Controller
{
    public function __construct(
        protected IntroService $introService
    ) {}

    public function index()
    {
        $intros = Intro::latest()->paginate(20);
        return view('admin.intros.index', compact('intros'));
    }

    public function create()
    {
        return view('admin.intros.create');
    }

    public function store(Request $request)
    {
        $this->introService->create($request);
        return redirect()->route('admin.intros.index')->with('success', 'Intro created successfully.');
    }

    public function edit(Intro $intro)
    {
        return view('admin.intros.edit', compact('intro'));
    }

    public function update(Request $request, Intro $intro)
    {
        $this->introService->update($request, $intro);
        return redirect()->route('admin.intros.index')->with('success', 'Intro updated successfully.');
    }

    public function destroy(Intro $intro)
    {
        $this->introService->delete($intro);
        return redirect()->route('admin.intros.index')->with('success', 'Intro deleted successfully.');
    }

}
