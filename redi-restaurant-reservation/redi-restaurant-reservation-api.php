<?php
trait ReDiAPIHelperMethods
{
    public function handle_api_response_error($response) {
        if (isset($response['Error'])) {
            return new WP_Error(
                'redi_api_error',
                $response['Error'],
                array('status' => 500)
            );
        }
        return false;
    }

    
    public function get_all_places()
    {
        if ($this->ApiKey == null) {
            return $this->handle_api_key_error();
        }

        $cached_places = get_transient('redi_restaurant_places');

        // If cached data exists, return it
        if ($cached_places !== false) {
            return $cached_places;
        }

        $places = $this->redi->getPlaces();

        $error_response = $this->handle_api_response_error($places);
        
        if (is_wp_error($error_response)) {
            return $error_response;
        }

        // Extracting only id, name, and address fields from each place
        $filteredPlaces = array();
        
        foreach ($places as $place) {
            $filteredPlaces[] = array(
                'id' => $place->ID,
                'name' => $place->Name,
                'address' => $place->Address,
                'email' => $place->Email,
                'phone' => $place->Phone,
                'min_time_before_reservation' => $place->MinTimeBeforeReservation, 
                'min_time_before_reservation_type' => $place->MinTimeBeforeReservationType,
                'max_time_before_reservation' => $place->MaxTimeBeforeReservation,
                'max_time_before_reservation_type' => $place->MaxTimeBeforeReservationType,
            );
        }

        set_transient('redi_restaurant_places', $filteredPlaces, HOUR_IN_SECONDS);

        return $filteredPlaces;
    }

    /**
     * Get custom fields for a specific place
     * 
     * @return WP_REST_Response|WP_Error Response object or WP_Error
     */
    public function get_custom_fields($place_id, $lang) {
        if ($this->ApiKey == null) {
            return $this->handle_api_key_error();
        }

        $cache_key = 'redi_restaurant_custom_fields_' . $place_id . '_' . $lang;
        
        // Check cache first
        $cached_fields = get_transient($cache_key);

        if ($cached_fields !== false) {
            return new WP_REST_Response($cached_fields, 200);
        }
        
        $custom_fields = $this->redi->getCustomField($lang, $place_id);

        $error_response = $this->handle_api_response_error($custom_fields);
    
        if (is_wp_error($error_response)) {
            return $error_response;
        } 

        $custom_fields_array = json_decode(json_encode($custom_fields), true);

        foreach ($custom_fields_array as &$field) {
            if (isset($field['Text'])) {
            $field['Text'] = $this->extractTranslatedContent($field['Text'], $lang);
            }
        }

        $custom_fields = $custom_fields_array;

        // Cache the results
        set_transient($cache_key, $custom_fields, HOUR_IN_SECONDS);

        return new WP_REST_Response($custom_fields, 200);
    }

    /**
     * Handle API key error
     * 
     * @return WP_Error Error response indicating the API key is missing or invalid
     */
    public function handle_api_key_error() {
        return new WP_Error(
            'redi_api_error',
            __('ReDi Restaurant Reservation plugin not registered', 'redi-restaurant-reservation'),
            array('status' => 401)
        );
    }

/**
 * Get date information for a specific place
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error Response object or WP_Error
 */
public function get_days_availability($place_id, $start_time, $end_time, $guests){
    if ($this->ApiKey == null) {
        return $this->handle_api_key_error();
    }

    $cache_key = 'redi_restaurant_days_availability_' . $place_id . '_' . $start_time . '_' . $end_time . '_' . $guests;
    
    // Check cache first
    $cached_dates = get_transient($cache_key);

    if ($cached_dates !== false) {
        return new WP_REST_Response($cached_dates, 200);
    }

    $categories = $this->get_place_categories($place_id);

    $error_response = $this->handle_api_response_error($categories);

    if (is_wp_error($error_response)) {
        return $error_response;
    } 
    
    $category_id = $categories[0]->ID;
    $dates = $this->redi->getDateInformation(
        str_replace('_', '-', get_locale()), 
        $category_id, 
        array(
            'StartTime' => $start_time,
            'EndTime' => $end_time,
            'Guests' => $guests,
        )
    );

    $error_response = $this->handle_api_response_error($dates);

    if (is_wp_error($error_response)) {
        return $error_response;
    }         

    // Cache the results
    set_transient($cache_key, $dates, HOUR_IN_SECONDS);

    return new WP_REST_Response($dates, 200);
}
    
    
    public function get_time_availability($place_id, $day, $duration, $guests, $step) {
        if ($this->ApiKey == null) {
            return $this->handle_api_key_error();
        }

        $cache_key = 'redi_restaurant_time_availability_' . $place_id . '_' . $day . '_' . $duration . '_' . $guests;
        
        // Check cache first
        $cached_availability = get_transient($cache_key);

        if ($cached_availability !== false) {
            return new WP_REST_Response($cached_availability, 200);
        }

        $categories = $this->get_place_categories($place_id);

        $error_response = $this->handle_api_response_error($categories);
    
        if (is_wp_error($error_response)) {
            return $error_response;
        } 

        $StartTime = gmdate('Y-m-d 00:00', strtotime($day)); //CalendarDate + 00:00
        $EndTime = gmdate('Y-m-d 00:00',
            strtotime('+1 day', strtotime($day))); //CalendarDate + 1day + 00:00
        $currentTimeISO = current_datetime()->format('Y-m-d H:i');

        $params = array(
            'StartTime' => urlencode($StartTime),
            'EndTime' => urlencode($EndTime),
            'Quantity' => $guests,
            'ReservationDuration' => $duration,
            'AlternativeTimeStep' => $step,
            'Lang' => str_replace('_', '-', get_locale()),
            'CurrentTime' => urlencode($currentTimeISO)
        );

        $time_availability = $this->redi->availabilityByDay($categories[0]->ID, $params);
        
        $error_response = $this->handle_api_response_error($time_availability);
    
        if (is_wp_error($error_response)) {
            return $error_response;
        }

        if (has_filter('redi-reservation-discount'))
        {
            $discounts = apply_filters('redi-reservation-discount', $startTimeInt, $placeID);
        }

        if (isset($discounts))
        {
            foreach ($query as $q2) {
                if (isset($q2->Availability)) {
                    foreach ($q2->Availability as $q) {
                        
                        if (isset($discounts))
                        {
                            $discountElement = apply_filters('redi-reservation-max-discount', $discounts, $q->StartTime);

                            if (isset($discountElement))
                            {
                                $q->Discount = $discountElement->discountVisual;
                                $q->DiscountClass = $discountElement->discountClass;
                            }
                        }
                        
                        //$q->Select = ($startTimeISO == $q->StartTime && $q->Available);
                        //$q->StartTimeISO = $q->StartTime;
                        //$q->EndTimeISO = $q->EndTime;
                        //$q->StartTime = ReDiTime::format_time($q->StartTime, $time_lang, $time_format);
                        //$q->EndTime = gmdate($time_format, strtotime($q->EndTime));
                        
                        //$duration = date_diff(date_create($q->StartTimeISO), date_create($q->EndTimeISO));
                        //$q->Duration = $duration->h * 60 + $duration->i;
                    }
                }
            }
        }

        // Cache the results
        set_transient($cache_key, $time_availability, HOUR_IN_SECONDS);

        return new WP_REST_Response($time_availability, 200);
    }

    /**
     * Get place categories with caching
     * 
     * @param int $place_id Place ID
     * @return array|WP_Error Categories array or WP_Error
     */
    public function get_place_categories($place_id) {
        if ($this->ApiKey == null) {
            return $this->handle_api_key_error();
        }

        $cache_key = 'redi_restaurant_place_categories_' . $place_id;

        // Check cache first
        $cached_categories = get_transient($cache_key);

        if ($cached_categories !== false) {
            return $cached_categories;
        }

        $categories = $this->redi->getPlaceCategories($place_id);

        $error_response = $this->handle_api_response_error($categories);

        if (is_wp_error($error_response)) {
            return $error_response;
        }

        // Cache the results for 24 hours
        set_transient($cache_key, $categories, DAY_IN_SECONDS);

        return $categories;
    }

    /**
     * Create a reservation
     * 
     * @param int $place_id Place ID
     * @param array $reservation Reservation data
     * @return WP_REST_Response|WP_Error Response object or WP_Error
     */
    public function create_reservation($place_id, $reservation) {
        if ($this->ApiKey == null) {
            return $this->handle_api_key_error();
        }

        $params = array(
            'reservation' => array(
            'StartTime' => "2025-05-07 15:30",
            'EndTime' => "2025-05-07 16:00",
            'Quantity' => 5,
            'ChildrenQuantity' => 0,
            'UserName' => "Sergei",
            'FirstName' => "Pro",
            'LastName' => "",
            'UserEmail' => "email@email.ee",
            'UserComments' => "",
            'UserPhone' => "+37253435345",
            'UserProfileUrl' => "https://secure.gravatar.com/avatar/41cf65a0b68a6ef868aab77210d4ef3c?s=96&d=mm&r=g",
            'Name' => "Person",
            'Lang' => "en-US",
            'CurrentTime' => "2025-03-16 21:19",
            'Version' => "24.1209-Default_v1",
            'PrePayment' => "false",
            'Source' => "HOMEPAGE",
            'Admin' => true)
        );

        if (isset($reservation['trackingCode']))
        {
            $params[] = array(
                'Name'  => "TrackingCode",
                'Type'  => "Hidden",
                'Value' => $reservation['trackingCode']);
        }        
        
        $response = $this->redi->createReservation($params);

        $error_response = $this->handle_api_response_error($response);

        if (is_wp_error($error_response)) {
            return $error_response;
        }

        return new WP_REST_Response($response, 200);
    }
}