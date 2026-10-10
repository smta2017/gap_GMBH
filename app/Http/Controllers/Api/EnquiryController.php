<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\Source;
use App\Models\Medium;
use App\Models\Comment;
use App\Models\Integration;
use App\Models\EnquiryStatus;
use App\Http\Resources\EnquiryResource;
use App\Http\Resources\EnquiryCommentResource;
use DB;
use Carbon\Carbon;

class EnquiryController extends Controller
{

    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $enquiry = new Enquiry();
        
        $enquirysData = EnquiryResource::collection($enquiry->get_all($filter));

        return response()->json([
            'status' => true,
            'enquiries' => $enquirysData
        ]);
    }

    public function show($id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $enquiryData = new EnquiryResource($enquiry);

        return response()->json([
            'status' => true,
            'enquiry' => $enquiryData,
        ]);
    }

    public function get_statuses()
    {
        $statuses = EnquiryStatus::select(['id', 'name'])->get();
  
        return response()->json([
            'status' => true,
            'statuses' => $statuses,
        ]);
    }

    public function get_sources()
    {
        $s = Source::select(['id', 'name'])->get();
  
        return response()->json([
            'status' => true,
            'ssources' => $s,
        ]);
    }
    public function get_mediums()
    {
        $m = Medium::select(['id', 'name'])->get();
  
        return response()->json([
            'status' => true,
            'mediums' => $m,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'arrival_date' => 'required',
            'group_number' => 'required',
            'number_of_nights' => 'required|numeric',
            'number_of_rounds' => 'required|numeric',
            
            'flight' => 'required|in:0,1',
            'receive_offer' => 'required|in:0,1',
    
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
    
            // 'city_id' => 'required|exists:cities,id',
    
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $data['user_id'] = $user->id;

            $data['company_id'] = $user->details->company_id;

            $data['status_id'] = 1;

            $data['ip_address'] = request()->ip();

            if($request->schedule_datetime)
            {
                $data['schedule_datetime'] = Carbon::parse($request->schedule_datetime)->format('Y-m-d H:i:s');
            }

            $enquiry = Enquiry::create($data);

            DB::commit();

            $enquiryData = new EnquiryResource($enquiry);

            return response()->json([
                'status' => true,
                'enquiry' => $enquiryData
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
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'arrival_date' => 'required',
            'group_number' => 'required',
            'number_of_nights' => 'required|numeric',
            'number_of_rounds' => 'required|numeric',
            
            'flight' => 'required|in:0,1',
            'receive_offer' => 'required|in:0,1',
    
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
    
            // 'city_id' => 'required|exists:cities,id',
            'status_id' => 'required|exists:enquiry_statuses,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $data['schedule_datetime'] = Carbon::parse($request->schedule_datetime)->format('Y-m-d H:i:s');

            $enquiry->update($data);

            DB::commit();

            $enquiryData = new EnquiryResource(Enquiry::find($enquiry->id));

            return response()->json([
                'status' => true,
                'enquiry' => $enquiryData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_status($id, Request $request)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'status_id' => 'required|exists:enquiry_statuses,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $enquiry->update($data);

            DB::commit();

            $enquiryData = new EnquiryResource(Enquiry::find($enquiry->id));

            return response()->json([
                'status' => true,
                'enquiry' => $enquiryData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_comment($id, Request $request)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'comment' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            $user = request()->user();

            $data['created_by'] = $user->id;
            $data['user_id'] = $user->id;
            $data['commentable_id'] = $enquiry->id;
            $data['commentable_type'] = 'App\Models\Enquiry';

            $comment = Comment::create($data);

            DB::commit();

            $commentData = new EnquiryCommentResource($comment);

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

    public function prepare_filter($request)
    {
        $filter = [];

        return $filter;
    }

}
