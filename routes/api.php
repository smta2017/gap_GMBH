<?php

use App\Helper\Helpers;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AddressBookController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyDocumentController;
use App\Http\Controllers\Api\CompanyTypeController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\DmcController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EnquiryController;
use App\Http\Controllers\Api\ForgetPasswordController;
use App\Http\Controllers\Api\GolfCourseController;
use App\Http\Controllers\Api\GolfHolidayController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HotelProductController;
use App\Http\Controllers\Api\IntegrationController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PriceController;
use App\Http\Controllers\Api\PriceListController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductServiceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\RequestProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SeasonController;
use App\Http\Controllers\Api\ServicesController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TestimonyController;
use App\Http\Controllers\Api\TourOperatorController;
use App\Http\Controllers\Api\TravelAgencyController;
use App\Http\Controllers\Api\UserController;
use golfglobe\BewotecApi\DavinciPPSRest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.', 'middleware' => 'cors', 'namespace' => 'Api'], function(){


    Route::get('reload-statistics', [StatisticsController::class,'reload_statistics'])->name('reload.statistics');
    Route::get('close-requests', [RequestController::class,'close_requests'])->name('close.requests');

    Route::get('locales', [LocaleController::class,'index'])->name('locales.index');
    Route::get('locale/{locale}', [LocaleController::class,'set_locale'])->name('set.locale');

    Route::post('login', [LoginController::class,'login'])->name('login');
    Route::post('register', [RegisterController::class,'register'])->name('register');

    Route::post('direct-login', [LoginController::class,'direct_login'])->name('direct.login');
    Route::post('token-login', [LoginController::class,'token_login'])->name('token.login');

    Route::post('refresh-token', [LoginController::class,'refresh_token'])->name('refresh.token');

    
    Route::group(['prefix' => 'forget-password', 'as' => 'forget.password.'], function(){
        Route::post('', [ForgetPasswordController::class,'forget_password'])->name('index');
        Route::post('store', [ForgetPasswordController::class,'forget_password_store'])->name('store');
    });

    Route::get('voucher/{code}', [RequestController::class,'get_voucher'])->name('get.voucher');

    Route::group(['middleware' => ['auth:sanctum', 'validate.token']], function(){

        Route::post('logout', [LoginController::class,'logout'])->name('logout');

        Route::group(['prefix' => 'user', 'as' => 'user.'], function(){
            Route::get('', [ProfileController::class,'index'])->name('profile');
            Route::put('update-profile', [ProfileController::class,'update_profile'])->name('update.profile');
            Route::post('update-image', [ProfileController::class,'update_image'])->name('update.image');
            Route::post('remove-image', [ProfileController::class,'remove_image'])->name('remove.image');
            Route::post('reset-password', [ProfileController::class,'reset_password'])->name('reset.password');
            Route::post('check-email', [ProfileController::class,'check_email'])->name('check.email');
            Route::get('company/profile' , [ProfileController::class,'get_company_profile'])->name('company.profile');
            Route::group(['prefix' => 'company', 'as' => 'company.'], function(){
                Route::get('' , [ProfileController::class,'get_company_profile'])->name('profile');
                Route::put('update-profile' , [ProfileController::class,'update_company_profile'])->name('update.profile');
                Route::post('update-logo' , [ProfileController::class,'update_company_logo'])->name('update.logo');
                Route::post('remove-logo' , [ProfileController::class,'remove_company_logo'])->name('remove.logo');
            });
        });

        // DaVinci Routs
        Route::get('import-davinci-packages' , [ProductController::class,"importDaVinciPackages"]);
        Route::get('davinci-clean-up' , [ProductController::class,"cleanDaVinciPackages"]);
        Route::delete('davinci-delete-package' , [ProductController::class,"deleteDaVinciPackage"]);


        Route::group(['prefix' => 'regions', 'as' => 'regions.'], function(){
            Route::get('check-region-updated', [RegionController::class,'checkRegionUpdated'])->name('check-region-updated');
            Route::get('import', [RegionController::class,'import'])->name('import'); 
            Route::get('', [RegionController::class,'index'])->name('index');
            Route::get('list', [RegionController::class,'newIndex'])->name('newIndex');
            Route::get('list-table', [RegionController::class,'tableIndex'])->name('tableIndex');
            Route::get('/get/info', [RegionController::class,'index_info'])->name('index.info');
            Route::get('{id}', [RegionController::class,'show'])->name('show');
            Route::post('', [RegionController::class,'store'])->name('store');
            Route::put('{id}', [RegionController::class,'update'])->name('update');
            Route::delete('{id}', [RegionController::class,'destroy'])->name('destroy');
        });
        Route::group(['prefix' => 'countries', 'as' => 'countries.'], function(){
            Route::get('', [CountryController::class,'index'])->name('index');
            Route::get('list', [CountryController::class,'newIndex'])->name('newIndex');
            Route::get('list-table', [CountryController::class,'tableIndex'])->name('tableIndex');
            Route::get('{id}', [CountryController::class,'show'])->name('show');

            Route::get('field-types/{id}', [CountryController::class,'get_field_types'])->name('field.types.all');
            Route::get('currencies/{id}', [CountryController::class,'get_currencies'])->name('currencies.all');

            Route::post('', [CountryController::class,'store'])->name('store');

            Route::post('testimonies/{id}', [CountryController::class,'store_testimonies'])->name('store.testimonies');
            Route::post('testimonies/{id}/bulk', [CountryController::class,'store_testimonies_bulk'])->name('store.testimonies.bulk');
            Route::post('upload-images/{id}', [CountryController::class,'upload_images'])->name('upload.images');
            Route::post('delete-image/{id}', [CountryController::class,'delete_image'])->name('delete.image');

            Route::put('{id}', [CountryController::class,'update'])->name('update');
            Route::put('publish/{id}', [CountryController::class,'update_publish'])->name('update.publish');
            Route::put('{id}/change-main-image', [CountryController::class,'change_main_image'])->name('change.main.image');

            Route::delete('{id}', [CountryController::class,'destroy'])->name('destroy');
        });
        Route::group(['prefix' => 'cities', 'as' => 'cities.'], function(){
            Route::get('', [CityController::class,'index'])->name('index');
            Route::get('paginate', [CityController::class,'index_paginate'])->name('index.paginate');

            Route::get('list', [CityController::class,'newIndex'])->name('newIndex');
            Route::get('list-table', [CityController::class,'tableIndex'])->name('tableIndex');
            Route::get('{id}', [CityController::class,'show'])->name('show');

            Route::get('field-types/{id}', [CityController::class,'get_field_types'])->name('field.types.all');

            Route::post('', [CityController::class,'store'])->name('store');

            Route::post('testimonies/{id}', [CityController::class,'store_testimonies'])->name('store.testimonies');
            Route::post('testimonies/{id}/bulk', [CityController::class,'store_testimonies_bulk'])->name('store.testimonies.bulk');
            Route::post('upload-images/{id}', [CityController::class,'upload_images'])->name('upload.images');
            Route::post('delete-image/{id}', [CityController::class,'delete_image'])->name('delete.image');

            Route::put('{id}', [CityController::class,'update'])->name('update');
            Route::put('publish/{id}', [CityController::class,'update_publish'])->name('update.publish');

            Route::put('{id}/change-main-image', [CityController::class,'change_main_image'])->name('change.main.image');

            Route::delete('{id}', [CityController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'areas', 'as' => 'areas.'], function(){
            Route::get('', [AreaController::class,'index'])->name('index');
            Route::get('list', [AreaController::class,'newIndex'])->name('newIndex');
            Route::get('list-table', [AreaController::class,'tableIndex'])->name('tableIndex');
            Route::get('{id}', [AreaController::class,'show'])->name('show');

            Route::post('', [AreaController::class,'store'])->name('store');


            Route::put('{id}', [AreaController::class,'update'])->name('update');

            Route::delete('{id}', [AreaController::class,'destroy'])->name('destroy');
        });
        
        Route::group(['prefix' => 'testimonies', 'as' => 'testimonies.'], function(){

            Route::put('{id}', [TestimonyController::class,'update'])->name('update');
            
            Route::post('upload-image/{id}', [TestimonyController::class,'upload_image'])->name('upload.image');
            Route::post('delete-image/{id}', [TestimonyController::class,'delete_image'])->name('delete.image');

            Route::delete('{id}', [TestimonyController::class,'destroy'])->name('destroy');
        });
        Route::group(['prefix' => 'comments', 'as' => 'comments.'], function(){
            Route::put('{id}', [CommentController::class,'update'])->name('update');
            Route::delete('{id}', [CommentController::class,'destroy'])->name('destroy');
        });
        Route::group(['prefix' => 'documents', 'as' => 'documents.'], function(){
            Route::delete('{id}', [DocumentController::class,'destroy'])->name('destroy');
        });
        Route::group(['prefix' => 'company-types', 'as' => 'company.types.'], function(){
            Route::get('', [CompanyTypeController::class,'index'])->name('index');
        });

        Route::group(['prefix' => 'companies', 'as' => 'companies.'], function(){
            Route::get('', [CompanyController::class,'index'])->name('index')->name('index');
            Route::get('{id}', [CompanyController::class,'show'])->name('show')->name('show');
            Route::get('get/users', [CompanyController::class,'get_all_users'])->name('users.index');
            Route::get('get/childs', [CompanyController::class,'get_childs'])->name('childs.index');
            Route::get('get/booking-codes', [CompanyController::class,'get_booking_codes'])->name('booking.codes.index');
            Route::get('get/providers', [CompanyController::class,'get_providers'])->name('providers.index');
            Route::get('document/types', [CompanyDocumentController::class,'get_types'])->name('types.all');
            Route::post('', [CompanyController::class,'store'])->name('store')->name('store');
            Route::put('{id}', [CompanyController::class,'update'])->name('update');
            Route::post('update-logo/{id}' , [CompanyController::class,'update_company_logo'])->name('update.logo');
            Route::post('remove-logo/{id}' , [CompanyController::class,'remove_company_logo'])->name('remove.logo');
            Route::delete('{id}', [CompanyController::class,'destroy'])->name('destroy')->name('destroy');
            Route::delete('bulk/delete', [CompanyController::class,'bulk_destroy'])->name('bulk.destroy');

            Route::post('documents/update/{id}', [CompanyDocumentController::class,'update'])->name('documents.update');
            Route::delete('documents/{id}', [CompanyDocumentController::class,'destroy'])->name('documents.destroy');

            Route::group(['prefix' => 'documents', 'as' => 'documents.'], function(){
                Route::get('all', [CompanyDocumentController::class,'index'])->name('index');
                Route::post('create', [CompanyDocumentController::class,'store'])->name('store');
            });
        });

        Route::group(['prefix' => 'users', 'as' => 'users.'], function(){
            Route::get('', [UserController::class,'index'])->name('index');
            Route::get('activity', [UserController::class,'activity'])->name('index');
            Route::get('{id}', [UserController::class,'show'])->name('show');
            Route::get('get/companies', [UserController::class,'get_all_companies'])->name('companies.index');
            Route::get('get/companies-min', [UserController::class,'get_all_companies_new'])->name('companies.new.index');
            Route::post('', [UserController::class,'store'])->name('store');
            Route::put('{id}', [UserController::class,'update'])->name('update');
            Route::put('{id}/{lang}', [UserController::class,'update_lang'])->name('update.lang');
            Route::delete('{id}', [UserController::class,'destroy'])->name('destroy');
            Route::post('update-image/{id}' , [UserController::class,'update_image'])->name('update.image');
            Route::post('remove-image/{id}' , [UserController::class,'remove_image'])->name('remove.image');
        });

        Route::group(['prefix' => 'roles', 'as' => 'roles.'], function(){
            Route::get('', [RoleController::class,'index'])->name('index');
            Route::get('{id}', [RoleController::class,'show'])->name('show');
            Route::post('', [RoleController::class,'store'])->name('store');
            Route::post('assign-permissions-to-role/{id}', [RoleController::class,'assign_permissions_to_role'])->name('assign.permissions.to.role');
            Route::put('{id}', [RoleController::class,'update'])->name('update');
            Route::delete('{id}', [RoleController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'tags', 'as' => 'tags.'], function(){
            Route::get('', [TagController::class,'index'])->name('index');
            Route::post('', [TagController::class,'store'])->name('store');
        });

        Route::group(['prefix' => 'permissions', 'as' => 'permissions.'], function(){
            Route::get('', [PermissionController::class,'index'])->name('index');
            Route::get('{id}', [PermissionController::class,'show'])->name('show');
            Route::post('', [PermissionController::class,'store'])->name('store');
            Route::put('{id}', [PermissionController::class,'update'])->name('update');
            Route::delete('{id}', [PermissionController::class,'destroy'])->name('destroy');
        });
        
        Route::get('/golfcourses/import', [GolfCourseController::class,'import']); 
        Route::get('/hotels/import', [HotelController::class,'import']); 
        Route::get('/country/import', [CountryController::class,'import']); 

        Route::group(['prefix' => 'golfcourses', 'as' => 'golfcourses.'], function(){
            Route::get('', [GolfCourseController::class,'index'])->name('index');
            Route::get('get/all', [GolfCourseController::class,'get_all'])->name('get.all');
            Route::get('{id}', [GolfCourseController::class,'show'])->name('show');
            Route::get('styles/all', [GolfCourseController::class,'get_styles'])->name('styles.all');
            Route::get('basics/all', [GolfCourseController::class,'get_basics'])->name('basics.all');
            Route::get('facilities/all', [GolfCourseController::class,'get_facilities'])->name('facilities.all');
            Route::get('field-types/all', [GolfCourseController::class,'get_field_types'])->name('field.types.all');
            Route::get('services/all', [GolfCourseController::class,'get_services'])->name('services.all');
            Route::get('activities/{id}', [GolfCourseController::class,'get_activities'])->name('activities.all');
            Route::post('', [GolfCourseController::class,'store'])->name('store');
            Route::post('services/{id}', [GolfCourseController::class,'store_services'])->name('store.services');
            Route::post('activities/{id}', [GolfCourseController::class,'store_activity'])->name('store.activity');
            Route::post('upload-images/{id}', [GolfCourseController::class,'upload_images'])->name('upload.images');
            Route::post('{id}/change-main-image', [GolfCourseController::class,'change_main_image'])->name('change.main.image');
            Route::post('delete-image/{id}', [GolfCourseController::class,'delete_image'])->name('delete.image');
            Route::put('{id}', [GolfCourseController::class,'update'])->name('update');
            Route::put('{id}/cms', [GolfCourseController::class,'updateCms'])->name('update');
            Route::put('{id}/facility', [GolfCourseController::class,'updateFacilities'])->name('update.facilities');
            Route::put('publish/{id}', [GolfCourseController::class,'update_publish'])->name('update.publish');
            Route::delete('{id}/{force?}', [GolfCourseController::class,'destroy'])->name('destroy');
            Route::get('get/paginate', [GolfCourseController::class,'index_paginate'])->name('index_paginate');
            

        });

        Route::group(['prefix' => 'hotels', 'as' => 'hotels.'], function(){
            Route::get('', [HotelController::class,'index'])->name('index');
            Route::get('get/all', [HotelController::class,'get_all'])->name('get_all');
            Route::get('get/request-hotel', [HotelController::class,'get_request_hotel'])->name('get_request_hotel');
            Route::get('{id}', [HotelController::class,'show'])->name('show');
            Route::get('basics/all', [HotelController::class,'get_basics'])->name('basics.all');
            Route::get('facilities/all', [HotelController::class,'get_facilities'])->name('facilities.all');
            Route::get('room-facilities/all', [HotelController::class,'get_room_facilities'])->name('room.facilities.all');
            Route::get('boards/all', [HotelController::class,'get_boards'])->name('boards.all');
            Route::get('activities/{id}', [HotelController::class,'get_activities'])->name('activities.all');
            Route::get('field-types/{id}', [HotelController::class,'get_field_types'])->name('field.types.all');
            Route::get('room-field-types/{id}', [HotelController::class,'get_room_field_types'])->name('room.field.types.all');
            Route::get('room-types/{id}', [HotelController::class,'get_room_types'])->name('room.types.all');

            Route::get('services/all', [HotelController::class,'get_services'])->name('services.all');
            Route::post('services/{id}', [HotelController::class,'store_services'])->name('store.services');

            Route::post('rooms/{id}', [HotelController::class,'store_room'])->name('store.room');

            Route::post('', [HotelController::class,'store'])->name('store');
            Route::post('activities/{id}', [HotelController::class,'store_activity'])->name('store.activity');
            Route::post('upload-images/{id}', [HotelController::class,'upload_images'])->name('upload.images');
            Route::post('{id}/change-main-image', [HotelController::class,'change_main_image'])->name('change.main.image');
            Route::post('delete-image/{id}', [HotelController::class,'delete_image'])->name('delete.image');
            Route::put('{id}', [HotelController::class,'update'])->name('update');
            Route::put('publish/{id}', [HotelController::class,'update_publish'])->name('update.publish');
            Route::delete('{id}/{force?}', [HotelController::class,'destroy'])->name('destroy');
            Route::post('{hotel_id}/rooms', [RoomController::class,'store'])->name('store.room.new');
            Route::get('{hotel_id}/rooms', [HotelController::class,'hotel_rooms'])->name('store.room.new');
        });

        Route::group(['prefix' => 'packages', 'as' => 'packages.'], function(){
            Route::get('', [PackageController::class,'index'])->name('index');
            Route::get('{id}', [PackageController::class,'show'])->name('show');
            Route::post('', [PackageController::class,'store'])->name('store');
            Route::put('{id}', [PackageController::class,'update'])->name('update');
            Route::delete('{id}', [PackageController::class,'destroy'])->name('destroy');
            Route::get('field-types/all', [PackageController::class,'get_field_types'])->name('field.types.all');
            Route::post('upload-images/{id}', [PackageController::class,'upload_images'])->name('upload.images');
            Route::post('delete-image/{id}', [PackageController::class,'delete_image'])->name('delete.image');
            Route::post('{id}/change-main-image', [PackageController::class,'change_main_image'])->name('change.main.image');

        });

        Route::group(['prefix' => 'rooms', 'as' => 'rooms.'], function(){
            Route::get('', [RoomController::class,'index'])->name('index');
            Route::get('{id}', [RoomController::class,'show'])->name('show');
            Route::post('upload-images/{id}', [RoomController::class ,'upload_images'])->name('upload.images');
            Route::post('{id}/change-main-image', [RoomController::class,'change_main_image'])->name('change.main.image');
            Route::post('delete-image/{id}', [RoomController::class,'delete_image'])->name('delete.image');
            Route::put('{id}', [RoomController::class,'update'])->name('update');
            Route::delete('{id}', [RoomController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'dmcs', 'as' => 'dmcs.'], function(){
            Route::get('', [DmcController::class,'index'])->name('index');
            Route::get('get/all', [DmcController::class,'get_all'])->name('get_all');
            Route::get('{id}', [DmcController::class,'show'])->name('show');
            Route::get('basics/all', [DmcController::class,'get_basics'])->name('basics.all');
            Route::get('traveltypes/all', [DmcController::class,'get_travel_types'])->name('travel.types.all');
            Route::post('', [DmcController::class,'store'])->name('store');
            Route::post('upload-images/{id}', [DmcController::class,'upload_images'])->name('upload.images');
            Route::post('delete-image/{id}', [DmcController::class,'delete_image'])->name('delete.image');
            Route::put('{id}', [DmcController::class,'update'])->name('update');
            Route::delete('{id}/{force?}', [DmcController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'touroperators', 'as' => 'touroperators.'], function(){
            Route::get('', [TourOperatorController::class,'index'])->name('index');
            Route::get('get/all', [TourOperatorController::class,'get_all'])->name('get_all');
            Route::get('{id}', [TourOperatorController::class,'show'])->name('show');
            Route::post('', [TourOperatorController::class,'store'])->name('store');
            Route::put('{id}', [TourOperatorController::class,'update'])->name('update');
            Route::delete('{id}/{force?}', [TourOperatorController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'travelagencies', 'as' => 'travelagencies.'], function(){
            Route::get('', [TravelAgencyController::class,'index'])->name('index');
            Route::get('get/all', [TravelAgencyController::class,'get_all'])->name('get_all');
            Route::get('{id}', [TravelAgencyController::class,'show'])->name('show');
            Route::get('basics/all', [TravelAgencyController::class,'get_basics'])->name('basics.all');
            Route::get('traveltypes/all', [TravelAgencyController::class,'get_travel_types'])->name('travel.types.all');
            Route::post('', [TravelAgencyController::class,'store'])->name('store');
            Route::put('{id}', [TravelAgencyController::class,'update'])->name('update');
            Route::delete('{id}/{force?}', [TravelAgencyController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'enquiries', 'as' => 'enquiries.'], function(){
            Route::get('', [EnquiryController::class,'index'])->name('index');
            Route::get('{id}', [EnquiryController::class,'show'])->name('show');
            Route::get('status/all', [EnquiryController::class,'get_statuses'])->name('status.all');
            Route::get('sources/all', [EnquiryController::class,'get_sources'])->name('sources.all');
            Route::get('mediums/all', [EnquiryController::class,'get_mediums'])->name('mediums.all');
            Route::post('', [EnquiryController::class,'store'])->name('store');
            Route::put('{id}', [EnquiryController::class,'update'])->name('update');
            Route::put('{id}/status', [EnquiryController::class,'update_status'])->name('update.status');
            Route::post('comment/{id}', [EnquiryController::class,'store_comment'])->name('store.comment');
        });

        Route::group(['prefix' => 'integration/providers', 'as' => 'integration.providers'], function(){
            Route::get('', [IntegrationController::class,'index'])->name('index');
            Route::get('{id}', [IntegrationController::class,'show'])->name('show');
            Route::post('', [IntegrationController::class,'store'])->name('store');
            Route::put('{id}', [IntegrationController::class,'update'])->name('update');
            Route::delete('{id}', [IntegrationController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'product/services', 'as' => 'product.services'], function(){
            Route::get('', [ProductServiceController::class,'index'])->name('index');
            Route::get('get/all', [ProductServiceController::class,'get_all'])->name('get.all');
            Route::get('{id}', [ProductServiceController::class,'show'])->name('show');
            Route::get('seasons/{id}', [ProductServiceController::class,'get_seasons'])->name('seasons.all');
            Route::get('prices/{id}', [ProductServiceController::class,'get_prices'])->name('prices.all');
            Route::post('', [ProductServiceController::class,'store'])->name('store');
            Route::post('seasons/{id}', [ProductServiceController::class,'store_season'])->name('store.season');
            Route::put('{id}', [ProductServiceController::class,'update'])->name('update');
            Route::delete('{id}', [ProductServiceController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'products', 'as' => 'products.'], function(){
            Route::get('', [ProductController::class,'index'])->name('index');
            Route::get('get/all', [ProductController::class,'get_all'])->name('get.all');
            Route::get('{id}', [ProductController::class,'show'])->name('show');
            Route::get('holes/all', [ProductController::class,'get_holes'])->name('holes.all');
            Route::get('tee-times/all', [ProductController::class,'get_tee_times'])->name('tee.times.all');
            Route::post('', [ProductController::class,'store'])->name('store');
            Route::post('bulk', [ProductController::class,'bulk_store'])->name('bulk.store');
            Route::put('{id}', [ProductController::class,'update'])->name('update');
            Route::delete('{id}', [ProductController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'hotel-products', 'as' => 'hotel.products.'], function(){
            Route::get('', [HotelProductController::class,'index'])->name('index');
            Route::get('get/all', [HotelProductController::class,'get_all'])->name('get.all');
            Route::get('{id}', [HotelProductController::class,'show'])->name('show');
            Route::post('', [HotelProductController::class,'store'])->name('store');
            Route::put('{id}', [HotelProductController::class,'update'])->name('update');
            Route::delete('{id}', [HotelProductController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'golf-holidays', 'as' => 'golf.holidays.'], function(){
            Route::get('', [GolfHolidayController::class,'index'])->name('index');
            Route::get('get/all', [GolfHolidayController::class,'get_all'])->name('get.all');
            Route::get('{id}', [GolfHolidayController::class,'show'])->name('show');
            Route::post('', [GolfHolidayController::class,'store'])->name('store');
            Route::put('{id}', [GolfHolidayController::class,'update'])->name('update');
            Route::delete('{id}', [GolfHolidayController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'price-lists', 'as' => 'price.lists.'], function(){
            Route::get('', [PriceListController::class,'index'])->name('index');
            Route::get('{id}', [PriceListController::class,'show'])->name('show');
            Route::get('types/all', [PriceListController::class,'get_types'])->name('types.all');
            Route::post('', [PriceListController::class,'store'])->name('store');
            Route::put('{id}', [PriceListController::class,'update'])->name('update');
            Route::delete('{id}', [PriceListController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'prices', 'as' => 'prices.'], function(){
            Route::post('', [PriceController::class,'store'])->name('store');
            Route::put('{id}', [PriceController::class,'update'])->name('update');
        });

        Route::group(['prefix' => 'seasons', 'as' => 'seasons.'], function(){
            Route::get('', [SeasonController::class,'index'])->name('index');
            Route::get('{id}', [SeasonController::class,'show'])->name('show');
            Route::put('{id}', [SeasonController::class,'update'])->name('update');
            Route::delete('{id}', [SeasonController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'activities', 'as' => 'activities.'], function(){
            Route::get('types/all', [ActivityController::class,'get_types'])->name('types.all');
            Route::put('{id}', [ActivityController::class,'update'])->name('update');
            Route::delete('{id}', [ActivityController::class,'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'modules', 'as' => 'modules.'], function(){
            Route::get('', [ModuleController::class,'index'])->name('index');
        });

        Route::group(['prefix' => 'pages', 'as' => 'pages.'], function(){
            Route::get('', [PageController::class,'index'])->name('index');
        });

        Route::group(['prefix' => 'requests', 'as' => 'requests.'], function(){
            Route::get('', [RequestController::class,'index'])->name('index');
            
            Route::get('{id}', [RequestController::class,'show'])->name('show');
            Route::get('types/all', [RequestController::class,'get_types'])->name('types.all');
            Route::get('statuses/all', [RequestController::class,'get_statuses'])->name('statuses.all');

            Route::get('requests-teetimes/all', [RequestController::class,'get_requests'])->name('requests.all');
            Route::get('handlers-teetimes/all', [RequestController::class,'get_handlers'])->name('handlers.all');
            Route::get('golfcourses-teetimes/all', [RequestController::class,'get_golfcourses'])->name('golfcourses.all');
            Route::get('operators-teetimes/all', [RequestController::class,'get_operators_tee_times'])->name('operators.tee.times.all');
            Route::get('agencies-teetimes/all', [RequestController::class,'get_agencies_tee_times'])->name('agencies.tee.times.all');
            Route::get('date-teetimes/all', [RequestController::class,'get_date_tee_times'])->name('date.tee.times.all');
            Route::get('request-date-teetimes/all', [RequestController::class,'get_request_date_tee_times'])->name('request.date.tee.times.all');
            
            Route::get('company/{id}/teetimes', [RequestController::class,'get_company_tee_times'])->name('company.tee.times.all');
            Route::get('/{id}/teetimes', [RequestController::class,'get_requests_tee_times'])->name('requests.tee.times.all');
            Route::get('golfCourse/{id}/teetimes', [RequestController::class,'get_golfCourse_tee_times'])->name('golfCourse.tee.times.all');
            Route::get('travelAgency/{id}/teetimes', [RequestController::class,'get_travelAgency_tee_times'])->name('travelAgency.tee.times.all');
            Route::get('tourOperator/{id}/teetimes', [RequestController::class,'get_tourOperator_tee_times'])->name('tourOperator.tee.times.all');
            Route::get('teetimes/date', [RequestController::class,'get_tee_times_by_date'])->name('date.tee.times.by.date.all');
            Route::get('teetimes/request-date', [RequestController::class,'get_tee_times_by_request_date'])->name('date.tee.times.request.date.all');
            


            Route::get('validation/check', [RequestController::class,'check_request'])->name('check.request');

            Route::get('agencies/operators/all', [RequestController::class,'get_agencies_operators'])->name('agencies.operators.all');
            
            Route::get('products/statuses/all', [RequestController::class,'get_product_statuses'])->name('products.statuses.all');
            Route::get('tee-times/statuses/all', [RequestController::class,'get_tee_time_statuses'])->name('tee.times.statuses.all');
            
            Route::get('leader-types/all', [RequestController::class,'get_leader_types'])->name('leader.types.all');

            Route::get('{id}/vouchers', [RequestController::class,'get_vouchers'])->name('vouchers.all');
            Route::get('{id}/delegate-client-token', [RequestController::class,'get_delegate_client_token'])->name('get.delegate.client.token');

            Route::post('', [RequestController::class,'store'])->name('store');
            Route::post('store-bulk', [RequestController::class,'store_bulk'])->name('store.bulk');

            Route::put('{id}', [RequestController::class,'update'])->name('update');
            Route::put('{id}/update-bulk', [RequestController::class,'update_bulk'])->name('update.bulk');

            Route::put('{id}/status', [RequestController::class,'update_status'])->name('update.status');
            Route::put('{id}/delegate-client', [RequestController::class,'delegate_client'])->name('delegate.client');
            Route::put('{id}/delegate-client-link', [RequestController::class,'delegate_client_link'])->name('delegate.client.link');
            Route::put('{id}/revoke-delegate-client-link', [RequestController::class,'revoke_delegate_client_link'])->name('revoke.delegate.client.link');
            Route::put('{id}/reminder', [RequestController::class,'send_reminder'])->name('send.reminder');

            Route::post('{id}/destinations', [RequestController::class,'store_destinations'])->name('store.destinations');
            Route::post('{id}/destinations/bulk', [RequestController::class,'store_destinations_bulk'])->name('store.destinations.bulk');
            Route::put('destinations/{id}', [RequestController::class,'update_destinations'])->name('update.destinations');
            Route::delete('destinations/{id}', [RequestController::class,'delete_destinations'])->name('delete.destinations');

            Route::post('{id}/clients', [RequestController::class,'store_clients'])->name('store.clients');
            Route::post('{id}/clients/bulk', [RequestController::class,'store_clients_bulk'])->name('store.clients.bulk');
            Route::put('clients/{id}', [RequestController::class,'update_clients'])->name('update.clients');
            Route::delete('clients/{id}', [RequestController::class,'delete_clients'])->name('delete.clients');

            Route::post('{id}/players', [RequestController::class,'store_players'])->name('store.players');
            Route::post('{id}/players/bulk', [RequestController::class,'store_players_bulk'])->name('store.players.bulk');
            Route::put('players/{id}', [RequestController::class,'update_players'])->name('update.players');
            Route::delete('players/{id}', [RequestController::class,'delete_players'])->name('delete.players');

            Route::post('{id}/products', [RequestController::class,'store_products'])->name('store.products');
            Route::put('products/{id}', [RequestController::class,'update_products'])->name('update.products');
            Route::put('products/{id}/status', [RequestController::class,'update_products_status'])->name('update.products.status');
            Route::get('products/{id}/lock-edit', [RequestController::class,'get_products_lock_edit'])->name('get.products.lock.edit');
            Route::put('products/{id}/lock-edit', [RequestController::class,'update_products_lock_edit'])->name('update.products.lock.edit');
            Route::get('products/{id}/logs', [RequestController::class,'get_product_status_logs'])->name('products.status.logs.all');
            Route::delete('products/{id}', [RequestController::class,'delete_products'])->name('delete.products');

            Route::post('{id}/tee-times', [RequestController::class,'store_tee_times'])->name('store.tee.times');
            Route::post('tee-times/{id}/alternative', [RequestController::class,'store_tee_times_alternative'])->name('store.tee.times.alternative');
            Route::get('tee-times/{id}/alternative', [RequestController::class,'get_tee_time_alternative'])->name('get.tee.times.alternative');

            Route::post('{id}/tee-times/bulk', [RequestController::class,'store_tee_times_bulk'])->name('store.tee.times.bulk');
            Route::put('tee-times/{id}', [RequestController::class,'update_tee_times'])->name('update.tee.times');
            Route::put('tee-times/{id}/status', [RequestController::class,'update_tee_times_status'])->name('update.tee.times.status');
            Route::put('tee-times/bulk/{id}', [RequestController::class,'update_tee_times_bulk'])->name('update.tee.times.bulk');
            Route::get('tee-times/{id}/logs', [RequestController::class,'get_tee_times_status_logs'])->name('tee.times.status.logs.all');
            Route::delete('tee-times/{id}', [RequestController::class,'delete_tee_times'])->name('delete.tee.times');

            Route::post('{id}/comment', [RequestController::class,'store_comment'])->name('store.comment');
            Route::post('{id}/document', [RequestController::class,'store_document'])->name('store.document');

            Route::get('{id}/logs', [RequestController::class,'get_logs'])->name('logs.all');
            Route::get('{id}/status-logs', [RequestController::class,'get_status_logs'])->name('requests.status.logs.all');
            Route::delete('{id}', [RequestController::class,'delete'])->name('delete');
        });

        Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function(){
            Route::get('', [NotificationController::class,'index'])->name('index');
            Route::get('last', [NotificationController::class,'get_last'])->name('last');
            Route::put('update/seen', [NotificationController::class,'update_seen'])->name('update.seen');
        });

        Route::group(['prefix' => 'statistics', 'as' => 'statistics.'], function(){
            Route::get('', [StatisticsController::class,'index'])->name('index');
        });


        Route::group(['prefix' => 'address-books', 'as' => 'address.books.'], function(){
            Route::get('', [AddressBookController::class,'index'])->name('index');
            Route::get('{id}', [AddressBookController::class,'show'])->name('show');
            Route::post('', [AddressBookController::class,'store'])->name('store');
            Route::put('{id}', [AddressBookController::class,'update'])->name('update');
            Route::delete('{id}', [AddressBookController::class,'destroy'])->name('destroy');
        });

    });

    Route::group(['prefix' => 'request-products', 'as' => 'products.'], function(){
        Route::get('{id}', [RequestProductController::class,'show'])->name('show');
        Route::put('{id}', [RequestProductController::class,'update'])->name('update');
        Route::put('details/{id}', [RequestProductController::class,'update_details'])->name('update.details');
        Route::delete('details/{id}', [RequestProductController::class,'delete_details'])->name('destroy.details');
    });
    
    Route::post('services/store-pulck', [ServicesController::class,'store_pulck']);
    Route::apiResource('services', 'ServicesController');

    Route::group(['prefix' => 'integrations', 'as' => 'integrations.', 'namespace' => 'Integration', 'middleware' => ['api.key']], function(){

        Route::group(['prefix' => 'golfcourses', 'as' => 'golfcourses.'], function(){
            Route::get('', 'GolfCourseController@index')->name('index');
            Route::get('get/all', 'GolfCourseController@get_all')->name('get.all');
            Route::get('{id}', 'GolfCourseController@show')->name('show');
            Route::get('styles/all', 'GolfCourseController@get_styles')->name('styles.all');
            Route::get('basics/all', 'GolfCourseController@get_basics')->name('basics.all');
            Route::get('facilities/all', 'GolfCourseController@get_facilities')->name('facilities.all');
            Route::get('field-types/all', 'GolfCourseController@get_field_types')->name('field.types.all');
            Route::get('services/all', 'GolfCourseController@get_services')->name('services.all');
            Route::get('activities/{id}', 'GolfCourseController@get_activities')->name('activities.all');
        });
        
        Route::group(['prefix' => 'hotels', 'as' => 'hotels.'], function(){
            Route::get('', 'HotelController@index')->name('index');
            Route::get('get/all', 'HotelController@get_all')->name('get_all');
            Route::get('{id}', 'HotelController@show')->name('show');
            Route::get('basics/all', 'HotelController@get_basics')->name('basics.all');
            Route::get('facilities/all', 'HotelController@get_facilities')->name('facilities.all');
            Route::get('room-facilities/all', 'HotelController@get_room_facilities')->name('room.facilities.all');
            Route::get('boards/all', 'HotelController@get_boards')->name('boards.all');
            Route::get('activities/{id}', 'HotelController@get_activities')->name('activities.all');
            Route::get('field-types/{id}', 'HotelController@get_field_types')->name('field.types.all');
            Route::get('room-field-types/{id}', 'HotelController@get_room_field_types')->name('room.field.types.all');
            Route::get('room-types/{id}', 'HotelController@get_room_types')->name('room.types.all');

            Route::get('services/all', 'HotelController@get_services')->name('services.all');

        });

        




        Route::group(['prefix' => 'enquiries', 'as' => 'enquiries.'], function(){
            Route::post('', 'EnquiryController@store')->name('store');
        });

        Route::group(['prefix' => 'regions', 'as' => 'regions.'], function(){
            Route::get('', 'RegionController@index')->name('index');
        });


        Route::group(['prefix' => 'countries', 'as' => 'countries.'], function(){
            Route::get('', 'CountryController@index')->name('index');
            Route::get('{id}', 'CountryController@show')->name('show');

            Route::get('field-types/{id}', 'CountryController@get_field_types')->name('field.types.all');
            Route::get('currencies/{id}', 'CountryController@get_currencies')->name('currencies.all');

            Route::post('', 'CountryController@store')->name('store');

            Route::post('testimonies/{id}', 'CountryController@store_testimonies')->name('store.testimonies');
            Route::post('testimonies/{id}/bulk', 'CountryController@store_testimonies_bulk')->name('store.testimonies.bulk');
            Route::post('upload-images/{id}', 'CountryController@upload_images')->name('upload.images');
            Route::post('delete-image/{id}', 'CountryController@delete_image')->name('delete.image');

            Route::put('{id}', 'CountryController@update')->name('update');
            Route::put('{id}/change-main-image', 'CountryController@change_main_image')->name('change.main.image');

            Route::delete('{id}', 'CountryController@destroy')->name('destroy');
        });

        Route::group(['prefix' => 'cities', 'as' => 'cities.'], function(){
            Route::get('', 'CityController@index')->name('index');
            Route::get('{id}', 'CityController@show')->name('show');

            Route::get('field-types/{id}', 'CityController@get_field_types')->name('field.types.all');

            Route::post('', 'CityController@store')->name('store');

            Route::post('testimonies/{id}', 'CityController@store_testimonies')->name('store.testimonies');
            Route::post('testimonies/{id}/bulk', 'CityController@store_testimonies_bulk')->name('store.testimonies.bulk');
            Route::post('upload-images/{id}', 'CityController@upload_images')->name('upload.images');
            Route::post('delete-image/{id}', 'CityController@delete_image')->name('delete.image');

            Route::put('{id}', 'CityController@update')->name('update');
            Route::put('{id}/change-main-image', 'CityController@change_main_image')->name('change.main.image');

            Route::delete('{id}', 'CityController@destroy')->name('destroy');
        });
        
        Route::group(['prefix' => 'testimonies', 'as' => 'testimonies.'], function(){

            Route::put('{id}', 'TestimonyController@update')->name('update');
            
            Route::post('upload-image/{id}', 'TestimonyController@upload_image')->name('upload.image');
            Route::post('delete-image/{id}', 'TestimonyController@delete_image')->name('delete.image');

            Route::delete('{id}', 'TestimonyController@destroy')->name('destroy');
        });

        Route::group(['prefix' => 'packages'], function(){
            Route::get('get-packages', [ProductController::class,'getPackages']);
            Route::get('{id}/offers', [ProductController::class,'getPackageOffers']);
            Route::get('{id}', [PackageController::class,'show'])->name('show');
            Route::get('', [PackageController::class,'index'])->name('index');

        });
        
        Route::group(['prefix' => 'package-cms'], function(){
            Route::get('', [PackageController::class,'index'])->name('index');
            Route::get('{id}', [PackageController::class,'show'])->name('show');
            Route::get('field-types/all', [PackageController::class,'get_field_types'])->name('field.types.all');
        });
    });
});

