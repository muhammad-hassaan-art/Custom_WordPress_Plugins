<?php
// Function to prepare clinic info entry
function prepare_clinic_info_entry($entry, $clinic_demographic_form_id, $impact_form_id, $health_quality_form_id , $paging) {

    // get user data as well
    $user_id = rgar($entry, 'created_by'); // Adjust '99' to the correct field ID

    // Check for clinic name in either field 61 or 4, prefer 61 if available
    $clinic_name = rgar($entry, '61') ?: rgar($entry, '4');
    
    // If there is no clinic name in both fields, skip this entry
    if (empty($clinic_name)) {
        return null; // or return []; depending on how you want to handle this in the calling code
    }

    // Get the location string for Google Maps
    $location = rgar($entry, '24.1') . ' ' . rgar($entry, '24.2') . ' ' . rgar($entry, '24.3') . ' ' . rgar($entry, '24.4') . ' ' . rgar($entry, '24.5') . ' ' . rgar($entry, '24.6');

    // Get latitude and longitude
    $mapLat = rgar($entry, '24.geolocation_latitude');
    $mapLong = rgar($entry, '24.geolocation_longitude');

    // If latitude and longitude are not provided, retrieve them from Google Maps
    if ($mapLat == '' && $mapLong == '') {
        $latlong = get_lat_long($location); // Get lat long from Google Maps
        $map = explode(',' ,$latlong);
        $mapLat = $map[0];
        $mapLong = $map[1];
    }


    $clinic_entry = array(
        'entry_id' => rgar($entry, 'id'),
        'clinic_name' => $clinic_name,
        'clinic_contact_phone_number' => rgar($entry, '6'),
        'clinic_website' => rgar($entry, '7'),
        'clinic_street_address' => rgar($entry, '24.1'),
        'clinic_address_line_2' => rgar($entry, '24.2'),
        'clinic_city' => rgar($entry, '24.3'),
        'clinic_state' => rgar($entry, '24.4'),
        'clinic_zip' => rgar($entry, '24.5'),
        'clinic_country' => rgar($entry, '24.6'),
        'clinic_long' => $mapLong,
        'clinic_lat' => $mapLat,
        'clinic_is_your_organization_nonprofit' => rgar($entry, '43'),
        'clinic_source_of_funding' => implode(', ', rs_gf_get_checked_boxes($entry, 19)),
        'clinic_organization_collaboration' => implode(', ', rs_gf_get_checked_boxes($entry, 36)),
        'community_type' => implode(', ', rs_gf_get_checked_boxes($entry, 15)),
        'services_provided' => implode(', ', rs_gf_get_checked_boxes($entry, 16)),
        'populations_served' => implode(', ', rs_gf_get_checked_boxes($entry, 17)),
        'user_info' => json_encode(get_user_info_for_clinic($user_id)),
        'demographic_data' => json_encode(get_demographic_data($entry['id'], $clinic_demographic_form_id)),
        'impact_Form_Data' => json_encode(get_gravity_forms_impact_data($entry['id'], $impact_form_id)),
        'health_data' => json_encode(get_health_quality_forms_data($entry['id'], $health_quality_form_id)),
        'clinic_organization_type' => implode(', ', rs_gf_get_checked_boxes($entry,41)),
        'clinic_organization_type_other' => rgar($entry, 42),
        'clinic_organization_collaboration_other' => rgar($entry, 37),
        
    );

    return $clinic_entry;
}
// Return an array of checkboxes that have been checked on a Gravity Form entry
function rs_gf_get_checked_boxes($entry, $field_id) {
    $items = array();

    $field_keys = array_keys($entry);

    foreach ($field_keys as $input_id) {
        if (is_numeric($input_id) && absint($input_id) == $field_id) {
            $value = rgar($entry, $input_id);

            if ("" !== $value) $items[$input_id] = $value;
        }
    }

    return $items;
}

// function to get the address
function get_lat_long($address) {
    // Define the cache duration in seconds (e.g., 1 day)
    $cache_duration = 86400;
    // Generate a unique cache key based on the address
    $cache_key = 'geocode_' . md5($address);
    // Check if the geocode data is cached
    $cached_geocode = get_transient($cache_key);

    if ($cached_geocode) {
        // If cached data exists, return it
        return $cached_geocode;
    } else {
        // If not cached, make an API request to Google Maps
        $address = str_replace(" ", "+", $address);
        $api_key = ''; // Replace with your actual API key

        // Fetch geocode data from Google Maps
        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&key=$api_key");
        $json = json_decode($json);

        if ($json && isset($json->results[0]->geometry->location->lat) && isset($json->results[0]->geometry->location->lng)) {
            // Extract latitude and longitude from the JSON response
            $lat = $json->results[0]->geometry->location->lat;
            $lng = $json->results[0]->geometry->location->lng;
            // Format and store the geocode data
            $geocode = "$lat,$lng";
            // Cache the geocode data for future use
            set_transient($cache_key, $geocode, $cache_duration);
            return $geocode;
        } else {
            // Handle API request failure or invalid response
            return false;
        }
    }
}
?>