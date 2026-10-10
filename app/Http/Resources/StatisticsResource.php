<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'hotels_number' => $this->get_key('hotels_number'),
            'dmcs_number' => $this->get_key('dmcs_number'),
            'travel_agencies_number' => $this->get_key('travel_agencies_number'),
            'golf_clubs_number' => $this->get_key('golf_clubs_number'),
            'golf_courses_number' => $this->get_key('golf_courses_number'),
            'tour_operators_number' => $this->get_key('tour_operators_number'),
            'requests_number' => $this->get_key('requests_number'),
            'users_number' => $this->get_key('users_number'),
            'enquiries_number' => $this->get_key('enquiries_number'),
            'products_number' => $this->get_key('products_number'),
            'hotel_products_number' => $this->get_key('hotel_products_number'),
            'golf_holidays_number' => $this->get_key('golf_holidays_number'),
            'clients_number' => $this->get_key('clients_number'),
            'players_number' => $this->get_key('players_number'),
            'regions_number' => $this->get_key('regions_number'),
            'countries_number' => $this->get_key('countries_number'),
            'cities_number' => $this->get_key('cities_number'),
        ];
    }
}
