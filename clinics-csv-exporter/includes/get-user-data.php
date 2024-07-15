<?php
//Function to get user Data
function get_user_info_for_clinic($user_id) {
    $user = get_userdata($user_id);
    if ($user) {
        // Assuming standard WP roles and a single role per user
        $role = !empty($user->roles) ? $user->roles[0] : 'N/A';
        $status = $user->user_status == 0 ? 'Active' : 'Inactive'; // Simplified; adjust as needed

        $user_info = array(
            'owner' => $user->display_name,
            'user_name' => $user->user_login,
            'user_id' => $user_id,
            'user_email' => $user->user_email,
            'user_role' => $role,
            'user_status' => $status,
        );

        return $user_info;
    }

    return null; // or an appropriate default/fallback
}
?>