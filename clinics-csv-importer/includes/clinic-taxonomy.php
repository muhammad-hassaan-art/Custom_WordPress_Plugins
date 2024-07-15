<?php
// Import taxonomy terms
function import_taxonomy_terms($post_id, $clinic_info_field_group) {
    $terms_services_provided = $clinic_info_field_group['services_provided'] ?? array();
    $terms_community_type = $clinic_info_field_group['community_type'] ?? array();
    $terms_populations_served = $clinic_info_field_group['populations_served'] ?? array();

    wp_set_object_terms($post_id, $terms_services_provided, 'care', false);
    wp_set_object_terms($post_id, $terms_community_type, 'community', false);
    wp_set_object_terms($post_id, $terms_populations_served, 'population', false);
}
?>