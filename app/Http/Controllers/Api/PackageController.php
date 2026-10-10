<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\BasicResource;
use App\Http\Resources\PackageResource;
use App\Models\BewotecDavinciService;
use App\Models\FieldType;
use App\Models\Image;
use App\Models\Language;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $PBCodes = Package::whereNotNull('davinci_booking_code')->get();

        foreach ($PBCodes as $key => $PBCode) {
            $count_codes = BewotecDavinciService::whereBookingCode($PBCode->davinci_booking_code)->count();
            $PBCode->update(['price_cache' => $count_codes]);
        }
        
        $packages = new Package();
        return PackageResource::collection($packages->get_pagination());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => 'required',
            'davinci_booking_code' => 'required',
            'fields' => 'required|array',
        ]);

        try {

            \DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $package = $this->create_new_item($data);

            if (is_array($request->fields) && count($request->fields) > 0) {

                foreach ($request->fields as $fieldData) {

                    $field = $package->fields()->create($fieldData);

                    if (isset($fieldData['translations']) && is_array($fieldData['translations']) && count($fieldData['translations']) > 0) {
                        foreach ($fieldData['translations'] as $translation) {
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


            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $image) {

                    $imageName = \Str::random(6) . time() . '.' . $image->extension();

                    $image->move(public_path('images/package'), $imageName);

                    $image = new Image();
                    $image->file_name = $imageName;

                    $package->images()->create(['file_name' => $imageName]);
                }
            }

            \DB::commit();

            return new PackageResource($package);
        } catch (\PDOException $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $package = Package::findOrFail($id);
        return new PackageResource($package);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $validated = $request->validate([
            "name" => 'required',
            'davinci_booking_code' => 'required',
            'fields' => 'required|array',
        ]);


        try {
            \DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $package->update($data);

            if (is_array($request->fields)) {
                $package->fields()->forceDelete();
                foreach ($request->fields as $fieldData) {
                    $field = $package->fields()->create($fieldData);
                    if (isset($fieldData['translations']) && is_array($fieldData['translations']) && count($fieldData['translations']) > 0) {
                        foreach ($fieldData['translations'] as $translation) {
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
                foreach ($request->images as $image) {
                    if (Image::find($image['id'])) {

                        Image::find($image['id'])->update([
                            'alt' => (isset($image['alt'])) ? $image['alt'] : '',
                            'original_file_name' => (isset($image['original_file_name'])) ? $image['original_file_name'] : '',
                            'size' => (isset($image['size'])) ? $image['size'] : '',
                            'rank' => (isset($image['rank'])) ? $image['rank'] : ''
                        ]);
                    }
                }
            }

            \DB::commit();

            $packageData = new PackageResource(Package::find($package->id));

            return response()->json([
                'status' => true,
                'package' => $packageData
            ]);
        } catch (\PDOException $e) {
            \DB::rollBack();
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

        $hotel = Package::findOrFail($id);

        if (is_array($request->deleted_images)) {
            foreach ($hotel->images()->whereIn('id', $request->deleted_images)->get() as $item) {
                $d_image_path = public_path('images/package') . '/' . $item->file_name;
                if (File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $item->delete();
            }
        }

        $hotel = Helpers::uploadItemImages(Package::class, $id, $request);


        $hotelData = new PackageResource(Package::find($hotel->id));

        return response()->json([
            'status' => true,
            'package' => $hotelData
        ]);
    }


    public function change_main_image(Request $request, $id)
    {
        $hotel = Package::findOrFail($id);

        $validated = $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        foreach ($hotel->images as $img) {
            $img->update([
                'is_main' => '0'
            ]);
        }

        $image = Image::find($request->image_id)->update([
            'is_main' => '1'
        ]);

        $hotel->updateUpdatedAt();

        $hotelData = new PackageResource(Package::find($hotel->id));

        return response()->json([
            'status' => true,
            'package' => $hotelData
        ]);
    }

    public function delete_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image_id' => 'required|array',
            'image_id.*' => 'required|exists:images,id',
        ]);

        $hotel = Helpers::deleteItemImages(Package::class, $id, $request->image_id);

        $hotelData = new PackageResource(Package::find($hotel->id));

        return response()->json([
            'status' => true,
            'package' => $hotelData
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Package::destroy($id);
    }


    public function create_new_item($data)
    {
        return Package::create($data);
    }

    public function get_field_types()
    {
        $types = BasicResource::collection(FieldType::active()->packageFields()->get());

        return response()->json([
            'status' => true,
            'field_types' => $types
        ]);
    }
}
