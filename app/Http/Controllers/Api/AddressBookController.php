<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddressBook;
use App\Http\Resources\AddressBookResource;
use DB;
use Carbon\Carbon;

class AddressBookController extends Controller
{
    public function index()
    {
        $aBook = new AddressBook();
        
        if(request()->company_id)
        {
            $aBook = $aBook->where('company_id', request()->company_id);
        }

        $data = AddressBookResource::collection($aBook->get());

        return response()->json([
            'status' => true,
            'address_books' => $data
        ]);
    }

    public function show($id)
    {
        $aBook = AddressBook::findOrFail($id);

        $addressData = new AddressBookResource($aBook);

        return response()->json([
            'status' => true,
            'address_book' => $addressData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([    
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
    
            'company_id' => 'required|exists:companies,id',
    
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;


            $aBook = AddressBook::create($data);

            DB::commit();

            $addressData = new AddressBookResource($aBook);

            return response()->json([
                'status' => true,
                'address_book' => $addressData
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
        $aBook = AddressBook::findOrFail($id);

        $validated = $request->validate([
    
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
    
            'company_id' => 'required|exists:companies,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;


            $aBook->update($data);

            DB::commit();

            $addressData = new AddressBookResource(AddressBook::find($aBook->id));

            return response()->json([
                'status' => true,
                'address_book' => $addressData
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
        $aBook = AddressBook::findOrFail($id);

        try {
            DB::beginTransaction();

            $aBook->delete();

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
