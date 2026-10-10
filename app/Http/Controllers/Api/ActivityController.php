<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\ActivityType;
use DB;

class ActivityController extends Controller
{
    public function get_types()
    {
        $types = ActivityType::select(['id', 'name', 'color'])->get();

        return response()->json([
            'status' => true,
            'types' => $types
        ]);
    }

    public function update($id, Request $request)
    {
        $activity = Activity::findOrFail($id);

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

            $data = $request->except('activiteable_id', 'activiteable_type');

            if(is_array($request->days_of_week) && count($request->days_of_week) > 0)
                $data['days_of_week'] = implode(',', $request->days_of_week);

            $activity->update($data);

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

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);

        try {
            DB::beginTransaction();

            $activity->delete();

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
