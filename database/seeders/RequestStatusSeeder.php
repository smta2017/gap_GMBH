<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestStatus;
use App\Models\RequestSubStatus;

class RequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //if_production
        // RequestStatus::truncate(); 
        // RequestSubStatus::truncate(); 

        if(RequestStatus::count() == 0)
        {
            $open = RequestStatus::create([
                'name' => 'Open'
            ]);

            RequestSubStatus::create([
                'name' => 'InComplete',
                'request_status_id' => $open->id
            ]);

            RequestSubStatus::create([
                'name' => 'New',
                'request_status_id' => $open->id
            ]);
            RequestSubStatus::create([
                'name' => 'Delayed',
                'request_status_id' => $open->id
            ]);
            RequestSubStatus::create([
                'name' => 'Approved',
                'request_status_id' => $open->id
            ]);

            $progress = RequestStatus::create([
                'name' => 'In Progress'
            ]);
            RequestSubStatus::create([
                'name' => 'Approved',
                'request_status_id' => $progress->id
            ]);
            RequestSubStatus::create([
                'name' => 'Sys Redirected',
                'request_status_id' => $progress->id
            ]);
            RequestSubStatus::create([
                'name' => 'SP Ack',
                'request_status_id' => $progress->id
            ]);
            RequestSubStatus::create([
                'name' => 'SP Confirmed',
                'request_status_id' => $progress->id
            ]);
            RequestSubStatus::create([
                'name' => 'SP Rejected',
                'request_status_id' => $progress->id
            ]);
            RequestSubStatus::create([
                'name' => 'TA Confirmed',
                'request_status_id' => $progress->id
            ]);
            RequestSubStatus::create([
                'name' => 'GG Confirmed',
                'request_status_id' => $progress->id
            ]);

            $closed = RequestStatus::create([
                'name' => 'Closed'
            ]);
            RequestSubStatus::create([
                'name' => 'TA Canceled',
                'request_status_id' => $closed->id
            ]);
            RequestSubStatus::create([
                'name' => 'GG Canceled',
                'request_status_id' => $closed->id
            ]);
            RequestSubStatus::create([
                'name' => 'Successful',
                'request_status_id' => $closed->id
            ]);
        }

        $ggConfirmedStatus = RequestSubStatus::where('name', 'GG Confirmed')->first();
        if($ggConfirmedStatus)
        {
            $ggConfirmedStatus->update([
                'name' => 'TA Confirmed'
            ]);
        }

        $closedStatus = RequestStatus::where('name', 'Closed')->first();

        if($closedStatus)
        {
            $rejectCheck = RequestSubStatus::where('name', 'Canceled (TA)')->where('request_status_id', $closedStatus->id)->first();

            if(!$rejectCheck)
            {
                RequestSubStatus::create([
                    'name' => 'Canceled (TA)',
                    'request_status_id' => $closedStatus->id
                ]);
            }
        }

        // Remove Approved SubStatus Of Status Open
        $openStatus = RequestStatus::where('name', 'Open')->first();
        
        if($openStatus)
        {
            $approvedDel =  RequestSubStatus::where('name', 'Approved')->where('request_status_id', $openStatus->id)->first();
            if($approvedDel)
            {
                $approvedDel->forceDelete();
            }
        }

        // Change new to submitted
        $newSub = RequestSubStatus::where('name', 'New')->first();
        if($newSub)
        {
            $newSub->update([
                'name' => 'Submitted'
            ]);
        }
        
        $GGCanceled = RequestSubStatus::where('name', 'GG Canceled')->first();
        if($GGCanceled)
        {
            $GGCanceled->update([
                'name' => 'GG Rejected'
            ]);
        }
        //if_production
        // test
        // RequestSubStatus::whereIn('id',[7,3,9,12,10])->update(['status' => 0]);
    }
}
