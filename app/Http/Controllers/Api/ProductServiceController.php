<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductService;
use App\Models\Season;
use App\Models\Company;
use App\Models\PriceList;
use App\Models\Price;
use App\Models\City;
use App\Models\Hotel;
use App\Http\Resources\ProductServiceResource;
use App\Http\Resources\SeasonResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PriceListResource;
use App\Models\Product;
use DB;
use Carbon\Carbon;

class ProductServiceController extends Controller
{
    public function index()
    {      
        $services = new ProductService();
        
        $servicesData = ProductServiceResource::collection($services->get_pagination());

        return response()->json([
            'status' => true,
            'services' => $servicesData->response()->getData()
        ]);
    }

    public function get_all()
    {
        $services = new ProductService();

        return response()->json([
            'status' => true,
            'services' => ProductServiceResource::collection($services->get_all())
        ]);
    }

    public function show($id)
    {
        $service = ProductService::findOrFail($id);

        $serviceData = new ProductServiceResource($service);

        return response()->json([
            'status' => true,
            'service' => $serviceData,
        ]);
    }

    public function get_seasons($id)
    {
        $service = ProductService::findOrFail($id);

        $seasons = SeasonResource::collection($service->seasons);

        return response()->json([
            'status' => true,
            'seasons' => $seasons
        ]);
    }

    public function get_prices($id)
    {  
        $service = ProductService::find($id);
        
        $products = ProductResource::collection($service->products);
        $priceLists = PriceListResource::collection($service->lists);

        $seasons = Season::where('service_id', $service->id)->select(['id', 'title', 'code'])->get();
 
        foreach($seasons as $key => $season)
        {
            $lists = PriceList::where('service_id', $service->id)->select(['id', 'name'])->get();

            foreach($lists as $list)
            {
                $prices = Price::where('service_id', $service->id)
                            ->where('season_id', $season->id)
                            ->where('price_list_id', $list->id)
                            ->select([
                                'id',
                                'product_id',
                                'price'
                            ])->get();
                $list->prices = $prices;
            }

            $season->lists = $lists;
        };

        return response()->json([
            'status' => true,
            'products' => $products,
            'lists' => $priceLists,
            'seasons' => $seasons
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            // 'type' => 'required|in:Golf,Hotel,Golf Hotel',
    
            'company_id' => 'required|exists:companies,id',

            // 'provider_id' => 'required',
    
            'country_id' => 'exists:countries,id',
            'city_id' => 'exists:cities,id',
    
            // 'letter_code' => 'required',
    
            // 'invoice_handler_id' => 'required',
    
            // 'service_handler_type_id' => 'required',
            // 'service_handler_id' => 'required',
    
            'cities' => 'required|array|min:1',
            'cities.*' => 'exists:cities,id',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            'active' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
           
            $user = request()->user();

            $data['created_by'] = $user->id;

            $companyData = Company::find($request->company_id);

            $data['company_type_id'] = $companyData->company_type_id;

            if($request->validity_from)
            {
                $data['validity_from'] = Carbon::parse($request->validity_from)->format('Y-m-d');
            }

            if($request->validity_to)
            {
                $data['validity_to'] = Carbon::parse($request->validity_to)->format('Y-m-d');
            }

            $service = ProductService::create($data);

            if(is_array($request->cities) && count($request->cities) > 0)
            {
                $cities = City::whereIn('id', $request->cities)->get();
                foreach($cities as $city)
                {
                    $service->cities()->save($city);
                }
            }

            if(is_array($request->hotels) && count($request->hotels) > 0)
            {
                $hotels = Hotel::whereIn('id', $request->hotels)->get();
                foreach($hotels as $hotel)
                {
                    $service->hotels()->save($hotel);
                }
            }

            $code = '';
            if($service->provider())
            {
                $numberCheck = (ProductService::where('company_id', $service->provider()->company_id)->count()) + 1;

                $code = $service->provider()->booking_code . $numberCheck;
        
            }else{
                if($service->company)
                {
                    $numberCheck = ( ProductService::where('company_id', $service->company->id)->count() ) + 1;

                    $code = $service->company->booking_code . $numberCheck;
                }
            }
            
            $service->update([
                'code' => $code
            ]);

            DB::commit();

            $serviceData = new ProductServiceResource($service);

            return response()->json([
                'status' => true,
                'service' => $serviceData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_season($id, Request $request)
    {
        $service = ProductService::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string',
            'code' => 'string',
            'start_date' => 'string',
            'end_date' => 'string',
            'color' => 'string',
            'display' => 'string',
            'peak_time_from' => 'string',
            'peak_time_to' => 'string',
        ]);

        try {
            DB::beginTransaction();

            $service->seasons()->create($request->all());

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
        $service = ProductService::findOrFail($id);

        $products = Product::whereServiceId($id)->get();

        $validated = $request->validate([
            'name' => 'required',
            // 'type' => 'required|in:Golf,Hotel,Golf Hotel',
    
            'company_id' => 'required|exists:companies,id',

            // 'provider_id' => 'required',
    
            'country_id' => 'exists:countries,id',
            'city_id' => 'exists:cities,id',
            
            // 'code' => 'unique:product_services,code,'.$service->id,

            // 'letter_code' => 'required',

            // 'invoice_handler_id' => 'required',
    
            'cities' => 'required|array|min:1',
            'cities.*' => 'exists:cities,id',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            // 'service_handler_type_id' => 'required',
            // 'service_handler_id' => 'required',
    
            'active' => 'required',
        ]);

        try {
            DB::beginTransaction();
            
            $product_data = $request->only(['service_handler_type_id','booking_possible_for','hotels','service_handler_id']);
            
            $data = $request->all();

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $companyData = Company::find($request->company_id);

            $data['company_type_id'] = $companyData->company_type_id;

            if($request->validity_from)
            {
                $data['validity_from'] = Carbon::parse($request->validity_from)->format('Y-m-d');
            }

            if($request->validity_to)
            {
                $data['validity_to'] = Carbon::parse($request->validity_to)->format('Y-m-d');
            }

            $service->update($data);

            if(is_array($request->cities) && count($request->cities) > 0)
            {
                $service->cities()->detach();
                $cities = City::whereIn('id', $request->cities)->get();
                foreach($cities as $city)
                {
                    $service->cities()->save($city);
                }
            }

            if(is_array($request->hotels) )
            {
                $service->hotels()->detach();
                $hotels = Hotel::whereIn('id', $request->hotels)->get();
                foreach($hotels as $hotel)
                {
                    $service->hotels()->save($hotel);
                }
            }

            foreach($products as $product){
                if($product->use_service_configurations){
                    $hotels = Hotel::whereIn('id', $product_data['hotels'])->get();
                    if(is_array($product_data['hotels']) && count($product_data['hotels']) > 0)
                    {
                        $product->hotels()->detach();
                        foreach($hotels as $hotel)
                        {
                            $product->hotels()->save($hotel);
                        }
                    }
                    $product->update($product_data);
                }
            }


            DB::commit();

            $serviceData = new ProductServiceResource(ProductService::find($service->id));

            return response()->json([
                'status' => true,
                'service' => $serviceData
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
        $service = ProductService::findOrFail($id);

        try {

            DB::beginTransaction();

            $service->delete();

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
}
