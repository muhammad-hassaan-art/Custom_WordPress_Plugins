<?php
//Function to get health form data years data
function get_health_quality_forms_data($clinic_submission_id, $health_quality_form_id){

    $paging = array('offset' => 0, 'page_size' => 30);
    $health_entries = array();
    $search_criteria = array(
        'status' => 'active',
        'field_filters' => array(
            'mode' => 'any',
            array(
                'key' => '48',
                'value' => $clinic_submission_id,
            )
        )
    );

    do {
        $partial_entries = GFAPI::get_entries($health_quality_form_id, $search_criteria, null, $paging);
        $health_entries = array_merge($health_entries, $partial_entries);
        $paging['offset'] += $paging['page_size'];
    } while (!empty($partial_entries));
    $health_data = array();

    foreach ($health_entries as $entry) {
        if ($entry && $entry['form_id'] == $health_quality_form_id) {
            $year = rgar($entry, '1');
            $health_data[$year] = array(
                'entry_id' => rgar($entry, 'id'),
                'owner' => rgar($entry, '49'),
                'date' => rgar($entry, '4'),
                'notes' => rgar($entry, '5'),
                'health_affordable' => rgar($entry, '8'),
                'health_written_information' => rgar($entry, '9'),
                'health_convenient_location' => rgar($entry, '10'),
                'health_common_languages' => rgar($entry, '11'),
                'health_reflect_diversity' => rgar($entry, '12'),
                'health_offer_counseling' => rgar($entry, '18'),
                'health_use_clinical_interventions' => rgar($entry, '19'),
                'health_long_lasting' => rgar($entry, '20'),
                'health_health_context' => rgar($entry, '21'),
                'health_social_determinants' => rgar($entry, '22'),
                'health_analyze_community' => rgar($entry, '25'),
                'health_get_feedback' => rgar($entry, '26'),
                'health_review_data' => rgar($entry, '27'),
                'health_adjust_services' => rgar($entry, '28'),
                'health_train_personnel' => rgar($entry, '29'),
                'operational_data' => rgar($entry, '32'),
                'health_equity_data' => rgar($entry, '33'),
                'health_outcomes_data' => rgar($entry, '34'),
                'health_governance_data' => rgar($entry, '35'),
                'health_finance_data' => rgar($entry, '36'),
                'health_evidence-based' => rgar($entry, '39'),
                'health_measure_changes' => rgar($entry, '40'),
                'health_track_expenses' => rgar($entry, '42'),
                'health_track_return' => rgar($entry, '43'),
            );
        }
    }
    return $health_data;
}
?>