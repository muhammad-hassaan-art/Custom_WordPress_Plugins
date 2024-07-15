<?php
// Function to get demographic data
function process_demographic_data($clinic_demographic_form_id,$clinic_submission_id)
{

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
                'transgender' => rgar($demographic_data, '48'),
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

    return integrate_demographic_data($demographic_data);
}

// Map demographic data
function integrate_demographic_data($demographic_data)
{

    $clinic_demographic_data = array(
        'year' => $demographic_data['Year'],
        'clinic_demographic_repeater' => array(),
    );


    foreach ($demographic_data as $year => $year_data) {
        if ($year === 'Year') {
            continue;
        }

        $clinic_demographic_data['clinic_demographic_repeater'][] = array(
            'year' => $year,
            'clinic_gender' => array(
                'men' => $year_data['men'],
                'women' => $year_data['women'],
                'transgendernon-binary' => $year_data['transgender'],
            ),
            'age_group' => array(
                'clinic_0-17' => $year_data['clinic_0-17'],
                '18-44' => $year_data['18-44'],
                '45-64' => $year_data['45-64'],
                '65+' => $year_data['65+'],
            ),
            'race' => array(
                'american_indian_and_alaska_native' => $year_data['american_indian_and_alaska_native'],
                'asian' => $year_data['asian'],
                'black__african_american' => $year_data['black_african_american'],
                'hispanic__latino' => $year_data['hispanic_latino'],
                'native_hawaiian_and_other_pacific_islander' => $year_data['pacific_islander'],
                'white' => $year_data['white'],
                'multiracial' => $year_data['multiracial'],
                'unknown' => $year_data['unknown'],
            ),
            'insurance' => array(
                'private' => $year_data['private'],
                'medicaid__chip' => $year_data['medicaid_chip'],
                'medicare' => $year_data['medicare'],
                'multiple' => $year_data['multiple'],
                'uninsured' => $year_data['uninsured'],
            ),
            'date_updated' => $year_data['date_updated'],
        );
    }

    return $clinic_demographic_data;
}
