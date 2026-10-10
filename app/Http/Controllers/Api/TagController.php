<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use DB;

class TagController extends Controller
{
    public function index()
    {      
        $tags = new Tag();

        if(isset(request()->search))
        {
            $tags = $tags->where('name', 'LIKE', '%' . request()->search . '%');
        }

        $tagsData = TagResource::collection($tags->get());

        return response()->json([
            'status' => true,
            'tags' => $tagsData
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);


        try {
            DB::beginTransaction();

            $tag = Tag::create([
                'name' => $request->name,
            ]);
            
            DB::commit();

            $tagData = new TagResource($tag);

            return response()->json([
                'status' => true,
                'tag' => $tagData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
