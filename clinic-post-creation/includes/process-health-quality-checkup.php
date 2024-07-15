<?php
//Function to get health form data years data
function process_health_quality_forms_data($clinic_submission_id, $health_quality_form_id){

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
    return integrate_health_form_data($health_data);
}

function integrate_health_form_data($health_form_data) {
       
    $clinic_health_data = array(
        'year' => $health_form_data['Year'],
        'health_data' => array(),
    );
    foreach ($health_form_data as $year => $year_data) {
        if ($year === 'Year') {
            continue;
        }
        $clinic_health_data['health_data'][] = array(
            'health_year' => $year,
            'health_affordable' => $year_data['health_affordable'],
            'health_written_information' => $year_data['health_written_information'],
            'health_convenient_location' => $year_data['health_convenient_location'],
            'health_common_languages' => $year_data['health_common_languages'],
            'health_reflect_diversity' => $year_data['health_reflect_diversity'],
            'health_offer_counseling' => $year_data['health_offer_counseling'],
            'health_use_clinical_interventions' => $year_data['health_use_clinical_interventions'],
            'health_long_lasting' => $year_data['health_long_lasting'],
            'health_health_context' => $year_data['health_health_context'],
            'health_social_determinants' => $year_data['health_social_determinants'],
            'health_analyze_community' => $year_data['health_analyze_community'],
            'health_get_feedback' => $year_data['health_get_feedback'],
            'health_review_data' => $year_data['health_review_data'],
            'health_adjust_services' => $year_data['health_adjust_services'],
            'health_train_personnel' => $year_data['health_train_personnel'],
            'operational_data' => $year_data['operational_data'],
            'health_equity_data' => $year_data['health_equity_data'],
            'health_outcomes_data' => $year_data['health_outcomes_data'],
            'health_governance_data' => $year_data['health_governance_data'],
            'health_finance_data' => $year_data['health_finance_data'],
            'health_evidence-based' => $year_data['health_evidence-based'],
            'health_measure_changes' => $year_data['health_measure_changes'],
            'health_track_expenses' => $year_data['health_track_expenses'],
            'health_track_return' => $year_data['health_track_return'],
        );
    }

    return $clinic_health_data;
}
?>