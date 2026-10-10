<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\GolfCourse;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\Facility;
use App\Models\Board;
use App\Models\Note;
use App\Models\Image;
use App\Models\Link;
use App\Models\Field;
use App\Models\FieldType;
use App\Models\RoomFieldType;
use App\Models\RoomType;
use App\Models\RoomView;
use App\Models\RoomBoard;
use App\Models\TourOperator;

use App\Models\Service;
use App\Models\ServiceProperty;
use App\Models\ServiceAddon;
use App\Models\ServiceDetails;
use App\Models\Language;
use App\Models\Tag;

use App\Models\ObjectService;
use App\Models\ObjectServiceAddon;
use App\Models\ObjectServiceFeeDetails;
use App\Models\ObjectServiceProperty;
use App\Http\Resources\ServiceResource;

use App\Http\Resources\HotelResource;
use App\Http\Resources\HotelDetailsResource;
use App\Http\Resources\CompanyTypeResource;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\BasicResource;
use App\Http\Resources\RoomResource;
use Carbon\Carbon;
use DB;
use File;
use App\Imports\HotelImport;
use App\Models\BasicTranslation;
use App\Models\RequestDestination;
use App\Models\RequestProduct;
use App\Models\Room;
use Maatwebsite\Excel\Facades\Excel;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = new Hotel();

        $user = request()->user();

        if ($user->details->company->company_type_id != '1') {
            $childs = $user->childs->where('child_type_id', '4')->pluck('child_id')->toArray();
            $hotels = $hotels->whereIn('id', $childs);
        }

        $hotelsData = HotelResource::collection($hotels->get_pagination());

        return response()->json([
            'status' => true,
            'hotels' => $hotelsData->response()->getData()
        ]);
    }

    public function get_all()
    {
        // $filter = $this->prepare_filter(request());

        $hotels = new Hotel();

        $hotelsData = HotelResource::collection($hotels->get_all());

        return response()->json([
            'status' => true,
            'hotels' => $hotelsData
        ]);
    }

    public function get_request_hotel()
    {
        $hotels = new Hotel();

        $hotelsData = HotelResource::collection($hotels->get_hotel_for_request());

        return response()->json([
            'status' => true,
            'hotels' => $hotelsData
        ]);
    }

    public function show($id)
    {
        $hotel = Hotel::findOrFail($id);

        $hotelData = new HotelDetailsResource($hotel);

        return response()->json([
            'status' => true,
            'hotel' => $hotelData,
        ]);
    }

    public function get_facilities()
    {
        $facilities = BasicResource::collection(Facility::where('type', 'Hotel')->get());

        return response()->json([
            'status' => true,
            'facilities' => $facilities
        ]);
    }

    public function get_room_facilities()
    {
        $facilities = BasicResource::collection(Facility::where('type', 'Room')->get());

        return response()->json([
            'status' => true,
            'facilities' => $facilities
        ]);
    }

    public function get_boards()
    {
        $boards = Board::select(['id', 'name'])->get();

        return response()->json([
            'status' => true,
            'boards' => $boards
        ]);
    }

    public function get_field_types()
    {
        $types = BasicResource::collection(FieldType::active()->HotelFields()->get());

        return response()->json([
            'status' => true,
            'field_types' => $types
        ]);
    }

    public function get_room_field_types()
    {
        $types = BasicResource::collection(RoomFieldType::where('status', '1')->get());

        return response()->json([
            'status' => true,
            'room_field_types' => $types
        ]);
    }

    public function get_room_types()
    {
        $types = BasicResource::collection(RoomType::where('status', '1')->get());

        return response()->json([
            'status' => true,
            'room_types' => $types
        ]);
    }

    public function get_room_views()
    {
        $types = BasicResource::collection(RoomView::where('status', '1')->get());

        return response()->json([
            'status' => true,
            'room_views' => $types
        ]);
    }

    public function get_room_boards()
    {
        $types = BasicResource::collection(RoomBoard::where('status', '1')->get());

        return response()->json([
            'status' => true,
            'room_boards' => $types
        ]);
    }


    public function get_activities($id)
    {
        $hotel = Hotel::findOrFail($id);

        $activities = ActivityResource::collection($hotel->activities);

        return response()->json([
            'status' => true,
            'activities' => $activities
        ]);
    }

    public function get_services()
    {
        // return "get_services";
        $type = request()->type;

        $typeList = request()->type_list;

        $serviceObj = Service::where('active', '1')->whereIn('type', Service::HOTEL_SERVICES_LIST)->get();

        $services = ServiceResource::collection($serviceObj);

        return response()->json([
            'status' => true,
            'services' => $services
        ]);
    }

    public function get_basics()
    {
        $facilities = BasicResource::collection(Facility::where('type', 'Hotel')->get());

        $roomFacilities = BasicResource::collection(Facility::where('type', 'Room')->where('status', '1')->get());

        $boards = Board::select(['id', 'name'])->get();
        $types = BasicResource::collection(FieldType::where('status', '1')->get());

        $roomFieldTypes = BasicResource::collection(RoomFieldType::where('status', '1')->get());

        $roomTypes = BasicResource::collection(RoomType::where('status', '1')->get());

        $roomViews = BasicResource::collection(RoomView::where('status', '1')->get());
        $roomBoards = BasicResource::collection(RoomBoard::where('status', '1')->get());

        return response()->json([
            'status' => true,
            'facilities' => $facilities,
            'room_facilities' => $roomFacilities,
            'boards' => $boards,
            'field_types' => $types,
            'room_field_types' => $roomFieldTypes,
            'room_types' => $roomTypes,
            'room_views' => $roomViews,
            'room_boards' => $roomBoards,
        ]);
    }

    public function getAutoBookingCode()
    {
        if (Hotel::count()) {
            $nextId = Hotel::latest()->first()->id + 1;
        } else {
            $nextId = 0;
        }

        return 'HO-' . $nextId;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "company_id" => 'required|exists:companies,id',
            "name" => 'required',

            "active" => "required|in:0,1",
            "direct_contract" => "required|in:0,1",
            // "via_dmc" => "required|in:0,1",

            // "handler_type_id" => 'exists:company_types,id',
            // "handler_id" => 'exists:companies,id',

            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            // "location_link" => 'required',
            "email" => "required|email",

            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",


            'facilities' => 'array',
            'facilities.*.id' => 'exists:facilities,id',

            'tags' => 'array',
            'tags.*' => 'exists:tags,id',

            'golfcourses' => 'array',
            'golfcourses.*' => 'exists:golf_courses,id',

            'related_golfcourses' => 'array',
            'related_golfcourses.*' => 'exists:golf_courses,id',

            // 'touroperators' => 'array',
            // 'touroperators.*' => 'exists:tour_operators,id',

            'boards' => 'array',
            'boards.*' => 'exists:boards,id',

            // 'notes' => 'array',

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

            $data['created_by'] = $user->id;

            $data['booking_code'] = $this->getAutoBookingCode();

            $hotel = $this->create_new_item($data);

            if (is_array($request->boards) && count($request->boards) > 0) {
                $boards = Board::whereIn('id', $request->boards)->get();
                foreach ($boards as $board) {
                    $hotel->boards()->save($board);
                }
            }

            if (is_array($request->facilities) && count($request->facilities) > 0) {
                foreach ($request->facilities as $facility) {
                    if (isset($facility['id']) && isset($facility['number'])) {
                        $hotel->facilities()->attach([$facility['id'] => ['number' => $facility['number']]]);
                    }
                }
            }

            if (is_array($request->golfcourses) && count($request->golfcourses) > 0) {
                $courses = Golfcourse::whereIn('id', $request->golfcourses)->get();
                foreach ($courses as $course) {
                    $hotel->golfcourses()->attach($course->id, ['type' => 'owned']);
                }
            }

            if (is_array($request->related_hotels) && count($request->related_hotels) > 0) {
                $hotel->relatedHotels()->detach();
                $hotels = Hotel::whereIn('id', $request->related_hotels)->get();
                foreach ($hotels as $related_hotel) {
                    $hotel->relatedHotels()->attach($related_hotel['id']);
                }
            }

            if (is_array($request->tags) && count($request->tags) > 0) {
                $tags = Tag::whereIn('id', $request->tags)->get();
                foreach ($tags as $tag) {
                    $hotel->tags()->save($tag);
                }
            }

            if (is_array($request->related_golfcourses) && count($request->related_golfcourses) > 0) {
                $courses = Golfcourse::whereIn('id', $request->related_golfcourses)->get();
                foreach ($courses as $course) {
                    $hotel->golfcourses()->attach($course->id, ['type' => 'related']);
                }
            }

            if (is_array($request->touroperators) && count($request->touroperators) > 0) {
                $opers = TourOperator::whereIn('id', $request->touroperators)->get();
                foreach ($opers as $oper) {
                    if ($oper->id == '2000') {
                        $hotel->update([
                            'is_golf_globe' => '1'
                        ]);
                    }
                    $hotel->touroperators()->save($oper);
                }
            }

            // if(is_array($request->notes) && count($request->notes) > 0)
            // {
            //     foreach($request->notes as $r_note)
            //     {
            //         $note = new Note;
            //         $note->title = $r_note;

            //         $hotel->notes()->create(['title' => $r_note]);
            //     }
            // }

            if (is_array($request->fields) && count($request->fields) > 0) {
                foreach ($request->fields as $fieldData) {
                    $field = $hotel->fields()->create($fieldData);
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

            if ($request->translations && is_array($request->translations) && count($request->translations) > 0) {
                foreach ($request->translations as $translation) {
                    $language = Language::findOrFail($translation['language_id']);

                    $translateName = (isset($translation['name'])) ? $translation['name'] : null;
                    $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
                    $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

                    $hotel->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName,
                        'website_description' => $translateWebsiteDescription,
                        'internal_description' => $translateInternalDescription,
                    ]);
                }
            }

            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $image) {

                    $imageName = \Str::random(6) . time() . '.' . $image->extension();

                    $image->move(public_path('images/eggheads'), $imageName);

                    $image = new Image;
                    $image->file_name = $imageName;

                    $hotel->images()->create(['file_name' => $imageName]);
                }
            }

            if (is_array($request->links_hd_images) && count($request->links_hd_images) > 0) {

                foreach ($request->links_hd_images as $singleLink) {
                    $hotel->links()->create(['link' => $singleLink, 'type' => 'hd_images']);
                }
            }

            if (is_array($request->links_logo_images) && count($request->links_logo_images) > 0) {

                foreach ($request->links_logo_images as $singleLink) {
                    $hotel->links()->create(['link' => $singleLink, 'type' => 'logo_images']);
                }
            }


            if (is_array($request->davinici_codes)) {
                foreach ($request->davinici_codes as $davinici_code) {
                    $hotel->DaviniciCodes()->create(['davinici_code' => $davinici_code]);
                }
            }

            DB::commit();

            $hotelData = new HotelDetailsResource($hotel);

            return response()->json([
                'status' => true,
                'hotel' => $hotelData,
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_services($id, Request $request)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            // 'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.selected_option' => 'required',
            'services.*.properties' => 'array',
            'services.*.properties.*.property_id' => 'exists:service_properties,id',
            'services.*.addons' => 'array',
            'services.*.addons.*.addon_id' => 'exists:service_addons,id',
            'services.*.addond.fee_details' => 'array',
            'services.*.addond.fee_details.*.fee_id' => 'exists:sertice_fee_details,id',
            'services.*.fee_details' => 'array',
            'services.*.fee_details.*.fee_id' => 'exists:service_fee_details,id',
        ]);

        try {
            DB::beginTransaction();

            $hotelArrayData = Service::HOTEL_SERVICES_LIST;
            if (is_array($request->services)) {

                $servicesData = $request->services;

                ObjectService::where('child_id', $hotel->id)->whereHas('service', function ($q) use ($hotelArrayData) {
                    $q->whereIn('type', $hotelArrayData);
                })->forceDelete();
                ObjectServiceAddon::where('child_id', $hotel->id)->whereHas('service', function ($q) use ($hotelArrayData) {
                    $q->whereIn('type', $hotelArrayData);
                })->forceDelete();
                ObjectServiceProperty::where('child_id', $hotel->id)->whereHas('service', function ($q) use ($hotelArrayData) {
                    $q->whereIn('type', $hotelArrayData);
                })->forceDelete();
                ObjectServiceFeeDetails::where('child_id', $hotel->id)->whereHas('service', function ($q) use ($hotelArrayData) {
                    $q->whereIn('type', $hotelArrayData);
                })->forceDelete();


                foreach ($request->services as $service) {
                    $serviceObj = Service::findOrFail($service['service_id']);

                    if (isset($service['active'])) {
                        $isServiceActive = $service['active'];
                    } else {
                        $isServiceActive = 0;
                    }
                    ObjectService::create([
                        'child_id' => $hotel->id,
                        'service_id' => $service['service_id'],
                        'type' => $serviceObj->type,
                        'qty' => $service['qty'],
                        'fees' => $service['fees'],
                        'selected_option' => $service['selected_option'],
                        'notes' => $service['notes'],
                        'active' => $isServiceActive,
                    ]);

                    if (isset($service['properties']) && is_array($service['properties'])) {
                        foreach ($service['properties'] as $property) {
                            if (isset($property['notes'])) {
                                $propertyNote = $property['notes'];
                            } else {
                                $propertyNote = null;
                            }

                            ObjectServiceProperty::create([
                                'child_id' => $hotel->id,
                                'service_id' => $service['service_id'],
                                'service_property_id' => $property['property_id'],
                                'selected_option' => $property['selected_option'],
                                'notes' => $propertyNote,
                            ]);
                        }
                    }

                    if (isset($service['fee_details']) && is_array($service['fee_details'])) {
                        foreach ($service['fee_details'] as $fee) {
                            ObjectServiceFeeDetails::create([
                                'child_id' => $hotel->id,
                                'service_id' => $service['service_id'],
                                'service_fees_details_id' => $fee['fee_id'],
                                'qty' => $fee['qty'],
                                'fees' => $fee['fees'],
                                'unit' => $fee['unit'],
                                'notes' => $fee['notes']
                            ]);
                        }
                    }

                    if (isset($service['addons']) && is_array($service['addons'])) {
                        foreach ($service['addons'] as $addon) {
                            ObjectServiceAddon::create([
                                'child_id' => $hotel->id,
                                'service_id' => $service['service_id'],
                                'service_addon_id' => $addon['addon_id'],

                                'qty' => $addon['qty'],
                                'fees' => $addon['fees'],

                                'selected_option' => $addon['selected_option'],
                                'notes' => $addon['notes'],
                            ]);
                        }

                        if (isset($addon['fee_details']) && is_array($addon['fee_details'])) {
                            foreach ($addon['fee_details'] as $fee) {
                                ObjectServiceFeeDetails::create([
                                    'child_id' => $hotel->id,
                                    'service_id' => $service['service_id'],
                                    'service_addon_id' => $addon['addon_id'],
                                    'service_fees_details_id' => $fee['fee_id'],
                                    'qty' => $fee['qty'],
                                    'fees' => $fee['fees'],
                                    'unit' => $fee['unit'],
                                    'notes' => $fee['notes']
                                ]);
                            }
                        }
                    }
                }
            }

            $hotel->updateUpdatedAt();

            DB::commit();

            $hotelData = new HotelDetailsResource(Hotel::find($hotel->id));

            return response()->json([
                'status' => true,
                'hotel' => $hotelData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_room($id, Request $request)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $data['status'] = '1';
            $data['show_website'] = '0';

            $room = $hotel->rooms()->create($data);

            $roomData = new RoomResource($room);

            $hotel->updateUpdatedAt();

            DB::commit();

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

    public function store_activity($id, Request $request)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required',
            // 'start_time' => 'string',
            // 'end_time' => 'string',

            // 'start_recur' => 'string',
            // 'end_recur' => 'string',

            // 'duration' => 'string',
            // 'days_of_week' => 'array',
            // 'is_recurring' => 'string|in:0,1',

            // 'color' => 'string',
            'type_id' => 'required|exists:activity_types,id'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            if (is_array($request->days_of_week) && count($request->days_of_week) > 0)
                $data['days_of_week'] = implode(',', $request->days_of_week);

            $hotel->activities()->create($data);

            $hotel->updateUpdatedAt();

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

    public function update($id, Request $request)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            "company_id" => 'required|exists:companies,id',
            "name" => 'required',

            "active" => "required|in:0,1",
            "direct_contract" => "required|in:0,1",
            // "via_dmc" => "required|in:0,1",

            // "handler_type_id" => 'exists:company_types,id',
            // "handler_id" => 'exists:companies,id',

            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            // "location_link" => 'required',
            "email" => "required|email",

            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",


            'facilities' => 'array',
            'facilities.*.id' => 'exists:facilities,id',

            'tags' => 'array',
            'tags.*' => 'exists:tags,id',

            'golfcourses' => 'array',
            'golfcourses.*' => 'exists:golf_courses,id',

            // 'touroperators' => 'array',
            // 'touroperators.*' => 'exists:tour_operators,id',

            'related_golfcourses' => 'array',
            'related_golfcourses.*' => 'exists:golf_courses,id',

            'boards' => 'array',
            'boards.*' => 'exists:boards,id',

            // 'notes' => 'array', 

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



            if ($hotel->country_id != $request->country_id) {
                $hotel->countryfeaturedHotels()->detach();
            }

            if ($hotel->city_id != $request->city_id) {
                $hotel->cityfeaturedHotels()->detach();
            }

            if (($hotel->city_id != $request->city_id) || ($hotel->country_id != $request->country_id)) {
                $hotel->productServiceHotels()->detach();
            }

            $hotel->update($data);

            if (is_array($request->facilities)) {
                $hotel->facilities()->detach();

                foreach ($request->facilities as $facility) {
                    if (isset($facility['id'])) {
                        $hotel->facilities()->attach([$facility['id'] => ['number' => $facility['number']]]);
                    }
                }
            }

            if (is_array($request->boards)) {
                $hotel->boards()->detach();

                $boards = Board::whereIn('id', $request->boards)->get();
                foreach ($boards as $board) {
                    $hotel->boards()->save($board);
                }
            }

            if (is_array($request->tags)) {
                $hotel->tags()->detach();

                $tags = Tag::whereIn('id', $request->tags)->get();
                foreach ($tags as $tag) {
                    $hotel->tags()->save($tag);
                }
            }

            // if(is_array($request->notes))
            // {

            //     $hotel->notes()->forceDelete();
            //     foreach($request->notes as $r_note)
            //     {
            //         $note = new Note;
            //         $note->title = $r_note;

            //         $hotel->notes()->create(['title' => $r_note]);
            //     }
            // }


            if (is_array($request->links_hd_images)) {

                $hotel->linksHDImages()->forceDelete();
                foreach ($request->links_hd_images as $singleLink) {
                    $singleLink = (is_null($singleLink))  ? "" : $singleLink;
                    $hotel->links()->create(['link' => $singleLink, 'type' => 'hd_images']);
                }
            }

            if (is_array($request->links_logo_images)) {

                $hotel->linksLogoImages()->forceDelete();
                foreach ($request->links_logo_images as $singleLink) {
                    $singleLink = (is_null($singleLink))  ? "" : $singleLink;
                    $hotel->links()->create(['link' => $singleLink, 'type' => 'logo_images']);
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

            if (is_array($request->fields)) {
                $hotel->fields()->forceDelete();
                foreach ($request->fields as $fieldData) {
                    $field = $hotel->fields()->create($fieldData);
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

            if (is_array($request->golfcourses)) {
                $hotel->golfcourses()->wherePivot('type', 'owned')->detach();

                $courses = Golfcourse::whereIn('id', $request->golfcourses)->get();
                foreach ($courses as $course) {
                    $hotel->golfcourses()->attach($course->id, ['type' => 'owned']);
                }
            }

            if (is_array($request->related_golfcourses)) {
                $hotel->golfcourses()->wherePivot('type', 'related')->detach();

                $courses = Golfcourse::whereIn('id', $request->related_golfcourses)->get();
                foreach ($courses as $course) {
                    $hotel->golfcourses()->attach($course->id, ['type' => 'related']);
                }
            }

            if (is_array($request->related_hotels)) {
                $hotel->relatedHotels()->detach();
                $hotels = Hotel::whereIn('id', $request->related_hotels)->get();
                foreach ($hotels as $related_hotel) {
                    $hotel->relatedHotels()->attach($related_hotel['id']);
                }
            }

            if (is_array($request->touroperators)) {
                $hotel->touroperators()->detach();
                $opers = TourOperator::whereIn('id', $request->touroperators)->get();
                $hotel->update(['is_golf_globe' => '0']);

                foreach ($opers as $oper) {
                    if ($oper->id == '2000') {
                        $hotel->update(['is_golf_globe' => '1']);
                    }
                    $hotel->touroperators()->save($oper);
                }
            }

            if ($request->translations && is_array($request->translations) && count($request->translations) > 0) {
                $hotel->translations()->forceDelete();
                foreach ($request->translations as $translation) {
                    $language = Language::findOrFail($translation['language_id']);

                    $translateName = (isset($translation['name'])) ? $translation['name'] : null;
                    $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
                    $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

                    $hotel->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName,
                        'website_description' => $translateWebsiteDescription,
                        'internal_description' => $translateInternalDescription,
                    ]);
                }
            }

            if (is_array($request->davinici_codes)) {
                $hotel->DaviniciCodes()->forceDelete();
                foreach ($request->davinici_codes as $davinici_code) {
                    $hotel->DaviniciCodes()->create(['davinici_code' => $davinici_code]);
                }
            }

            DB::commit();

            $hotelData = new HotelDetailsResource(Hotel::find($hotel->id));

            return response()->json([
                'status' => true,
                'hotel' => $hotelData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_publish($id, Request $request)
    {
        $hotel = Hotel::findOrFail($id);

        try {
            DB::beginTransaction();

            $data = [];

            $user = request()->user();

            $data['updated_by'] = $user->id;
            $data['published_at'] = Carbon::now();

            $hotel->update($data);

            DB::commit();

            $hotelData = new HotelDetailsResource(Hotel::find($hotel->id));

            return response()->json([
                'status' => true,
                'hotel' => $hotelData
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

        $hotel = Hotel::findOrFail($id);

        if (is_array($request->deleted_images)) {
            foreach ($hotel->images()->whereIn('id', $request->deleted_images)->get() as $item) {
                $d_image_path = public_path('images/eggheads') . '/' . $item->file_name;
                if (File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $item->delete();
            }
        }

        $hotel = Helpers::uploadItemImages(Hotel::class, $id, $request);


        $hotelData = new HotelDetailsResource(Hotel::find($hotel->id));

        return response()->json([
            'status' => true,
            'hotel' => $hotelData
        ]);
    }


    public function change_main_image(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);

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

        $hotelData = new HotelDetailsResource(Hotel::find($hotel->id));

        return response()->json([
            'status' => true,
            'hotel' => $hotelData
        ]);
    }

    public function delete_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image_id' => 'required|array',
            'image_id.*' => 'required|exists:images,id',
        ]);

        $hotel = Helpers::deleteItemImages(Hotel::class, $id, $request->image_id);

        $hotelData = new HotelDetailsResource(Hotel::find($hotel->id));

        return response()->json([
            'status' => true,
            'hotel' => $hotelData
        ]);
    }

    public function destroy($id, $force = 0)
    {
        $hotel = Hotel::findOrFail($id);

        $has_requests = RequestDestination::whereHotelId($id)->first();

        if ($has_requests && $force==0) {
            return response()->json([
                'status' => true,
                'has_requests' => true,
            ]);
        }

        try {
            DB::beginTransaction();

            $hotel->notes()->delete();

            $hotel->delete();

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

    public function prepare_filter($request)
    {
        $filter = [];
        return $filter;
    }

    public function getFalseResponse()
    {
        return response()->json([
            'status' => false
        ], 422);
    }

    public function create_new_item($data)
    {
        return Hotel::create($data);
    }

    public function import(Request $request)
    {

        $hotels = Excel::toCollection(new HotelImport, public_path('final_GolfHotels.xlsx'));



        $newGC = [];
        $foundGC = [];
        // return $GolfCourses;
        foreach ($hotels[0] as $key => $row) {
            if ($key) {


                $region_id = $this->getTranslabelByDeName('Region', $row[3], 'de');
                $country_id = $this->getTranslabelByDeName('Country', $row[4], 'de');
                $city_id = $this->getTranslabelByDeName('City', $row[5], 'de');
                $area_id = $this->getTranslabelByDeName('Area', $row[6], 'de');

                $companydata = [
                    'name' => $row[1],
                    'email' => "info@golfglobe.com",
                    'region_id' => $region_id,
                    'country_id' => $country_id,
                    'city_id' => $city_id,
                    'area_id' => $area_id,
                    'company_type_id' => 4,
                    'active' => 1,
                ];

                try {
                    // DB::beginTransaction();

                    $company = Company::whereName($row[1])->first();

                    if (!$company) {
                        if (Company::count()) {
                            $nextId = Company::latest()->first()->id + 1;
                        } else {
                            $nextId = 0;
                        }

                        $companydata['booking_code'] = 'CO-' . 'HO' . '-' . $nextId;

                        \DB::select('ALTER TABLE companies AUTO_INCREMENT = 1');

                        $company = Company::create($companydata);
                    }

                    $Hotel_data = [
                        'company_id' => $company->id,
                        'name' => $row[1],
                        'region_id' => $region_id,
                        'country_id' => $country_id,
                        'city_id' => $city_id,
                        'area_id' => $area_id,
                        'company_type_id' => 4,
                        'active' => 1,
                        'ref_id' =>  $row[28],
                        'email' => "info@golfglobe.com",
                        'is_golf_globe' => 1,
                        'letter_code' => $row[8],
                        'location_link' => $row[17],
                        'booking_code' => $this->getAutoBookingCode(),

                        'latitude' => $row[18],
                        'longitude' => $row[19],
                        'hotel_rating' => $row[20],
                        'giata_code' => $row[27]
                    ];

                    $hotel =  Hotel::whereName($row[1])->whereCompanyId($company->id)->first();

                    if ($hotel) {
                        \array_push($foundGC, $hotel->id);
                    } else {
                        \DB::select('ALTER TABLE hotels AUTO_INCREMENT = 1');

                        $hotel = Hotel::create($Hotel_data);

                        \array_push($newGC, $hotel->id);

                        $oper = TourOperator::find(2000);
                        $hotel->touroperators()->save($oper);


                        $davinici_codes = \explode(',', $row[9]);
                        $hotel->DaviniciCodes()->forceDelete();
                        foreach ($davinici_codes as $davinici_code) {
                            if ($davinici_code) {
                                $hotel->DaviniciCodes()->create(['davinici_code' => $davinici_code]);
                            }
                        }

                        $hotel->fields()->forceDelete();
                        if ($row[12]) {
                            $type_id = 10;
                            $field = $hotel->fields()->create(['type_id' => $type_id, 'is_html' => "1", 'description' => $row[12]]);
                            $translateDescription = (isset($row[12])) ? $row[12] : null;
                            $field->translations()->create([
                                'language_id' => 1,
                                'locale' => 'en',
                                'description' => $translateDescription,
                            ]);
                            $field->translations()->create([
                                'language_id' => 2,
                                'locale' => 'de',
                                'description' => $translateDescription,
                            ]);
                        }

                        $services = \explode(',', $row[21]);

                        // ObjectService::whereChildId($hotel->id)->whereType('Hotel')->forceDelete();

                        // ObjectService::get()->forceDelete();
                        foreach ($services as $service) {
                            $service_id = $this->getServiceTranslabelByDeName('Hotel-General', 'Service', trim($service), 'de');
                            if ($service_id) {
                                ObjectService::create([
                                    'child_id' => $hotel->id,
                                    'service_id' => $service_id,
                                    'type' => 'Hotel-General',
                                    'qty' => 1,
                                    'fees' => 1,
                                    'selected_option' => 1,
                                    'notes' => null,
                                    'active' => 1,
                                ]);
                            }
                        }

                        $Sports = \explode(',', $row[22]);

                        // ObjectService::whereChildId($hotel->id)->whereType('Hotel-Sport&Wellness')->forceDelete();
                        foreach ($Sports as $service) {
                            $service_id = $this->getServiceTranslabelByDeName('Hotel-Sport&Wellness', 'Service', trim($service), 'de');
                            if ($service_id) {
                                ObjectService::create([
                                    'child_id' => $hotel->id,
                                    'service_id' => $service_id,
                                    'type' => 'Hotel-Sport&Wellness',
                                    'qty' => 1,
                                    'fees' => 1,
                                    'selected_option' => 1,
                                    'notes' => null,
                                    'active' => 1,
                                ]);
                            }
                        }


                        $Highlights = \explode(',', $row[24]);

                        // ObjectService::whereChildId($hotel->id)->whereType('Hotel-Golf&Highlights')->forceDelete();
                        foreach ($Highlights as $service) {
                            $service_id = $this->getServiceTranslabelByDeName('Hotel-Golf&Highlights', 'Service', trim($service), 'de');
                            if ($service_id) {
                                ObjectService::create([
                                    'child_id' => $hotel->id,
                                    'service_id' => $service_id,
                                    'type' => 'Hotel-Golf&Highlights',
                                    'qty' => 1,
                                    'fees' => 1,
                                    'selected_option' => 1,
                                    'notes' => null,
                                    'active' => 1,
                                ]);
                            }
                        }


                        $boards = \explode(',', $row[25]);
                        $hotel->boards()->forceDelete();
                        foreach ($boards as $board) {
                            if ($board) {
                                $boardob  = Board::whereName($board)->first();
                                if ($boardob) {
                                    $hotel->boards()->attach([$boardob->id]);
                                }
                            }
                        }
                    }
                } catch (\PDOException $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage(),
                    ], 422);
                }
            }


            // DB::commit();


        }
        return ['new_count' => count($newGC), 'exe' => $foundGC, 'new' => $newGC];
    }

    public function getTranslabelByDeName($transTable, $name, $locale = 'en')
    {
        $trans = BasicTranslation::whereName($name)->whereLocale($locale)->whereBasicableType('App\\Models\\' . $transTable)->orderBy('id', 'desc')->first();
        if ($trans) {
            $intent_id = $trans['basicable_id'];
        } else {
            return null;
        }
        return $intent_id;
    }



    public function getServiceTranslabelByDeName($type = '', $transTable, $name, $locale = 'en')
    {
        $trans = Service::whereType($type)->whereHas('translations', function ($q) use ($transTable, $name, $locale) {
            $q->whereName($name)->whereLocale($locale)->whereBasicableType('App\\Models\\' . $transTable);
        })->first();

        if ($trans) {
            return $trans->id;
        } else {
            return null;
        }
    }

    public function hotel_rooms($id) {
        $room = Room::where('hotel_id',$id)->get();
        $roomData = RoomResource::collection($room);
        return $roomData;
    }
}
