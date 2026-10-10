<?php

namespace App\Http\Controllers\Api;

use App\Helper\DaVinciHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackagesServicesResource;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Hole;
use App\Models\Hotel;
use App\Models\Tag;
use App\Models\TeeTime;
use App\Http\Resources\ProductResource;
use App\Models\BewotecDavinciService;
use App\Models\BewotecDavinciServiceType;
use App\Models\Package;
use DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Queue;

class ProductController extends Controller
{
    public function index()
    {
        // $filter = $this->prepare_filter(request());

        $product = new Product();

        $productsData = ProductResource::collection($product->get_pagination());

        return response()->json([
            'status' => true,
            'products' => $productsData->response()->getData()
        ]);
    }

    public function get_all()
    {
        // $filter = $this->prepare_filter(request());

        $product = new Product();

        $productsData = ProductResource::collection($product->get_all());

        return response()->json([
            'status' => true,
            'products' => $productsData
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        $productData = new ProductResource($product);

        return response()->json([
            'status' => true,
            'product' => $productData,
        ]);
    }

    public function get_holes()
    {
        $holes = Hole::select(['id', 'name'])->get();

        return response()->json([
            'status' => true,
            'holes' => $holes,
        ]);
    }

    public function get_tee_times()
    {
        $times = TeeTime::select(['id', 'name'])->get();

        return response()->json([
            'status' => true,
            'tee_times' => $times,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',

            'service_id' => 'required|exists:product_services,id',
            'golf_course_id' => 'required_unless:is_package,1|exists:golf_courses,id',

            'tee_time_id' => 'required|exists:tee_times,id',
            'hole_id' => 'required_unless:is_package,1|exists:holes,id',

            // 'code' => 'unique:products,code',

            'validity_from' => 'date:y-m-d',
            'validity_to' => 'date:y-m-d',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            'tags' => 'array',
            'tags.*' => 'exists:tags,id',

            'status' => 'required|in:1,0',

        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $product = Product::create($data);

            if (is_array($request->hotels) && count($request->hotels) > 0) {
                $hotels = Hotel::whereIn('id', $request->hotels)->get();
                foreach ($hotels as $hotel) {
                    $product->hotels()->save($hotel);
                }
            }

            if (!$request->code) {

                $code = $product->service->code . $product->id;

                $product->update([
                    'code' => $code
                ]);
            }

            if (is_array($request->tags) && count($request->tags) > 0) {
                $tags = Tag::whereIn('id', $request->tags)->get();
                foreach ($tags as $tag) {
                    $product->tags()->save($tag);
                }
            }

            if ($request->is_package == '1') {
                $productDetails = $request->details;

                if ($productDetails && is_array($productDetails)) {
                    foreach ($productDetails as $detail) {
                        $data = [
                            'product_id' => $product->id,
                            'golf_course_id' => $detail['golf_course_id'],
                            'type' => $detail['type'],
                        ];

                        if (isset($detail['tee_time_id'])) {
                            $data['tee_time_id'] = $detail['tee_time_id'];
                        }
                        if (isset($detail['min_tee_time_id'])) {
                            $data['min_tee_time_id'] = $detail['min_tee_time_id'];
                        }
                        if (isset($detail['max_tee_time_id'])) {
                            $data['max_tee_time_id'] = $detail['max_tee_time_id'];
                        }
                        ProductDetails::create($data);
                    }
                }
            }

            DB::commit();

            $productData = new ProductResource($product);

            return response()->json([
                'status' => true,
                'product' => $productData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function bulk_store(Request $request)
    {
        $validated = $request->validate([

            'products' => 'required|array|min:1',

            'products.*.name' => 'required',

            'products.*.service_id' => 'required|exists:product_services,id',
            'products.*.golf_course_id' => 'required|exists:golf_courses,id',

            'products.*.tee_time_id' => 'required|exists:tee_times,id',
            'products.*.hole_id' => 'required|exists:holes,id',

            // 'products.*.code' => 'unique:products,code',

            'products.*.validity_from' => 'date:y-m-d',
            'products.*.validity_to' => 'date:y-m-d',

            'products.*.hotels' => 'array',
            'products.*.hotels.*' => 'exists:hotels,id',

            'products.*.status' => 'required|in:1,0',

        ]);

        try {
            DB::beginTransaction();

            $user = request()->user();
            foreach ($request->products as $product) {
                $data = $request->all();
                $product['created_by'] = $user->id;

                if (isset($product['code'])) {
                    $code = $product['code'];
                } else {
                    $code = null;
                }

                $info = (isset($product['additional_information'])) ? $product['additional_information'] : null;

                $productData = Product::create([
                    'name' => $product['name'],
                    'service_id' => $product['service_id'],
                    'golf_course_id' => $product['golf_course_id'],

                    'code' => $code,

                    'tee_time_id' => $product['tee_time_id'],
                    'hole_id' => $product['hole_id'],

                    'validity_from' => $product['validity_from'],
                    'validity_to' => $product['validity_to'],

                    'additional_information' => $info,

                    'status' => $product['status'],

                    'created_by' => $user->id
                ]);

                if (isset($product['hotels']) && is_array($product['hotels']) && count($product['hotels']) > 0) {
                    $hotels = Hotel::whereIn('id', $product['hotels'])->get();
                    foreach ($hotels as $hotel) {
                        $productData->hotels()->save($hotel);
                    }
                }

                if (!isset($product['code'])) {

                    $productCode = $productData->service->code . $productData->id;

                    $productData->update([
                        'code' => $code
                    ]);
                }
            }

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
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',

            'service_id' => 'required|exists:product_services,id',

            'golf_course_id' => 'required_unless:is_package,1|exists:golf_courses,id',

            'tee_time_id' => 'required|exists:tee_times,id',
            'hole_id' => 'required_unless:is_package,1|exists:holes,id',

            // 'code' => 'unique:products,code,' . $id,

            'validity_from' => 'date:y-m-d',
            'validity_to' => 'date:y-m-d',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            'tags' => 'array',
            'tags.*' => 'exists:tags,id',

            'status' => 'required|in:1,0',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['updated_by'] = $user->id;

            $product->update($data);


            if (is_array($request->hotels) && count($request->hotels) > 0) {
                $product->hotels()->detach();

                $hotels = Hotel::whereIn('id', $request->hotels)->get();
                foreach ($hotels as $hotel) {
                    $product->hotels()->save($hotel);
                }
            }


            if (is_array($request->tags)) {
                $product->tags()->detach();

                $tags = Tag::whereIn('id', $request->tags)->get();
                foreach ($tags as $tag) {
                    $product->tags()->save($tag);
                }
            }

            if ($product->is_package == '1') {
                $productDetails = $request->details;

                if ($productDetails && is_array($productDetails)) {

                    ProductDetails::where('product_id', $product->id)->forceDelete();

                    foreach ($productDetails as $detail) {
                        $data = [
                            'product_id' => $product->id,
                            'golf_course_id' => $detail['golf_course_id'],
                            'type' => $detail['type'],
                        ];

                        if (isset($detail['tee_time_id'])) {
                            $data['tee_time_id'] = $detail['tee_time_id'];
                        }
                        if (isset($detail['min_tee_time_id'])) {
                            $data['min_tee_time_id'] = $detail['min_tee_time_id'];
                        }
                        if (isset($detail['max_tee_time_id'])) {
                            $data['max_tee_time_id'] = $detail['max_tee_time_id'];
                        }

                        ProductDetails::create($data);
                    }
                }
            }

            DB::commit();

            $productData = new ProductResource(Product::find($product->id));

            return response()->json([
                'status' => true,
                'product' => $productData
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
        $product = Product::findOrFail($id);

        try {
            DB::beginTransaction();

            $product->delete();

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
        // if($request->search)
        // {
        //     array_push($filter, array('name', 'LIKE', '%' . $request->search . '%'));
        // }

        // if($request->service_id)
        // {
        //     array_push($filter, array('service', $request->service_id ));
        // }

        // if($request->city_id)
        // {
        //     $servicesData = ProductService::where('city_id', $request->city_id)->get();

        //     array_push($filter, array('service', $request->service_id ));
        // }

        return $filter;
    }

    public function getPackages(Request $request)
    {
        // From, To, Duration (from/to), Adult
        $result =  BewotecDavinciService::whereRequirement("P")->with([
            'Children' => function ($query) use ($request) {
                $query->with([
                    'ServiceTypes' => function ($query) use ($request) {
                        $query->orderBy('price', 'asc');
                        if ($request->adult) {
                            $query->where('adults', $request->adult);
                        }
                    }
                ]);
            }
        ]);

        if ($request->adult) {
            $result->where(function ($query) use ($request) {
                $query->orWhereHas('Children.ServiceTypes', function ($q2) use ($request) {
                    $q2->where('adults', $request->adult);
                });
            });
        }

        if ($request->booking_code) {
            $result->where(function ($query) use ($request) {
                $query->where('booking_code', $request->booking_code);
                if ($request->packages_only != 0) {
                    $query->orWhereHas('Children', function ($q2) use ($request) {
                        $q2->where('booking_code', $request->booking_code);
                    });
                }
            });
        }


        if ($request->date_from) {
            $result->where(function ($query) use ($request) {
                $query->where('date_from', $request->date_from);
                if ($request->packages_only != 0) {
                    $query->where(function ($query) use ($request) {
                        $query->orWhereHas('Children', function ($q2) use ($request) {
                            $q2->where('date_from', $request->date_from);
                        });
                    });
                } else {
                }
            });
        }

        if ($request->date_to) {
            $result->where(function ($query) use ($request) {
                $query->where('date_to', $request->date_to);
                if ($request->packages_only != 0) {
                    $query->where(function ($query) use ($request) {
                        $query->orWhereHas('Children', function ($q2) use ($request) {
                            $q2->where('date_to', $request->date_to);
                        });
                    });
                }
            });
        }

        if ($request->duration) {
            $result->where(function ($query) use ($request) {
                $query->where('duration', $request->duration);
                if ($request->packages_only != 0) {
                    $query->where(function ($query) use ($request) {
                        $query->orWhereHas('Children', function ($q2) use ($request) {
                            $q2->where('duration', $request->duration);
                        });
                    });
                }
            });
        }

        $services = $result->get();
        return PackagesServicesResource::collection($services);
    }


    public function getPackageOffers($id, Request $request)
    {
        $package = Package::findOrFail($id);
        $davinci_booking_code = $package->davinci_booking_code;

        // From, To, Duration (from/to), Adult
        $result =  BewotecDavinciService::whereRequirement("P")->with([
            'Children' => function ($query) use ($request) {
                $query->with([
                    'ServiceTypes' => function ($query) use ($request) {
                        $query->orderBy('price', 'asc');
                        if ($request->adult) {
                            $query->where('adults', $request->adult);
                        }
                    }
                ]);
            }
        ]);

        $result->where(function ($query) use ($davinci_booking_code) {
            $query->where('booking_code', $davinci_booking_code);
            $query->orWhereHas('Children', function ($q2) use ($davinci_booking_code) {
                $q2->where('booking_code', $davinci_booking_code);
            });
        });

        if ($request->date_from) {
            $result->where(function ($query) use ($request) {
                $query->where('date_from', $request->date_from);
                if ($request->packages_only != 0) {
                    $query->orWhereHas('Children', function ($q2) use ($request) {
                        $q2->where('date_from', $request->date_from);
                    });
                }
            });
        }

        if ($request->date_to) {
            $result->where(function ($query) use ($request) {
                $query->where('date_to', $request->date_to);
                if ($request->packages_only != 0) {
                    $query->orWhereHas('Children', function ($q2) use ($request) {
                        $q2->where('date_to', $request->date_to);
                    });
                }
            });
        }

        if ($request->duration) {
            $result->where(function ($query) use ($request) {
                $query->where('duration', $request->duration);
                if ($request->packages_only != 0) {
                    $query->orWhereHas('Children', function ($q2) use ($request) {
                        $q2->where('duration', $request->duration);
                    });
                }
            });
        }

        $services = $result->get();
        return PackagesServicesResource::collection($services);
    }


    public function testGetDvinciCodes()
    {
        $davinciPPS = new \golfglobe\BewotecApi\DavinciPPSRest();

        $nowDate = Carbon::now();
        $currentDate = $nowDate->format('Y-m-d');
        $nextYearDate = $nowDate->addYear()->format('Y-m-d');

        $data = $davinciPPS->GetOverviewOfValidPackages($currentDate, $nextYearDate);
        $jsonData = json_decode($data, true);
        $codes =   array_column($jsonData, 'Code');

        return $codes;
    }

    public function testPricesAndAvailabilitiesDvinci(Request $request)
    {
        $davinciPPS = new \golfglobe\BewotecApi\DavinciPPSRest();

        $response = $davinciPPS->getPackagePricesAndAvailabilitiesV2($request['bCode'], [7, 14], '2023-08-01', '2023-08-14', 2);

        return $response;
    }



    public function testImportDavinci()
    {
        $data = file_get_contents(env("DAVINCI_STATIC_FILE_PATH"));
        $responce = (new DaVinciHelper())->importDaVinciPackages($data);
        return $responce;
    }

    public function importDaVinciPackages()
    {
        $davinciPPS = new \golfglobe\BewotecApi\DavinciPPSRest();
        //
        $queueSize = Queue::size('davinci_auto_import');
        if ($queueSize == 0) {

            if (env("APP_ENV") != 'local') {
                $davinciPPS->clearCache();
                $davinciPPS->updateSearchData();
            }
            $daVinciHelper = (new DaVinciHelper())->availablePackages();
        }
    }

    public function cleanDaVinciPackages()
    {
        return DaVinciHelper::cleanDaVinciPackages();
    }

    public function manualCustomImport(Request $request)
    {
        $request['adult'] =  (int) $request->adult;
        $request['duration_request'] = [(int) $request->duration_request];
        // return $request->all();
        return (new DaVinciHelper())->getDavinciSpecificPackage($request->all());
    }


    public function manualImportDaVinciPackages(Request $request)
    {
        $davinciPPS = new \golfglobe\BewotecApi\DavinciPPSRest();

        $queueSize = Queue::size('davinci_manual_import');

        if ($queueSize == 0 || (isset($request->booking_code) && $request->booking_code != "")) {
            if (env("APP_ENV") != 'local') {
                $davinciPPS->clearCache();
                $davinciPPS->updateSearchData();
            }

            $allCount = (new DaVinciHelper())->availablePackages($request);

            return response()->json([
                'status' => true,
                'jobs_count' => $allCount
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => "There are jobs pending"
        ]);
    }

    public function manualCleanDaVinciPackages()
    {
        DaVinciHelper::cleanDaVinciPackages();
        return response()->json([
            'status' => true,
            'message' => 'Clean-Up has ben sent...'
        ]);
    }

    public function deleteDaVinciPackage(Request $request)
    {
        try {
            (new DaVinciHelper())->deletePackage($request);

            return response()->json([
                'status' => \true,
                'message' => "Package(s) Deleted!"
            ]);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function getImportInfo()
    {
        $info = collect(\DB::select('select count(id) as jobs from jobs union select count(id) as service from bewotec_davinci_services union select min(updated_at) as max_service from bewotec_davinci_services ;'))->pluck('jobs')->toArray();

        $SQL = "select count(id) as id FROM bewotec_davinci_services WHERE 
                (
                    (requirement IN ('H', 'SO', 'T') AND NOT EXISTS (
                        SELECT 1 FROM bewotec_davinci_service_types WHERE bewotec_davinci_services.id = bewotec_davinci_service_types.service_id
                    ))
                    OR
                    (requirement = 'P' AND id NOT IN (
                        SELECT package_service_id FROM bewotec_davinci_services AS children WHERE bewotec_davinci_services.id = children.package_service_id
                    ))
                );
        ";

        $ids = collect(\DB::select($SQL))->pluck('id')->toArray();
        $info[] = $ids[0];
        $info[] = date("Y-m-d H:i:s");

        return response()->json([
            'status' => true,
            'info' => $info
        ]);
    }
}
