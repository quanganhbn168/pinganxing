<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlugAjaxController extends Controller
{
    /**
     * Query params:
     * - slug: string (bắt buộc)
     * - table: string (bảng chính cần check, vd: posts, categories)
     * - id: int|null (id bản ghi hiện tại khi edit để exclude)
     * - field: string (mặc định 'slug' nếu bảng chính dùng cột khác thì truyền)
     */
    // app/Http/Controllers/SlugController.php
public function check(Request $r)
{
    $slug  = $r->input('slug');
    $table = $r->input('table');
    $field = $r->input('field', 'slug');
    $id    = $r->input('id');

    $exists = \DB::table($table)
        ->where($field, $slug)
        ->when($id, fn($q) => $q->where('id', '!=', $id))
        ->exists();

    return response()->json(['ok' => !$exists]);
}

}
