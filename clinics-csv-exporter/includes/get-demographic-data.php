<?php
// Function to get demographic data
function get_demographic_data($clinic_submission_id, $clinic_demographic_form_id) {


    $paging = array('offset' => 0, 'page_size' => 30);
    $search_criteria = array(
        'status' => 'active',
        'field_filters' => array(
            'mode' => 'any',
            array(
                'key' => '56',
                'value' => $clinic_submission_id,
            )
        )
    );
    $demographic_entries = array();

    do {
        $partial_entries = GFAPI::get_entries($clinic_demographic_form_id, $search_criteria, null, $paging);
        $demographic_entries = array_merge($demographic_entries, $partial_entries);
        $paging['offset'] += $paging['page_size'];
    } while (!empty($partial_entries));


    $demographic_data = array();

    if (!is_wp_error($demographic_entries)) {

        foreach ($demographic_entries as $demographic_entry) {
            $year = rgar($demographic_entry, '1');
            $demographic_data[$year] = array(
                'men' => rgar($demographic_entry, '7'),
                'women' => rgar($demographic_entry, '8'),
                'clinic_0-17' => rgar($demographic_entry, '12'),
                '18-44' => rgar($demographic_entry, '13'),
                '45-64' => rgar($demographic_entry, '14') ?: 'N/A',
                '65+' => rgar($demographic_entry, '15') ?: 'N/A',
                'american_indian_and_alaska_native' => rgar($demographic_entry, '17'),
                'asian' => rgar($demographic_entry, '18'),
                'black_african_american' => rgar($demographic_entry, '20'),
                'hispanic_latino' => rgar($demographic_entry, '21'),
                'pacific_islander' => rgar($demographic_entry, '22'),
                'white' => rgar($demographic_entry, '23'),
                'multiracial' => rgar($demographic_entry, '24'),
                'unknown' => rgar($demographic_entry, '49'),
                'private' => rgar($demographic_entry, '26'),
                'medicaid_chip' => rgar($demographic_entry, '27'),
                'medicare' => rgar($demographic_entry, '28'),
                'multiple' => rgar($demographic_entry, '29'),
                'uninsured' => rgar($demographic_entry, '30'),
            );
        }
    }

    return $demographic_data;
}
?>