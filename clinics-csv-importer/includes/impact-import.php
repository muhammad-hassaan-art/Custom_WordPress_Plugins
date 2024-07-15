<?php
// Import impact form data
function import_impact_form_data($impact_form_data) {
    $impact_form_data_array = json_decode($impact_form_data, true);
    $clinic_impact_data = array(
        'year' => $impact_form_data_array['Year'],
        'clinic_impact_years' => array(),
    );
    foreach ($impact_form_data_array as $year => $year_data) {
        if ($year === 'Year') {
            continue;
        }
        $clinic_impact_data['clinic_impact_years'][] = array(
            'year' => $year,
            'owner' => $year_data['owner'],
            'date' => $year_data['date'],
            'planning_data' => $year_data['planning_data'],
            'notes' => $year_data['notes'],
            'visits' => $year_data['visits'],
            'patients' => $year_data['patients'],
            'new_patients' => $year_data['new_patients'],
            'covid-19_vaccinations' => $year_data['covid-19_vaccinations'],
            'covid-19_tests' => $year_data['covid-19_tests'],
            'immunizations' => $year_data['immunizations'],
            'vision_screening' => $year_data['vision_screening'],
            'injury_prevention_counseling' => $year_data['injury_prevention_counseling'],
            'cervical_cancer_screening' => $year_data['cervical_cancer_screening'],
            'breast_cancer_screening' => $year_data['breast_cancer_screening'],
            'chlamydia_screening_&_treatment' => $year_data['chlamydia_screening_&_treatment'],
            'osteoporosis_screening_&_counseling' => $year_data['osteoporosis_screening_&_counseling'],
            'discuss_folic_acid' => $year_data['discuss_folic_acid'],
            'discuss_calcium_supplementation' => $year_data['discuss_calcium_supplementation'],
            'colorectal_cancer_screening' => $year_data['colorectal_cancer_screening'],
            'hypertension_screening_&_treatment' => $year_data['hypertension_screening_&_treatment'],
            'high_risk_cholesterol_screening' => $year_data['high_risk_cholesterol_screening'],
            'cholesterol_screening_&_treatment' => $year_data['cholesterol_screening_&_treatment'],
            'obesity_screening' => $year_data['obesity_screening'],
            'depression_screening' => $year_data['depression_screening'],
            'diabetes_screening' => $year_data['diabetes_screening'],
            'syphilis' => $year_data['syphilis'],
            'hiv' => $year_data['hiv'],
            'discuss_daily_aspirin' => $year_data['discuss_daily_aspirin'],
            'tobacco_smoking_cessation_advice_&_help_quit' => $year_data['tobacco_smoking_cessation_advice_&_help_quit'],
            'alcohol_misuse_screening_&_brief_counseling' => $year_data['alcohol_misuse_screening_&_brief_counseling'],
            'unhealthy_drug_use_screening' => $year_data['unhealthy_drug_use_screening'],
            'influenza_immunization' => $year_data['influenza_immunization'],
            'diet_counseling' => $year_data['diet_counseling'],
            'tetanus_diphtheria_booster' => $year_data['tetanus_diphtheria_booster'],
            'social_determinants_of_health_screening' => $year_data['social_determinants_of_health_screening'],
            'older_adults_vision_screening' => $year_data['older_adults_vision_screening'],
            'pneumococcal_immunization' => $year_data['pneumococcal_immunization'],
            'hearing_screening' => $year_data['hearing_screening'],
            'yearly_clinic_operating_costs' => $year_data['yearly_clinic_operating_costs'],
        );
    }

    return $clinic_impact_data;
}
?>