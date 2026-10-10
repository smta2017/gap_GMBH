<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            ModulePageSeeder::class,
            
            LanguageSeeder::class,
            CurrencySeeder::class,
            // RegionSeeder::class,
            // CountrySeeder::class,
            // CitySeeder::class,
            CompanyTypeSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            DefaultUserSeeder::class,
            UserSeeder::class,
            CompanySeeder::class,
            GolfCourseStyleSeeder::class,
            FacilitySeeder::class,
            RoomFacilitySeeder::class,
            GolfCourseBasicSeeder::class,
            ServiceSeeder::class,
            HotelSurroundingsServiceSeeder::class,
            ActivityTypeSeeder::class,
            // HotelSeeder::class,
            // GolfCourseSeeder::class,
            BoardSeeder::class,
            DocumentTypeSeeder::class,
            TravelTypeSeeder::class,
            EnquiryStatusSeeder::class,
            IntegrationSeeder::class,
            SourceSeeder::class,
            MediumSeeder::class,
            TeeTimesSeeder::class,
            HolesSeeder::class,
            PriceListTypeSeeder::class,
            FieldTypeSeeder::class,
            RoomFieldTypeSeeder::class,
            DestinationSeeder::class,
            DestinationFieldTypeSeeder::class,
            RequestTypeSeeder::class,
            LeaderTypeSeeder::class,
            RequestStatusSeeder::class,
            RequestProductStatusSeeder::class,
            RequestTeeTimeStatusSeeder::class,
            // ImageSeeder::class,
            // ProductSeeder::class,
            RoomTypeSeeder::class,
            RoomViewSeeder::class,
            RoomBoardSeeder::class,
            // TourOperatorSeeder::class,
            TourOperatorNewSeeder::class,
            // TravelAgencySeeder::class,
            // WfsHotelSeeder::class,
            // RegionCountryCitySeeder::class,
            // DmcSeeder::class,
            MoveServiceCityIdToCitiesSeeder::class,
            GroupHotelsWithTheSameCodeSeeder::class,
            // ProductService2Seeder::class,
            // CompanyUserSeeder::class,
            // RemoveDublicateCompanySeederr::class,
            // RemoveDeviceKeySeeder::class,
            DmcChangeCompanyTypeSeeder::class,
            // DeleteRequestsSeeder::class,
            GolfCourseHotelMainImageSeeder::class,
            // PoolBeachFacilitySeeder::class,
            // HotelCategorySeeder::class,
            // DirectlyOnTheBeachFacilitySeeder::class,
            // AddAditionalInfoToRequestProductsSeeder::class,
            // RemoveTaSeeder::class,
            // RemoveTaCompanyUserSeeder::class,
            // RemoveSerivesAndProducts::class,
            // AreasSeeder::class,
            // RegionTrSeeder::class,
            // ClearDataSeeder::class,
            PlayableSeeder::class,
        ]);
    }
}
