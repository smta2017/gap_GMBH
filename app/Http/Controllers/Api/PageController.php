<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Http\Resources\PageResource;

class PageController extends Controller
{
    public function index()
    {
        $module_id = request()->input('module_id');

        $pages = new Page();

        $pagesData = PageResource::collection($pages->get_all());

        return response()->json([
            'status' => true,
            'pages' => $pagesData
        ]);
    }
}
