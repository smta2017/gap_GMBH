<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Room extends Model
{
    use SoftDeletes, HasFactory;

    public const IMAGE_PATH = 'images/rooms';

    protected $fillable = [
        'name',
        'code',
        'room_type_id',
        'hotel_id',
        'show_website',
        'status',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function get_pagination()
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $results = $this->query();


        return $results->paginate($pagination);
    }

    public function type()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }
    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'room_facility')->withPivot(["number"]);
    }

    public function fields()
    {
        return $this->hasMany(RoomField::class, 'room_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function imagesFullData()
    {
        return $this->images()->select('id', DB::raw("CONCAT('".asset('images/rooms')."', '/', file_name) AS file_name"), 'is_main','alt','original_file_name', 'size', 'rank')->orderBy('rank');
    } 

    public function get_main_image()
    {
        $image = $this->images()->where('is_main', '1')->first();
        if($image)
        {
            return asset('images/rooms') . '/' . rawurlencode($image->file_name);
        }
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
    
    public function isPublishRequired()
    {
        if($this->updated_at > $this->published_at)
        {
            return true;
        }
        return false;
    }

    public function updateUpdatedAt()
    {
        Room::find($this->id)->update([
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
