<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use DB;

class CommentController extends Controller
{

    public function update($id, Request $request)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'comment' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $comment->update([
                'comment' => $request->comment,
            ]);

            DB::commit();

            $commentData = new CommentResource($comment);

            return response()->json([
                'status' => true,
                'comment' => $commentData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        try {
            DB::beginTransaction();

            $comment->delete();

            DB::commit();

            return response()->json([
                'status' => true,
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
