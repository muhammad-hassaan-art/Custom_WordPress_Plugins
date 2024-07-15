<?php
// Import clinic information
function import_clinic_info($row) {

    return array(
        'clinic_contact_phone_number' => sanitize_phone_number($row[2]),
        'clinic_website' => $row[3],
        'clinic_street_address' => $row[4],
        'clinic_address_line_2' => $row[5],
        'clinic_city' => $row[6],
        'clinic_state' => $row[7],
        'clinic_zip' => $row[8],
        'clinic_country' => $row[9],
        'clinic_long' => $row[10],
        'clinic_lat' => $row[11],
        'clinic_is_your_organization_nonprofit' => ($row[12] == 'Yes') ? true : false,
        // 'clinic_organization_type' => $row[5],
        'clinic_organization_collaboration' => explode(', ', $row[14]),
        'clinic_source_of_funding' => explode(', ', $row[13]),
        'clinic_id' => $row[0],
        'community_type' => explode(', ', $row[15]),
        'services_provided' => explode(', ', $row[16]),
        'populations_served' => explode(', ', $row[17]),
        'clinic_organization_type' => explode(', ', $row[22]),
        'clinic_organization_type_other' => $row[23],
        'clinic_organization_collaboration_other' => $row[24],
    );
}
// Sanitize phone number
function sanitize_phone_number($phone_number) {
    return preg_replace('/[^0-9]/', '', $phone_number);
}


// Update clinic information
function update_clinic_information($post_id, $existing_values, $new_values) {

    foreach ($new_values as $key => $value) {
        if (is_array($value)) {
            // Handle multiple values (e.g., exploded values)
            if (!is_null($existing_values[$key]) && count(array_diff($value, $existing_values[$key])) > 0) {
                update_field($key, $value, $post_id);
            }
        } elseif (!is_null($existing_values[$key]) && $existing_values[$key] !== $value) {
            // Single value, update if different
            update_field($key, $value, $post_id);
        }
    }
}

// Get existing clinic values
function get_existing_clinic_values($post_id) {
    return array(
        'clinic_contact_phone_number'           => get_field('clinic_contact_phone_number', $post_id),
        'clinic_website'                       => get_field('clinic_website', $post_id),
        'clinic_street_address'                => get_field('clinic_street_address', $post_id),
        'clinic_address_line_2'                => get_field('clinic_address_line_2', $post_id),
        'clinic_city'                          => get_field('clinic_city', $post_id),
        'clinic_state'                         => get_field('clinic_state', $post_id),
        'clinic_zip'                           => get_field('clinic_zip', $post_id),
        'clinic_country'                       => get_field('clinic_country', $post_id),
        'clinic_long'                          => get_field('clinic_long', $post_id),
        'clinic_lat'                           => get_field('clinic_lat', $post_id),
        'clinic_is_your_organization_nonprofit' => get_field('clinic_is_your_organization_nonprofit', $post_id),
        // 'clinic_organization_type'             => get_field('clinic_organization_type', $post_id),
        'clinic_organization_collaboration'    => get_field('clinic_organization_collaboration', $post_id),
        'clinic_source_of_funding'             => get_field('clinic_source_of_funding', $post_id),
        'clinic_id'                            => get_field('clinic_id', $post_id),
        'clinic_organization_type'             => get_field('clinic_organization_type', $post_id),
        'clinic_organization_type_other'       => get_field('clinic_organization_type_other', $post_id),
        'clinic_organization_collaboration_other' => get_field('clinic_organization_collaboration_other', $post_id)
    );
}

?>