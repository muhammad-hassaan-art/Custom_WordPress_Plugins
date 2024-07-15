<?php
/*
Plugin Name: Custom Clinics Importer
Description: Custom plugin for importing clinics data into WordPress posts with ACF fields.
Version: 1.1
Author: Fishnet
*/

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');

// Include other files
require_once plugin_dir_path(__FILE__) . 'includes/clinic-import.php';
require_once plugin_dir_path(__FILE__) . 'includes/clinic-taxonomy.php';
require_once plugin_dir_path(__FILE__) . 'includes/demographic-import.php';
require_once plugin_dir_path(__FILE__) . 'includes/impact-import.php';
require_once plugin_dir_path(__FILE__) . 'includes/health-import.php';
require_once plugin_dir_path(__FILE__) . 'includes/clinic-associate-field-group.php';

// Menu creation
add_action('admin_menu', 'add_custom_clinics_importer_menu');
// add_custom_clinics_importer_menu();
function add_custom_clinics_importer_menu() {
    add_menu_page(
        'Custom Clinics Importer',    // Page title
        'Clinics Importer',           // Menu title
        'manage_options',             // Capability required to access
        'custom-clinics-importer',    // Menu slug
        'custom_clinics_importer_page'// Callback function to display content
    );
}

// Page display for clinic importer
function custom_clinics_importer_page() {
    ?>
<div class="wrap">
    <h2>Clinics Importer</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <label for="csv_file">Upload CSV File:</label>
        <input type="file" name="csv_file" accept=".csv">
        <input type="submit" name="import_data" value="Import Data">
    </form>
</div>
<?php
}

// Process clinics import
add_action('init', 'process_clinics_import');
function process_clinics_import() {

    if (isset($_POST['import_data'])) {
        if (!empty($_FILES['csv_file']['name'])) {
            $csv_file = $_FILES['csv_file']['tmp_name'];
            $csv_data = array_map('str_getcsv', file($csv_file));
            foreach ($csv_data as $index => $row) {
                if ($index === 0) {
                    continue; 
                }
                $clinic_name = sanitize_text_field($row[1]);
                $clinic_slug = sanitize_title($clinic_name);

                $entry_id = intval($row[0]);

            
                $existing_post = get_posts(array(
                    'posts_per_page'    => -1,
                    'post_type'     => 'clinic',
                    'meta_key'      => 'clinic_id',
                    'meta_value'    => $entry_id
                ));

                if ($existing_post) {
                    // Clinic with same clinic_id and created_by exists, delete and recreate
                    foreach ($existing_post as $post) {
                        wp_delete_post($post->ID, true); // Delete post and its attachments
                    }
                }
                
                // Create the clinic post
                $post_id = create_clinic_post($clinic_name, $clinic_slug);
                
                if ($post_id) {
                    // Import various data associated with the clinic
                    $clinic_info_field_group = import_clinic_info($row);
                    import_taxonomy_terms($post_id, $clinic_info_field_group);
                    $clinic_user_data = $row[18]; // Assuming user data is in column 19 (index 18 in zero-based index)
                    import_user_data($clinic_user_data, $post_id);
                    $clinic_demographic_data = import_demographic_data($row[19]); // Assuming demographic data is in column 20 (index 19 in zero-based index)
                    $clinic_impact_data = import_impact_form_data($row[20]); // Assuming impact data is in column 21 (index 20 in zero-based index)
                    $clinic_health_data = import_health_form_data($row[21]); // Assuming health data is in column 22 (index 21 in zero-based index)
                    
                    // Associate various field groups with the post
                    associate_field_groups_with_post($post_id, $clinic_info_field_group, $clinic_demographic_data, $clinic_impact_data, $clinic_health_data);
                    
                    error_log('Clinic imported successfully. Clinic Name: ' . $clinic_name . ', Post ID: ' . $post_id);
                } else {
                    error_log('Error creating post for Clinic Name: ' . $clinic_name);
                }
            }
        }
    }
}

// Create clinic post
function create_clinic_post($clinic_name, $clinic_slug) {
    return wp_insert_post(array(
        'post_title'   => $clinic_name,
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'clinic',
        'post_name'    => $clinic_slug,
    ));
}
// Import user data and set as post author
function import_user_data($user_data, $post_id) {
    $user_data_array = json_decode($user_data, true);


    $owner_name = $user_data_array['owner'] ?? '';

    $user_id = $user_data_array['user_id'] ?? '';

    update_field('created_by', $user_id, $post_id);

    $user_name = $user_data_array['user_name'] ?? '';   

    if ($user_name) {
        // Check if user with the username exists
        $existing_user = get_user_by('login', $user_name);
        
        if ($existing_user) {
            // Set the user as the post author
            
            wp_update_post(array('ID' => $post_id, 'post_author' => $existing_user->ID));
    
            error_log('Post author set successfully. Post ID: ' . $post_id . ', Author: ' . $owner_name);
        } else {
            error_log('User not found for username: ' . $user_name);
        }
    }
}



?>