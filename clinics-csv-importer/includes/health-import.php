<?php
//import health form data 

function import_health_form_data($health_form_data) {
    $health_form_data_array = json_decode($health_form_data, true);

   
    
    $clinic_health_data = array(
        'year' => $health_form_data_array['Year'],
        'health_data' => array(),
    );
    foreach ($health_form_data_array as $year => $year_data) {
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