<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimony;
use App\Models\Image;
use App\Http\Resources\TestimonyResource;
use DB;
use File;

class TestimonyController extends Controller
{

    public function update($id, Request $request)
    {
        $testimony = Testimony::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'text' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $user = $request->user();

            $testimony->update([
                'name' => $request->name,
                'text' => $request->text,
                'updated_by' => $user->id
            ]);

            DB::commit();

            $testimonyData = new TestimonyResource($testimony);

            return response()->json([
                'status' => true,
                'testimony' => $testimonyData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function upload_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $testimony = Testimony::findOrFail($id);

        if ($request->hasFile('image')) {

            $imageName = \Str::random(6) . time().'.'.$request->image->extension();  
     
            $request->image->move(public_path('images/testimonies'), $imageName);

            $image = new Image;
            $image->file_name = $imageName;

            if($testimony->image) {

                $d_image_path = public_path('images/testimonies') . '/' . $testimony->image->file_name;
                if(File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $testimony->image()->update(['file_name' => $imageName]);
            }else{
                $testimony->image()->create(['file_name' => $imageName]);
            }
        }

        $testimonyData = new TestimonyResource(Testimony::find($testimony->id));

        return response()->json([
            'status' => true,
            'testimony' => $testimonyData
        ]);
    }

    public function delete_image(Request $request, $id)
    {
        $testimony = Testimony::findOrFail($id);
        
        if($testimony->image)
        {
            $d_image_path = public_path('images/testimonies') . '/' . $testimony->image->file_name;

            if(File::exists($d_image_path)) {
                File::delete($d_image_path);
            }

            $testimony->image->delete();
        }

        return response()->json([
            'status' => true,
        ]);

    }

    public function destroy($id)
    {
        $testimony = Testimony::findOrFail($id);

        try {
            DB::beginTransaction();

            $testimony->delete();

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
