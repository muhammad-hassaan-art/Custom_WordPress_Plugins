<?php
/*
Plugin Name: Clinics CSV Exporter
Description: Custom plugin for integrating Gravity Forms data into ACF fields and creating clinics custom post type.
Version: 1.1
Author: Fishnet
*/
error_reporting(E_ERROR | E_PARSE);
ini_set("display_errors", "1");

// Include other files
require_once plugin_dir_path(__FILE__) . "includes/clinic-info-entry.php";
require_once plugin_dir_path(__FILE__) . "includes/get-demographic-data.php";
require_once plugin_dir_path(__FILE__) . "includes/get-health-quality-data.php";
require_once plugin_dir_path(__FILE__) . "includes/get-impact-years-data.php";
require_once plugin_dir_path(__FILE__) . "includes/get-user-data.php";
// Add menu page to the admin menu
add_action("admin_menu", "add_custom_clinics_menu");

function add_custom_clinics_menu()
{
  add_menu_page(
    "Custom Clinics Plugin", // Page title
    "Clinics Exporter", // Menu title
    "manage_options", // Capability required to access
    "custom-clinics-menu", // Menu slug
    "custom_clinics_menu_page", // Callback function to display content
    "dashicons-building" // Icon (use dashicons classes)
  );
}

// AJAX action to run clinic_generate_csv function
add_action("wp_ajax_run_clinic_generate_csv", "clinic_generate_csv");

// Page display for clinics data
function custom_clinics_menu_page()
{
  ?>
<div class="wrap">
    <h2>Clinics Data</h2>
    <button id="my-plugin-button">Generate CSV</button>
    <div id="loader" style="display:none;"><img width="25px" height="25px"
            src="<?php echo plugin_dir_url(__FILE__) .
              "/loader.gif"; ?>" alt="Loading..."></div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#my-plugin-button').on('click', function() {
        $('#loader').show();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'run_clinic_generate_csv',
            },
            success: function(response) {
                $('#loader').hide();

                var blob = new Blob([response], {
                    type: 'text/csv'
                });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'clinics_data.csv';
                link.click();
            },
            error: function(error) {
                $('#loader').hide();
                console.error('AJAX error:', error);
            }
        });
    });
});
</script>
<?php
}

// Function to generate CSV file
function clinic_generate_csv()
{
  $clinics_data = get_gravity_forms_data_retrieval();
  $file = fopen("php://temp", "w+");

  fputcsv($file, array_keys($clinics_data[0]), ",");

  foreach ($clinics_data as $row) {
    fputcsv($file, $row, ",", '"');
  }

  rewind($file);

  header("Content-Type: text/csv");
  header('Content-Disposition: attachment; filename="clinics_data.csv"');

  fpassthru($file);

  fclose($file);

  exit();
}

// Function to retrieve Gravity Forms data
function get_gravity_forms_data_retrieval()
{
  $clinic_info_form_id = 7;
  $clinic_demographic_form_id = 9;
  $impact_form_id = 6;
  $health_quality_form_id = 10;
  $paging = ["offset" => 0, "page_size" => 30];
  $clinic_info_entries = [];
  $search_criteria = ["status" => "active"];

  do {
    $partial_entries = GFAPI::get_entries(
      $clinic_info_form_id,
      $search_criteria,
      null,
      $paging
    );
    $clinic_info_entries = array_merge($clinic_info_entries, $partial_entries);
    $paging["offset"] += $paging["page_size"];
  } while (!empty($partial_entries));

  // Prepare the clinic info entries and filter out null or empty entries
  $clinic_info = array_filter(
    array_map(function ($entry) use (
      $clinic_demographic_form_id,
      $impact_form_id,
      $health_quality_form_id,
      $paging
    ) {
      return prepare_clinic_info_entry(
        $entry,
        $clinic_demographic_form_id,
        $impact_form_id,
        $health_quality_form_id,
        $paging
      );
    }, $clinic_info_entries),
    function ($entry) {
      // Ensure the entry is not null and is an array with elements
      return !is_null($entry) && !empty($entry);
    }
  );

  return array_values($clinic_info); // Resetting array keys
}

?>
