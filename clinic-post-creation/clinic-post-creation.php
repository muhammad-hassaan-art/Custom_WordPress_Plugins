<?php
/*
Plugin Name: Clinic Post Creation
Description: Plugin to handle clinic info form submissions and create posts
Version: 1.1
Author: Fishnet
*/

class ClinicPostCreation {
    public function __construct() {
        // Include other files
        require_once plugin_dir_path(__FILE__) . 'includes/process-demographic-data.php';
        require_once plugin_dir_path(__FILE__) . 'includes/process-impact-data.php';
        require_once plugin_dir_path(__FILE__) . 'includes/process-health-quality-checkup.php';

        // Hook to handle form submissions
        add_action('gform_after_submission', [$this, 'handle_form_submission'], 10, 2);
    }

    public function handle_form_submission($entry, $form) {
        switch ($form['id']) {
            case 7:
                $this->create_clinic_post_from_form($entry);
                break;
            case 9:
                $this->process_demographic_data($entry);
                break;
            case 6:
                $this->process_impact_data($entry);
                break;
            case 10:
                $this->process_health_quality_forms_data($entry);
                break;
        }
    }

    private function clinic_exists($submission_id) {
        $existing_clinic = get_posts([
            'posts_per_page' => 1,
            'post_type'      => 'clinic',
            'meta_key'       => 'clinic_id',
            'meta_value'     => $submission_id,
            'post_status'  => 'publish',
        ]);

        return !empty($existing_clinic);
    }

    private function get_clinic_post_id($submission_id) {
        $clinic_post = get_posts([
            'posts_per_page' => 1,
            'post_type'      => 'clinic',
            'meta_query'     => [
                [
                    'key'   => 'clinic_id',
                    'value' => $submission_id,
                ],
            ],
        ]);

        return $clinic_post ? $clinic_post[0]->ID : false;
    }

    private function create_clinic_post_from_form($entry) {
        $form_entry_id = isset($entry['id']) ? $entry['id'] : false;
        $clinic_primary_id = isset($entry['31']) ? $entry['31'] : false;
        $query_clinic_id = $clinic_primary_id ? $clinic_primary_id : $form_entry_id;
    
        error_log('query_clinic_id: ' . $query_clinic_id);
    
        $existing_clinic = get_posts([
            'posts_per_page' => 1,
            'post_type'      => 'clinic',
            'meta_key'       => 'clinic_id',
            'meta_value'     => $query_clinic_id,
            'post_status'    => 'any' // Check all statuses, including trash
        ]);
    
    
        if (!empty($existing_clinic)) {
            $post_id = $existing_clinic[0]->ID;
            $this->update_clinic_fields($entry, $post_id, $query_clinic_id);
        } elseif ($form_entry_id) {
            $this->create_or_update_clinic($entry, $query_clinic_id, '61', '4');
        } elseif (!empty($clinic_primary_id)) {
            $this->create_or_update_clinic($entry, $query_clinic_id, '61', '4');
        } else {
            error_log('No action taken');
        }
    }
    
    private function create_or_update_clinic($entry, $query_clinic_id, $clinic_name_field_id_1, $clinic_name_field_id_2) {
        $clinic_name = rgar($entry, $clinic_name_field_id_1) ?: rgar($entry, $clinic_name_field_id_2);
        $clinic_slug = sanitize_title($clinic_name);
    
        $post_id = wp_insert_post([
            'post_title'   => $clinic_name,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'clinic',
            'post_name'    => $clinic_slug,
        ]);
    
        if (is_wp_error($post_id)) {
            error_log('Error creating post: ' . $post_id->get_error_message());
        } else {
            $this->update_clinic_fields($entry, $post_id, $query_clinic_id);
        }
    }

    private function update_clinic_fields($entry, $post_id, $query_clinic_id) {
        $location = rgar($entry, '24.1') . ' ' . rgar($entry, '24.2') . ' ' . rgar($entry, '24.3') . ' ' . rgar($entry, '24.4') . ' ' . rgar($entry, '24.5') . ' ' . rgar($entry, '24.6');
        $mapLat = rgar($entry, '24.geolocation_latitude');
        $mapLong = rgar($entry, '24.geolocation_longitude');

        if ($mapLat == '' && $mapLong == '') {
            $latlong = $this->get_lat_long($location);
            $map = explode(',', $latlong);
            $mapLat = $map[0];
            $mapLong = $map[1];
        }

        $address = rgar($entry, '24.1') . ' ' . rgar($entry, '24.2') . ' ' . rgar($entry, '24.3') . ' ' . rgar($entry, '24.4') . ' ' . rgar($entry, '24.5') . ' ' . rgar($entry, '24.6');
        $location = [
            "address" => $address,
            "lat"     => floatval($mapLat),
            "lng"     => floatval($mapLong),
        ];

        update_field('clinic_id', $query_clinic_id, $post_id);
        update_field('created_by', rgar($entry, '47'), $post_id);
        update_field('clinic_contact_phone_number', rgar($entry, '6'), $post_id);
        update_field('clinic_website', rgar($entry, '7'), $post_id);
        update_field('clinic_street_address', rgar($entry, '24.1'), $post_id);
        update_field('clinic_address_line_2', rgar($entry, '24.2'), $post_id);
        update_field('clinic_city', rgar($entry, '24.3'), $post_id);
        update_field('clinic_state', rgar($entry, '24.4'), $post_id);
        update_field('clinic_zip', rgar($entry, '24.5'), $post_id);
        update_field('clinic_country', rgar($entry, '24.6'), $post_id);
        update_field('clinic_long', $mapLong, $post_id);
        update_field('clinic_lat', $mapLat, $post_id);
        update_field('clinic_location', $location, $post_id);
        update_field('clinic_is_your_organization_nonprofit', (rgar($entry, '43') == 'Yes') ? true : false, $post_id);

        update_field('clinic_source_of_funding', explode(",", implode(',', $this->rs_gf_get_checked_boxes($entry, 19))), $post_id);
        update_field('clinic_organization_collaboration', explode(",", implode(',', $this->rs_gf_get_checked_boxes($entry, 36))), $post_id);
        update_field('clinic_organization_collaboration_other', rgar($entry, 37), $post_id);
        update_field('clinic_organization_type', explode(",", implode(', ', $this->rs_gf_get_checked_boxes($entry, 41))), $post_id);
        update_field('clinic_organization_type_other', rgar($entry, 42), $post_id);

        $this->create_taxonomy_terms($post_id, [
            'community_type'      => explode(',', implode(', ', $this->rs_gf_get_checked_boxes($entry, 15))),
            'services_provided'   => explode(',', implode(', ', $this->rs_gf_get_checked_boxes($entry, 16))),
            'populations_served'  => explode(',', implode(', ', $this->rs_gf_get_checked_boxes($entry, 17))),
        ]);
    }

    private function create_taxonomy_terms($post_id, $clinic_info_field_group) {
        $terms_services_provided = $clinic_info_field_group['services_provided'] ?? [];
        $terms_community_type = $clinic_info_field_group['community_type'] ?? [];
        $terms_populations_served = $clinic_info_field_group['populations_served'] ?? [];

        wp_set_object_terms($post_id, $terms_services_provided, 'care', false);
        wp_set_object_terms($post_id, $terms_community_type, 'community', false);
        wp_set_object_terms($post_id, $terms_populations_served, 'population', false);
    }

    private function rs_gf_get_checked_boxes($entry, $field_id) {
        $items = [];

        foreach ($entry as $input_id => $value) {
            if (is_numeric($input_id) && absint($input_id) == $field_id && $value !== "") {
                $items[$input_id] = $value;
            }
        }

        return $items;
    }

    private function get_lat_long($address) {
        $cache_duration = 86400;
        $cache_key = 'geocode_' . md5($address);
        $cached_geocode = get_transient($cache_key);

        if ($cached_geocode) {
            return $cached_geocode;
        } else {
            $address = str_replace(" ", "+", $address);
            $api_key = ''; // Replace with your actual API key

            $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&key=$api_key");
            $json = json_decode($json);

            if ($json && isset($json->results[0]->geometry->location->lat) && isset($json->results[0]->geometry->location->lng)) {
                $lat = $json->results[0]->geometry->location->lat;
                $lng = $json->results[0]->geometry->location->lng;
                $geocode = "$lat,$lng";
                set_transient($cache_key, $geocode, $cache_duration);
                return $geocode;
            } else {
                return false;
            }
        }
    }

    private function process_demographic_data($entry) {
        $clinicId = rgar($entry, '56');
        $clinic_exists = $this->clinic_exists($clinicId);

        if ($clinic_exists) {
            $post_id = $this->get_clinic_post_id($clinicId);
            delete_field('clinic_demographic_data', $post_id);
            $processed_data = process_demographic_data(9, $clinicId);
            $repeater_field_key = 'clinic_demographic_data';
            update_field($repeater_field_key, $processed_data['clinic_demographic_repeater'], $post_id);
        }
    }

    private function process_impact_data($entry) {
        $impactClinicId = rgar($entry, '82');
        $clinic_exists = $this->clinic_exists($impactClinicId);

        if ($clinic_exists) {
            $post_id = $this->get_clinic_post_id($impactClinicId);
            delete_field('clinic_impact_years', $post_id);
            $processed_data = process_impact_data($impactClinicId, 6);
            $repeater_field_key = 'clinic_impact_years';
            update_field($repeater_field_key, $processed_data['clinic_impact_years'], $post_id);
        }
    }

    private function process_health_quality_forms_data($entry) {
        $healthClinicId = rgar($entry, '48');
        $clinic_exists = $this->clinic_exists($healthClinicId);

        if ($clinic_exists) {
            $post_id = $this->get_clinic_post_id($healthClinicId);
            delete_field('health_data', $post_id);
            $processed_data = process_health_quality_forms_data($healthClinicId, 10);
            $repeater_field_key = 'health_data';
            update_field($repeater_field_key, $processed_data['health_data'], $post_id);
        }
    }
}

// Instantiate the class
new ClinicPostCreation();
