<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceMiniResource;
use App\Models\BasicTranslation;
use Illuminate\Http\Request;
use App\Models\Service;
class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::get();

        return response()->json([
            'status' => true,
            'services' => ServiceMiniResource::collection($services)
        ]);
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
            'translations'=>'required|array',
            'type' => 'required',            
        ]);


        $tanss = [];
        foreach ($request->translations as $translation) {
            $tanss[]= new BasicTranslation($translation);
        }

        $service = Service::create($request->all());

        $service->translations()->saveMany($tanss);

        return response()->json([
            'status' => true,
            'service' => new ServiceMiniResource($service)
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_pulck(Request $request)
    {
        $validated = $request->validate([
            'services'=>'required|array'           
        ]);

        foreach ($request->services as $service) {
            $tanss = [];
            foreach ($service['translations'] as $translation) {
                $tanss[]= new BasicTranslation($translation);
            }

            $service = Service::create($service);

            $service->translations()->saveMany($tanss);
        }
        return response()->json([
            'status' => true
        ]);

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);

        return response()->json([
            'status' => true,
            'service' => new ServiceMiniResource($service)
        ]);
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
        $validated = $request->validate([
            'translations'=>'array',
        ]);

        $service = Service::findOrFail($id);

         
            foreach ($request->translations as $translation) 
            {
                $trans = $service->translations->where('language_id',$translation['language_id'])->first();
                BasicTranslation::find($trans->id)->update(['name'=>$translation['name']]);
            }
         

        $service->update($request->all());

        $service = Service::find($id);
        return response()->json([
            'status' => true,
            'service' =>  new ServiceMiniResource($service)
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
        $service = Service::destroy($id);

        return response()->json([
            'status' => true,
            'service' => $service
        ]);
    }
}
