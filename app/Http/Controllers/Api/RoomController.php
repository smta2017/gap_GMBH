<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Facility;
use App\Models\Language;
use App\Models\Image;
use App\Http\Resources\RoomResource;
use App\Models\Hotel;
use DB;
use File;

class RoomController extends Controller
{

    public function index(){
        $packages = new Room();
        return RoomResource::collection($packages->get_pagination());
    }
    
    public function store($hotel_id,Request $request)
    {

        $validated = $request->validate([
            'name'  => "required",
            'code'  => "required|max:3",
            "status" => "required|in:0,1",

            'facilities' => 'array',
            'facilities.*.id' => 'exists:facilities,id',

            'fields' => 'array', 
            'fields.*.translations' => 'array',
            'fields.*.translations.*.language_id' => 'required|exists:languages,id',

            'translations' => 'array',
            'translations.*.language_id' => 'required|exists:languages,id',
        ]);

        $hotel = Hotel::findOrFail($hotel_id);

        try {
            \DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['created_by'] = $user->id;
            $room = $hotel->rooms()->create($data);

            if(is_array($request->facilities))
            {
                $facilitiesData = Facility::whereIn('id', $request->facilities)->get();

                foreach($facilitiesData as $facility)
                {
                    $room->facilities()->attach($facility, ['number' => '1']);
                }
            }

            if(is_array($request->fields))
            {
                foreach($request->fields as $fieldData)
                {        
                    $field = $room->fields()->create($fieldData);
                    if(isset($fieldData['translations']) && is_array($fieldData['translations']) && count($fieldData['translations']) > 0)
                    {
                        foreach($fieldData['translations'] as $translation)
                        {
                            $language = Language::findOrFail($translation['language_id']);
        
                            $translateDescription = (isset($translation['description'])) ? $translation['description'] : null;
        
                            $field->translations()->create([
                                'language_id' => $language->id,
                                'locale' => $language->code,
                                'description' => $translateDescription,
                            ]);
                        }
                    }
                }
            }

            

            \DB::commit();
                        
            $roomData = new RoomResource(Room::find($room->id));

            return response()->json([
                'status' => true,
                'room' => $roomData
            ]);
        } catch (\PDOException $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }



    public function show($id){
        $room = Room::findOrFail($id);
        return new RoomResource($room);
    }

    public function update($id, Request $request)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([

            "status" => "required|in:0,1",

            'facilities' => 'array',
            'facilities.*.id' => 'exists:facilities,id',


            'fields' => 'array', 
            'fields.*.translations' => 'array',
            'fields.*.translations.*.language_id' => 'required|exists:languages,id',

            'translations' => 'array',
            'translations.*.language_id' => 'required|exists:languages,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $room->update($data);
            $room->hotel->updateUpdatedAt();

            if(is_array($request->facilities))
            {
                $room->facilities()->detach();

                $facilitiesData = Facility::whereIn('id', $request->facilities)->get();

                foreach($facilitiesData as $facility)
                {
                    $room->facilities()->attach($facility, ['number' => '1']);
                }
            }

            if(is_array($request->fields))
            {
                $room->fields()->forceDelete();
                foreach($request->fields as $fieldData)
                {        
                    $field = $room->fields()->create($fieldData);
                    if(isset($fieldData['translations']) && is_array($fieldData['translations']) && count($fieldData['translations']) > 0)
                    {
                        foreach($fieldData['translations'] as $translation)
                        {
                            $language = Language::findOrFail($translation['language_id']);
        
                            $translateDescription = (isset($translation['description'])) ? $translation['description'] : null;
        
                            $field->translations()->create([
                                'language_id' => $language->id,
                                'locale' => $language->code,
                                'description' => $translateDescription,
                            ]);
                        }
                    }
                }
            }

            if (is_array($request->images) && ($request->images) > 0) {
                foreach($request->images as $image)
                {
                    if ( Image::find($image['id'])) {
                        Image::find($image['id'])->update([ 
                          'alt' =>  (isset($image['alt'])) ? $image['alt'] : '',
                          'original_file_name' => (isset($image['original_file_name'])) ? $image['original_file_name'] : '', 
                          'size' => (isset($image['size'])) ? $image['size'] : '', 
                          'rank' => (isset($image['rank'])) ? $image['rank'] : ''
                          ]); 
                        
                    }
                }
            }

            DB::commit();
            
            $room->updateUpdatedAt();
            
            $roomData = new RoomResource(Room::find($room->id));

            return response()->json([
                'status' => true,
                'room' => $roomData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function upload_images(Request $request, $id)
    {
        $validated = $request->validate([
            'images' => 'required',
            // 'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deleted_images' => 'array',
            'deleted_images.*' => 'exists:images,id',
        ]);
        
        $room = Room::findOrFail($id);

        if(is_array($request->deleted_images))
        {   
            foreach($room->images()->whereIn('id', $request->deleted_images)->get() as $item)
            {
                $d_image_path = public_path('images/rooms') . '/' . $item->file_name;
                if(File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $item->delete();
            }
            
        }
        $room = Helpers::uploadItemImages(Room::class,$id,$request);


        $roomData = new RoomResource(Room::find($room->id));

        return response()->json([
            'status' => true,
            'room' => $roomData
        ]);

    }

    public function change_main_image(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        
        $validated = $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);
        
        foreach($room->images as $img)
        {
            $img->update([
                'is_main' => '0'
            ]);
        }

        $image = Image::find($request->image_id)->update([
            'is_main' => '1'
        ]);

        $room->hotel->updateUpdatedAt();

        $roomData = new RoomResource(Room::find($room->id));

        return response()->json([
            'status' => true,
            'room' => $roomData
        ]);

    }
    public function delete_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image_id' => 'required|array',
            'image_id.*' => 'required|exists:images,id',
        ]);
        
        $room = Helpers::deleteItemImages(Room::class,$id,$request->image_id);

        $roomData = new RoomResource(Room::find($room->id));

        return response()->json([
            'status' => true,
            'room' => $roomData
        ]);

    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);

        try {
            DB::beginTransaction();

            $room->hotel->updateUpdatedAt();

            $room->delete();

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

    public function getFalseResponse()
    {
        return response()->json([
            'status' => false
        ], 422);
    }

    public function create_new_item($data)
    {
        return Room::create($data);
    }
}
