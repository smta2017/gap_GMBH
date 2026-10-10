<?php

namespace App\Helper;

use App\Models\BasicTranslation;
use App\Models\City;
use App\Models\CompanyType;
use App\Models\Country;
use App\Models\Facility;
use App\Models\FieldType;
use App\Models\GolfCourse;
use App\Models\GolfCourseStyle;
use App\Models\Hotel;
use App\Models\Image;
use App\Models\RequestProductTeeTime;
use App\Models\RoomFieldType;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use File;

class Helpers
{

    public static function create_facility($name_en,$name_de, $type, $icon='', $icon_name='', $font_type=1)
    {
        $tanss = array(
            new BasicTranslation(array('language_id'=>1 ,'locale'=>'en','name' => $name_en)),
            new BasicTranslation(array('language_id'=>2 ,'locale'=>'de','name' => $name_de)),
        );

        $service = Facility::create([
            'name'=> $name_en,
            'type' =>  $type,
            'icon'=>$icon,
            'icon_name'=>$icon_name,
            'font_type'=>$font_type,
        ]);
        $service->translations()->saveMany($tanss);
    }

    public static function create_service($name_en,$name_de,$type, $icon='' ,$icon_name='')
    {
        $tanss = array(
            new BasicTranslation(array('language_id'=>1 ,'locale'=>'en','name' => $name_en)),
            new BasicTranslation(array('language_id'=>2 ,'locale'=>'de','name' => $name_de)),
        );

        $service = Service::create([
            'name'=> $name_en,
            'type' => $type,
            'view_type' => 'boolean',
            'icon' => $icon,
            'icon_name' => $icon_name,
            'font_type' => 1,
        ]);    
        $service->translations()->saveMany($tanss);
    }

    
    public static function create_field_type($name_en='',$name_de='',$category='',$status = 1)
    {
        $tanss = array(
            new BasicTranslation(array('language_id'=>1 ,'locale'=>'en','name' => $name_en)),
            new BasicTranslation(array('language_id'=>2 ,'locale'=>'de','name' => $name_de)),
        );

        $service = FieldType::create([
            'name'=> $name_en,
            'category_id'=>$category,
            'status'=> $status,
        ]);    
        $service->translations()->saveMany($tanss);
    }
    
    
    public static function create_RoomFieldType($name_en,$name_de)
    {
        $tanss = array(
            new BasicTranslation(array('language_id'=>1 ,'locale'=>'en','name' => $name_en)),
            new BasicTranslation(array('language_id'=>2 ,'locale'=>'de','name' => $name_de)),
        );

        $field_type = RoomFieldType::create([
            'name'=> $name_en,
        ]);    
        $field_type->translations()->saveMany($tanss);
    }

    public static function create_GolfCourseStyle($name_en,$name_de)
    {
        $tanss = array(
            new BasicTranslation(array('language_id'=>1 ,'locale'=>'en','name' => $name_en)),
            new BasicTranslation(array('language_id'=>2 ,'locale'=>'de','name' => $name_de)),
        );

        $GolfCourseStyle = GolfCourseStyle::create([
            'name'=> $name_en,
        ]);    
        $GolfCourseStyle->translations()->saveMany($tanss);
    }


    public static function upadte_GolfCourseStyle($style_id,$name_en,$name_de)
    {
        $GC_style = GolfCourseStyle::find($style_id);
        $tanss = array(
            new BasicTranslation(array('language_id'=>1 ,'locale'=>'en','name' => $name_en)),
            new BasicTranslation(array('language_id'=>2 ,'locale'=>'de','name' => $name_de)),
        );

        $GC_style->translations()->forceDelete();
        $GC_style->translations()->saveMany($tanss);
    }

    public static function deleteItemImages($model,$model_id,$images_ids)
    {

        $modelObject = $model::findOrFail($model_id);
        
        $imagesToDelete = Image::find($images_ids);
        
        foreach ($imagesToDelete as $imageToDelete) {
             
            $d_image_path = public_path($modelObject::IMAGE_PATH) . '/' . $imageToDelete->file_name;
            if(File::exists($d_image_path)) {
                File::delete($d_image_path);
            }
    
            $imageToDelete->delete();
        }

        $modelObject->updateUpdatedAt();

        return $modelObject;

    }

    public static function uploadItemImages($model,$model_id,$request)
    {

        $modelObject = $model::findOrFail($model_id);
       
        if ($request->hasFile('main_image')) {

            $imageName = \Str::random(6) . time().'.'.$request->main_image->extension();  
     
            $request->main_image->move(public_path('images/countries'), $imageName);

            $modelObject->images()->create(['file_name' => $imageName, 'is_main' => '1']);
            
        }

         
        if ($request->hasFile('images')) {
            
            foreach($request->file('images') as $key => $image)
            {

                $imageName = \Str::random(6) . time().'.'.$image->extension();  
     
                $image->move(public_path($modelObject::IMAGE_PATH), $imageName);
    
                $image_data = [
                    'file_name' => $imageName ,
                    'size'=> (isset($request->size[$key])) ? $request->size[$key] : '',
                    'alt'=> (isset($request->alt[$key])) ? $request->alt[$key] : '',
                    'original_file_name'=> (isset($request->original_file_name[$key])) ? $request->original_file_name[$key] : '',
                    'rank'=> (isset($request->rank[$key])) ? $request->rank[$key] : '',
                ];
    
                $modelObject->images()->create($image_data);
                 
            }
        }

        $modelObject->updateUpdatedAt();


        return $modelObject;

    }


    public static function pulk_publish($model=null)
    {

        $models = [GolfCourse::class,Hotel::class,Country::class,City::class];
        $arr = [];
                foreach ($models as $model) {
                    $results = $model::query();
                    
                        $results->where(function ($query){
                            $query->where('show_website',1);
                            $query->whereRaw('updated_at > published_at');
                        })->orWhere(function ($query){
                            $query->where('show_website',1);
                            $query->where('published_at');
                        })->orWhere(function ($query){
                            $query->where('show_website',1);
                            $query->whereRaw('updated_at <= published_at');
                        });
        
                        $item_data = $results->get('id');
                        
                        if ($item_data) {
                            foreach ($item_data as $item){
                                    self::sendCurl($item->id,$model);
                            }
                        }
                        \array_push($arr,[ explode('\\', $model)[2],$item_data]);
                }
        
                return $arr;
        return "<h1>Published</h1>";
    }


    public static function sendCurl($id,  $model)
    {
        $type =strtolower(explode('\\', $model)[2]);
         
        $url = env('PULK_PUBLISH_URL');          
  
        $data = [  
                    "auth"=>"GAP_API",
                    "token"=> env("PULK_PUBLISH_TOKEN"),
                    "type"=>$type,
                    "object_id"=>$id
                ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Content-Type: application/json',
        ];
    
        
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        
        // Close connection
        curl_close($ch);
    }


    
    public static function getSenderEmails(RequestProductTeeTime $requestTeeTime)
    {

        $emails = [];
        $ModelsRequest =$requestTeeTime->requestProduct->destination->request;
       
        // Check tee time user type if is Agancy type (TA/TO)
        if(self::TTUpdateorType($requestTeeTime)=='agancy'){
            // Check it TeeTime product handler by (Hotel or DMC Handler)
            if(in_array($requestTeeTime->requestProduct->service_handler_type_id, [CompanyType::HO,CompanyType::DMC]))// Hotel or DMC Handler
            { 
                if ($requestTeeTime->requestProduct->get_service_handler_info()) {
                    $emails = ($requestTeeTime->requestProduct->get_service_handler_info()->email != '') ? [$requestTeeTime->requestProduct->get_service_handler_info()->email] : ['NO@EMAIL.FOUND'];
                }
            } 
            // Check it TeeTime product handler by (GolfCourse)
            elseif(in_array($requestTeeTime->requestProduct->service_handler_type_id, [CompanyType::GC])){
                if($requestTeeTime->golfcourse)
                {
                   $emails = [$requestTeeTime->golfcourse->email];
                }
            }
        }
        // Check tee time user type if is SP type (GolfCourse, Hotel or DMC Handler)
        elseif(self::TTUpdateorType($requestTeeTime) =='s_provider'){
            $emails =  [$ModelsRequest->getAgencyOperatorsEmail()];
        }

        
        return $emails;
    }


    public static function getSenderPushNotification(RequestProductTeeTime $requestTeeTime)
    {

        $ids = [];
        $ModelsRequest =$requestTeeTime->requestProduct->destination->request;
        
        // Check tee time user type if is Agancy type (TA/TO)
        if(in_array($requestTeeTime->UpdatedUser->details->company->company_type_id,CompanyType::AGENCY)){
            // Check it TeeTime product handler by (Hotel or DMC Handler)
            if(in_array($requestTeeTime->requestProduct->service_handler_type_id, [CompanyType::HO,CompanyType::DMC]))// Hotel or DMC Handler
            { 
                if ($requestTeeTime->requestProduct->get_service_handler_info()) {
                    $ids = [$requestTeeTime->requestProduct->get_service_handler_info()->id];
                }
            } 
            // Check it TeeTime product handler by (GolfCourse)
            elseif(in_array($requestTeeTime->requestProduct->service_handler_type_id, [CompanyType::GC])){
                if($requestTeeTime->golfcourse)
                {
                   $ids = [$requestTeeTime->golfcourse->company_id];
                }
            }
        }
        // Check tee time user type if is SP type (GolfCourse, Hotel or DMC Handler)
        elseif(in_array($requestTeeTime->UpdatedUser->details->company->company_type_id,CompanyType::SProvider)){
            $ids =  [$ModelsRequest->getAgencyOperatorsCompanyIds()];
        }

        
        return $ids;
    }



    public static function getSPHandlerEmail(RequestProductTeeTime $requestTeeTime)
    {

        $emails = [];
        
        // Check tee time user type if is Agancy type (TA/TO)
        if( $requestTeeTime->UpdatedUser && in_array($requestTeeTime->UpdatedUser->details->company->company_type_id,CompanyType::AGENCY)){
            // Check it TeeTime product handler by (Hotel or DMC Handler)
            if(in_array($requestTeeTime->requestProduct->service_handler_type_id, CompanyType::SProvider))// Hotel or DMC Handler
            { 
                if ($requestTeeTime->requestProduct->get_service_handler_info()) {
                    //Get 
                    $emails = ($requestTeeTime->requestProduct->get_service_handler_info()->email != '') ? [$requestTeeTime->requestProduct->get_service_handler_info()->email] : ['NO@EMAIL.FOUND'];
                }
            } 
            // Check it TeeTime product handler by (GolfCourse)
            elseif(in_array($requestTeeTime->requestProduct->service_handler_type_id, [CompanyType::GC])){
                if($requestTeeTime->golfcourse)
                {
                   $emails = [$requestTeeTime->golfcourse->email];
                }
            }
        }
        
        return $emails;
    }



    public static function getSPHandlerIds(RequestProductTeeTime $requestTeeTime)
    {

        // $auth_user=auth()->user();
        $ids = [];
        if(in_array($requestTeeTime->user->details->company->company_type_id,CompanyType::AGENCY)){
            if(in_array($requestTeeTime->requestProduct->service_handler_type_id, CompanyType::SProvider))// Hotel or DMC Handler
            {
                $ids = ($requestTeeTime->requestProduct->get_service_handler_info()) ? [$requestTeeTime->requestProduct->get_service_handler_info()->id] : [];
            } 
            elseif(in_array($requestTeeTime->requestProduct->service_handler_type_id, [CompanyType::GC])){
                if($requestTeeTime->golfcourse)
                {
                   $ids = [$requestTeeTime->golfcourse->company_id];
                }
            }
        }

        return $ids;
    }




    public static function TTCreatorType(RequestProductTeeTime $teeTime)
    {
        if (in_array($teeTime->createdbyuser->details->company->company_type_id, CompanyType::AGENCY)) {
            return 'agancy';
        } elseif (in_array($teeTime->createdbyuser->details->company->company_type_id, CompanyType::SProvider)) {
            return 's_provider';
        }
    }

    public static function TTUpdateorType(RequestProductTeeTime $teeTime)
    {
        if (in_array($teeTime->UpdatedUser->details->company->company_type_id, CompanyType::AGENCY)) {
            return 'agancy';
        } elseif (in_array($teeTime->UpdatedUser->details->company->company_type_id, CompanyType::SProvider)) {
            return 's_provider';
        }elseif ($teeTime->UpdatedUser->details->company->company_type_id == CompanyType::GG) {
            return 'golf_globe';
        }
    }


}

