<?php
// Function to get Impact years data
function get_gravity_forms_impact_data($clinic_submission_id, $impact_form_id) {

    $paging = array('offset' => 0, 'page_size' => 30);
    $impact_entries = array();
    $search_criteria = array(
        'status' => 'active',
        'field_filters' => array(
            'mode' => 'any',
            array(
                'key' => '82',
                'value' => $clinic_submission_id,
            )
        )
    );

    do {
        $partial_entries = GFAPI::get_entries($impact_form_id, $search_criteria, null, $paging);
        $impact_entries = array_merge($impact_entries, $partial_entries);
        $paging['offset'] += $paging['page_size'];
    } while (!empty($partial_entries));

    $impact_data = array();

    foreach ($impact_entries as $entry) {
        if ($entry && $entry['form_id'] == $impact_form_id) {
            $year = rgar($entry, '3');
            $impact_data[$year] = array(
                'entry_id' => rgar($entry, 'id'),
                'owner' => rgar($entry, '83'),
                'date' => rgar($entry, '5'),
                'notes' => rgar($entry, '48'),
                'visits' => rgar($entry, '9'),
                'patients' => rgar($entry, '81'),
                'new_patients' => rgar($entry, '10'),
                'Fieldset' => rgar($entry, '93'),
                'covid-19_vaccinations' => rgar($entry, '97'),
                'covid-19_tests' => rgar($entry, '98'),
                'immunizations' => rgar($entry, '13'),
                'vision_screening' => rgar($entry, '14'),
                'injury_prevention_counseling' => rgar($entry, '15'),
                'cervical_cancer_screening' => rgar($entry, '17'),
                'breast_cancer_screening' => rgar($entry, '18'),
                'chlamydia_screening_&_treatment' => rgar($entry, '19'),
                'osteoporosis_screening_&_counseling' => rgar($entry, '20'),
                'discuss_folic_acid' => rgar($entry, '22'),
                'discuss_calcium_supplementation' => rgar($entry, '23'),
                'colorectal_cancer_screening' => rgar($entry, '25'),
                'hypertension_screening_&_treatment' => rgar($entry, '26'),
                'high_risk_cholesterol_screening' => rgar($entry, '27'),
                'cholesterol_screening_&_treatment' => rgar($entry, '28'),
                'obesity_screening' => rgar($entry, '29'),
                'depression_screening' => rgar($entry, '30'),
                'diabetes_screening' => rgar($entry, '31'),
                'syphilis' => rgar($entry, '100'),
                'hiv' => rgar($entry, '101'),
                'discuss_daily_aspirin' => rgar($entry, '33'),
                'tobacco_smoking_cessation_advice_&_help_quit' => rgar($entry, '34'),
                'alcohol_misuse_screening_&_brief_counseling' => rgar($entry, '35'),
                'unhealthy_drug_use_screening' => rgar($entry, '102'),
                'influenza_immunization' => rgar($entry, '36'),
                'diet_counseling' => rgar($entry, '37'),
                'tetanus_diphtheria_booster' => rgar($entry, '38'),
                'social_determinants_of_health_screening' => rgar($entry, '103'),
                'older_adults_vision_screening' => rgar($entry, '40'),
                'pneumococcal_immunization' => rgar($entry, '41'),
                'hearing_screening' => rgar($entry, '42'),
                'yearly_clinic_operating_costs' => rgar($entry, '44'),

            );
        }
    }
    return $impact_data;
}

?>