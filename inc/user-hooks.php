<?php
function mrj_on_user_register($user_id) {
    global $wpdb;
    $user_info = get_userdata($user_id);
    
    if (!$user_info) {
        error_log('Failed to get user data for user ID: ' . $user_id);
        return;
    }

    $table_name = $wpdb->prefix . 'user_program_access';
    error_log('Registering new user with ID: ' . $user_id);

    $programs = get_posts([
        'post_type' => 'program',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'  // Only get the IDs to reduce memory usage
    ]);

    if (empty($programs)) {
        error_log('No programs found to register for user.');
        return;
    }

    foreach ($programs as $program_id) {
        $program = get_post($program_id);  // Get the program post object
        error_log('Inserting program: ' . $program->post_title); 

        // Check if the program already has a row for this user
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND program_name = %s",
            $user_id,
            $program->post_title
        ));

        // Only insert if it does not exist
        if (!$exists) {
            $result = $wpdb->insert($table_name, [
                'user_id' => $user_id,
                'program_id' => $program_id, 
                'user_email' => $user_info->user_email,
                'program_name' => $program->post_title,
                'access_granted' => 0,
                'created_at' => current_time('mysql', 1)
            ]);

            if ($result === false) {
                error_log('Failed to insert program access for user. Error: ' . $wpdb->last_error);
            } else {
                error_log('Program access for user ' . $user_id . ' to program ' . $program->post_title . ' added.');
            }
        }
    }
}

add_action('user_register', 'mrj_on_user_register');


// Hook into user account deletion and clean up custom data
function mrj_on_user_delete($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_program_access';

    // Delete the records where the user ID matches the ID of the user being deleted
    $result = $wpdb->delete($table_name, ['user_id' => $user_id]);

    if (false === $result) {
        error_log("Failed to delete user program access records for user ID: $user_id");
    } else {
        error_log("Deleted user program access records for user ID: $user_id");
    }
}

// Add the hook into WordPress
add_action('delete_user', 'mrj_on_user_delete');

