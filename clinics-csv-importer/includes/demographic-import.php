<?php
// Import demographic data
function import_demographic_data($demographic_data) {
    $demographic_data_array = json_decode($demographic_data, true);
    $clinic_demographic_data = array(
        'year' => $demographic_data_array['Year'],
        'clinic_demographic_repeater' => array(),
    );

    foreach ($demographic_data_array as $year => $year_data) {
        if ($year === 'Year') {
            continue;
        }

        $clinic_demographic_data['clinic_demographic_repeater'][] = array(
            'year' => $year,
            'clinic_gender' => array(
                'men' => $year_data['men'],
                'women' => $year_data['women'],
                'transgendernon-binary' => '', // You might need to adjust this based on your form
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


// Update demographic data
function update_demographic_data($post_id, $new_demographic_data) {
    $existing_demographic_repeater = get_field('clinic_demographic_data', $post_id);


    if (!$existing_demographic_repeater) {
        return; // No existing data to update
    }

    foreach ($new_demographic_data['clinic_demographic_repeater'] as $index => $new_entry) {
        // Check if the entry exists in the existing repeater
        if (isset($existing_demographic_repeater[$index])) {
            $existing_entry = $existing_demographic_repeater[$index];
            
            // Check if the existing entry is empty or different
            if (empty($existing_entry) || !arrays_are_identical($existing_entry, $new_entry)) {
                // If it's empty or different, update the existing repeater entry
                $existing_demographic_repeater[$index] = $new_entry;
                // Update the entire repeater field
                update_field('clinic_demographic_data', $existing_demographic_repeater, $post_id);
            }
        } else {
            // If the entry doesn't exist, create a new entry in the existing repeater
            $existing_demographic_repeater[$index] = $new_entry;
            // Update the entire repeater field
            update_field('clinic_demographic_data', $existing_demographic_repeater, $post_id);
        }
    }
}

function arrays_are_identical($array1, $array2) {
    return ($array1 == $array2) && (count($array1) == count($array2));
}
?>