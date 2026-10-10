<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\DocumentType;
use App\Http\Resources\CompanyDocumentResource;
use DB;
use File;

class CompanyDocumentController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $company_id = request()->company_id;

        if($company_id)
        {
            $company = Company::where('id', $company_id)->first();
        }else{
            $company = Company::where('id', $user->details->company_id)->first();
        }

        if(!$company)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        $documents = new CompanyDocument();

        $documentsData = CompanyDocumentResource::collection($company->documents);

        return response()->json([
            'status' => true,
            'documents' => $documentsData->response()->getData()
        ]);
    }

    public function get_types()
    {
        $types = DocumentType::where('status', '1')->select(['id', 'name'])->get();

        return response()->json([
            'status' => true,
            'types' => $types
        ]);
    }

    public function store(Request $request)
    {
        $user = request()->user();

        // $company = Company::where('id', $user->details->company_id)->first();

        // if(!$company)
        // {
        //     return response()->json([
        //         'status' => false
        //     ], 404);
        // }

        $validated = $request->validate([
            'title' => 'required',
            'company_id' => "exists:companies,id",
            "document_type_id" => 'required|exists:document_types,id',
            'expire_date' => 'required|date_format:Y-m-d',
            'is_notify' => 'required|in:0,1',
            'file' => 'required|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $company = Company::where('id', $request->company_id)->first();

        if(!$company)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        try {
            DB::beginTransaction();

            $data = $request->except('file');

            

            $data['created_by'] = $user->id;
            $data['company_id'] = $company->id;

            if ($request->hasFile('file')) {
            
                $extention = $request->file->extension();

                $fileName = \Str::random(6) . time() . '.' . $extention;  
                
                $request->file->move(public_path('images/companies'), $fileName);

                $data['file_type'] = $extention;
                $data['file_name'] = $fileName;
                
            }

            $document = CompanyDocument::create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'document' => new CompanyDocumentResource(CompanyDocument::find($document->id))
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
        $document = CompanyDocument::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required',
            "document_type_id" => 'required|exists:document_types,id',
            'expire_date' => 'required|date_format:Y-m-d',
            'is_notify' => 'required|in:0,1',
            'file' => 'mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except('file');

            $user = request()->user();

            $data['updated_by'] = $user->id;

            if ($request->hasFile('file')) {
            
                // remove old file
                $d_file_path = public_path('images/companies') . '/' . $document->file_name;
                if(File::exists($d_file_path)) {
                    File::delete($d_file_path);
                }

                $extention = $request->file->extension();

                $fileName = \Str::random(6) . time() . '.' . $extention;  
                
                $request->file->move(public_path('images/companies'), $fileName);

                $data['file_type'] = $extention;
                $data['file_name'] = $fileName;
                
            }

            $document->update($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'document' => new CompanyDocumentResource(CompanyDocument::find($document->id))
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
        $document = CompanyDocument::findOrFail($id);

        try {
            DB::beginTransaction();

            $document->delete();;

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
