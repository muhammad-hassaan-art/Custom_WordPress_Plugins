<?php
// Associate imported field groups with the post
function associate_field_groups_with_post($post_id, $clinic_info_field_group, $clinic_demographic_data, $clinic_impact_data, $clinic_health_data) {

    update_field('clinic_contact_phone_number', $clinic_info_field_group['clinic_contact_phone_number'], $post_id);
    update_field('clinic_website', $clinic_info_field_group['clinic_website'], $post_id);
    update_field('clinic_street_address', $clinic_info_field_group['clinic_street_address'], $post_id);
    update_field('clinic_address_line_2', $clinic_info_field_group['clinic_address_line_2'], $post_id);
    update_field('clinic_city', $clinic_info_field_group['clinic_city'], $post_id);
    update_field('clinic_state', $clinic_info_field_group['clinic_state'], $post_id);
    update_field('clinic_zip', $clinic_info_field_group['clinic_zip'], $post_id);
    update_field('clinic_country', $clinic_info_field_group['clinic_country'], $post_id);
    update_field('clinic_long', $clinic_info_field_group['clinic_long'], $post_id);
    update_field('clinic_lat', $clinic_info_field_group['clinic_lat'], $post_id); 
    update_field('clinic_is_your_organization_nonprofit', $clinic_info_field_group['clinic_is_your_organization_nonprofit'], $post_id);
    update_field('clinic_organization_collaboration', $clinic_info_field_group['clinic_organization_collaboration'], $post_id);
    update_field('clinic_source_of_funding', $clinic_info_field_group['clinic_source_of_funding'], $post_id);
    update_field('clinic_id', $clinic_info_field_group['clinic_id'], $post_id);
    update_field('care_delivered', $clinic_info_field_group['services_provided'], $post_id);
    update_field('community_type', $clinic_info_field_group['community_type'], $post_id);
    update_field('population_served', $clinic_info_field_group['populations_served'], $post_id);
    update_field('clinic_organization_type', $clinic_info_field_group['clinic_organization_type'], $post_id);
    update_field('clinic_organization_type_other', $clinic_info_field_group['clinic_organization_type_other'], $post_id);
    update_field('clinic_organization_collaboration_other', $clinic_info_field_group['clinic_organization_collaboration_other'],$post_id);

    $address = $clinic_info_field_group['clinic_street_address'] . ' ' . $clinic_info_field_group['clinic_address_line_2'] . ' ' . $clinic_info_field_group['clinic_city'] . ' ' . $clinic_info_field_group['clinic_state'] . ' ' . $clinic_info_field_group['clinic_zip'] . ' ' . $clinic_info_field_group['clinic_country'];
    $location = array(
        "address" => $address,
        "lat"     => floatval($clinic_info_field_group['clinic_lat']),
        "lng"     => floatval($clinic_info_field_group['clinic_long']),
    );
    update_field('clinic_location', $location, $post_id);

    $repeater_field_key = 'clinic_demographic_data';
    update_field($repeater_field_key, $clinic_demographic_data['clinic_demographic_repeater'], $post_id);
    $repeater_impact_key = 'clinic_impact_years';
    update_field($repeater_impact_key, $clinic_impact_data['clinic_impact_years'], $post_id);
    $repeater_health_key = 'health_data';
    update_field($repeater_health_key, $clinic_health_data['health_data'], $post_id);
}
?>