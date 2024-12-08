<?php
trait ReDiAPIHelperMethods
{
    public function get_all_places()
    {
        if ($this->ApiKey == null) {

            $errors['Error'] = array(
                __(
                    'ReDi Restaurant Reservation plugin not registered'
                )
            );
            $this->display_errors($errors, true, 'Not registered');
            die;
        }

        $cached_places = get_transient('redi_restaurant_places');

        // If cached data exists, return it
        if ($cached_places !== false) {
            return $cached_places;
        }

        $places = $this->redi->getPlaces();

        if (isset ($places['Error'])) {
            $this->display_errors($places, true, 'getPlaces');
            die;
        }

        // Extracting only id, name, and address fields from each place
        $filteredPlaces = array();
        foreach ($places as $place) {
            $filteredPlaces[] = array(
                'id' => $place->ID,
                'name' => $place->Name,
                'address' => $place->Address
            );
        }

        set_transient('redi_restaurant_places', $filteredPlaces, HOUR_IN_SECONDS);

        return $filteredPlaces;
    }

    /**
     * Get custom fields for a specific place
     * 
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error Response object or WP_Error
     */
    public function get_custom_fields($request) {
        if ($this->ApiKey == null) {
            return new WP_Error(
                'redi_api_error',
                __('ReDi Restaurant Reservation plugin not registered'),
                array('status' => 401)
            );
        }

        $place_id = $request['place_id'];
        $cache_key = 'redi_restaurant_custom_fields_' . $place_id;
        
        // Check cache first
        $cached_fields = get_transient($cache_key);
        if ($cached_fields !== false) {
            return new WP_REST_Response($cached_fields, 200);
        }
        
        try {
            $custom_fields = $this->redi->getCustomField(self::lang(), $place_id);
            
            if (isset($custom_fields['Error'])) {
                return new WP_Error(
                    'redi_api_error',
                    $custom_fields['Error'],
                    array('status' => 400)
                );
            }

            // Cache the results
            set_transient($cache_key, $custom_fields, HOUR_IN_SECONDS);

            return new WP_REST_Response($custom_fields, 200);
            
        } catch (Exception $e) {
            return new WP_Error(
                'redi_api_error',
                $e->getMessage(),
                array('status' => 500)
            );
        }
    }
}