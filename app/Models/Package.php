<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use  SoftDeletes,HasFactory;
    
    public const IMAGE_PATH = 'images/package';

    protected $fillable = [
        'name',
        'davinci_booking_code',
        'price_cache'
    ];

    public function get_pagination()
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $results = $this->query();

        if(request()->search)
        {
            $results = $results->where('id', request()->search)
                                ->orWhere('name', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('davinci_booking_code', 'LIKE', '%' . request()->search . '%');
        }

        if(request()->davinci_booking_code)
        {
            $results = $results->where('davinci_booking_code', request()->davinci_booking_code);
        }

        return $results->paginate($pagination);
    }
    public function fields()
    {
        return $this->morphMany(Field::class, 'fieldable');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function get_main_image()
    {
        $image = $this->images()->where('is_main', '1')->first();
        if($image)
        {
            return asset('images/package') . '/' . rawurlencode($image->file_name);
        }
    }
    public function imagesFullData()
    {
        return $this->images()->select('id', \DB::raw("CONCAT('".asset('images/package')."', '/', file_name) AS file_name"), 'is_main','alt','original_file_name', 'size', 'rank')->orderBy('rank');
    } 

    public function imagesFullDataURLEncode()
    {
        $images = $this->imagesFullData;

        foreach($images as $image)
        {
            $imageParts = explode('/', $image->file_name);

            $imageNameEncode = $imageParts[count($imageParts) -1];

            $image->file_name = asset('images/package') . '/' . rawurlencode($imageNameEncode);
        }

        return $images;
    }

    public function updateUpdatedAt()
    {
        $new_date =  \Carbon\Carbon::now()->format('Y-m-d H:i:s') ;
        Hotel::find($this->id)->update([
            'updated_at' =>$new_date
        ]);
    }
}
