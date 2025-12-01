<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DuplicatorService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class DuplicateController extends Controller
{
    public function duplicate(Request $request, DuplicatorService $dup)
    {
        $map = config('duplicate.models', []);

        $data = $request->validate([
            'model' => ['required', Rule::in(array_keys($map))],
            'id'    => ['required','integer','min:1'],
        ]);

        $cfg   = $map[$data['model']];
        $class = $cfg['class'];
        $edit  = $cfg['edit_route'];

        $record = $class::find($data['id']);
        if (! $record) {
            return response()->json(['success'=>false,'error'=>'Bản ghi không tồn tại.'], Response::HTTP_NOT_FOUND);
        }

        $copy = $dup->duplicate($record, $cfg);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'id'       => $copy->getKey(),
                'edit_url' => $edit ? route($edit, $copy) : null,
            ]);
        }
        return $edit
            ? redirect()->route($edit, $copy)->with('success','Nhân bản thành công!')
            : back()->with('success','Nhân bản thành công!');
    }
}
