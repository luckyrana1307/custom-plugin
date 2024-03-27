<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
    function voteUser(userId, userName) {
        alert('Your selected candidate is ' + userName);
    }
</script>



<script>
jQuery(document).ready(function($) {
    $('#showUsersCheckbox').change(function() {
        if($(this).is(":checked")) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_all_users_ajax'
                },
                success: function(response) {
                    $('#userListContainer').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        } else {
            $('#userListContainer').empty();
        }
    });

    $('#userListForm').submit(function(e) {
        e.preventDefault();
        // Your form submission logic here
    });
});
</script>


<?php
/*
Plugin Name: Custom User Management
Description: Custom user management functionality.
Version: 1.0
Author: Your Name
*/

// Display list of users
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}



function custom_user_list() {
    $users = get_users();

    echo '<h2 class="text-center">User List</h2>';
    echo '<form method="post" action="">';
    echo'<div class="row">';
    echo'<div class="col-md-8 mx-auto">';
    echo '<ul class="list-group">';
    foreach ($users as $user) {
        $user_id = $user->ID;
        $user_login = esc_html($user->user_login);
        echo '<li class="list-group-item w-100"><a href="admin.php?page=custom-manage-profiles&user_id=' . $user_id . '">' . $user_login . '</a></li>';
    }
    echo '</ul>';
    echo '<h2 class="text-center">Select Users to Send Email</h2>';
    echo '<ul class="list-group">';
    foreach ($users as $user) {
        $user_id = $user->ID;
        $user_login = esc_html($user->user_login);
        echo '<li class="list-group-item w-100">';
        echo '<label><input type="checkbox" name="selected_users[]" value="' . $user_id . '"> ' . $user_login . '</label>';
        echo '</li>';
    }
    echo '</ul>';
    echo '<p><input type="submit" name="send_emails" value="Send Email"></p>';
    echo'</div>';
    echo'</div>';
    echo '</form>';
}


// Handle form submission

// Handle form submission
function handle_email_submission() {
    if (isset($_POST['send_emails'])) {
        $selectedUsers = isset($_POST['selected_users']) ? $_POST['selected_users'] : array();
        
        // Loop through selected user IDs and send emails
        foreach ($selectedUsers as $userId) {
            $user = get_userdata($userId);
            $to = $user->user_email;
            $subject = 'Your subject here';
            $message = 'Your message here';
            wp_mail($to, $subject, $message);
        }
        echo '<div class="updated"><p class="text-center">Emails sent successfully!</p></div>';
    }
}

// Create new user
function custom_create_user_page() {
   
    if (isset($_POST['create_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone_number = $_POST['phone_number'];
        $linkedin = $_POST['linkedin'];
        $gender = $_POST['gender'];
        $hobbies = $_POST['hobbies'];

        // Create the user
        $userdata = array(
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
        );

        $user_id = wp_insert_user($userdata);

        if (!is_wp_error($user_id)) {
            // User created successfully, now save additional user meta data
            update_user_meta($user_id, 'phone_number', $phone_number);
            update_user_meta($user_id, 'linkedin', $linkedin);
            update_user_meta($user_id, 'gender', $gender);
            update_user_meta($user_id, 'hobbies', $hobbies);

            echo '<div class="updated"><p class="row">User created successfully!</p></div>';
        } else {
            echo '<div class="error"><p>' . $user_id->get_error_message() . '</p></div>';
        }
    }

    // Display form for creating user
    echo '<div class="row  ">';
    echo '<div class="col-md-9 mx-auto mt-5">';
    echo '<div class=" ">';
    echo '<h2>Create New User</h2>';
    echo '<form method="post" class="form-control p-5">';
    echo '<p class="row"><label class="col-md-3" for="username">Username:</label>
     <input class="col-md-8" type="text" name="username" id="username" required></p>';
    echo '<p class="row"><label class="col-md-3" for="email">Email:</label>
     <input class="col-md-8" type="email" name="email" id="email" required></p>';
    echo '<p class="row"><label class="col-md-3" for="password">Password:</label>
     <input class="col-md-8" type="password" name="password" id="password" required></p>';
    echo '<p class="row"><label class="col-md-3" for="first_name">First Name:</label>
     <input class="col-md-8" type="text" name="first_name" id="first_name"></p>';
    echo '<p class="row"><label class="col-md-3" for="last_name">Last Name:</label>
     <input class="col-md-8" type="text" name="last_name" id="last_name"></p>';
    echo '<p class="row"><label class="col-md-3" for="phone_number">Phone Number:</label>
     <input class="col-md-8" type="text" name="phone_number" id="phone_number"></p>';
    echo '<p class="row"><label class="col-md-3" for="linkedin">LinkedIn:</label>
     <input class="col-md-8" type="text" name="linkedin" id="linkedin"></p>';
   echo '<div class="row mb-2">';
        echo '<label class="col-md-3">Gender:</label>';
        echo '<div class="col-md-7 mb-2">';
        echo '<label class="mx-2"><input class="form-check-input" type="radio" name="gender" value="male"> Male</label>';
        echo '<label class="mx-2"><input class="form-check-input" type="radio" name="gender" value="female"> Female</label>';
        echo '<label class="mx-2"><input class="form-check-input" type="radio" name="gender" value="other"> Other</label>';
        echo '</div>';
        echo '</div';
    echo '<p class="row"><label class="col-md-3" for="hobbies">Hobbies:</label> <textarea class="col-md-8" name="hobbies" id="hobbies"></textarea></p>';
    echo '<p class="row "> <div class="col-md-10"></div><input class="btn btn-success  col-md-2 float-end" type="submit" name="create_user" value="Create User"></p>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function custom_manage_user_profiles_page() 
{
    echo '<h2>User Profile Management</h2>';
    
    // Check if a user ID is provided in the URL
    if(isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        $user_data = get_userdata($user_id);
        
        if($user_data) {
            // Display user information
            echo'<div class="row ">';
            echo '<div class="col-md-8 mx-auto mt-5 bg-light text-center">';
            echo '<h3>User Information</h3>';
            echo '<div class="d-flex justify-content-center card mx-auto">';
            echo '<p><strong>Username:</strong> ' . $user_data->user_login . '</p>';
            echo '<p><strong>Email:</strong> ' . $user_data->user_email . '</p>';
            echo '<p><strong>First Name:</strong> ' . $user_data->first_name . '</p>';
            echo '<p><strong>Last Name:</strong> ' . $user_data->last_name . '</p>';
            echo '<p><strong>Phone Number:</strong> ' . $user_data->phone_number . '</p>';
            echo '<p><strong>Linkedin:</strong> ' . $user_data->linkedin . '</p>';
            echo '<p><strong>gender:</strong> ' . $user_data->gender . '</p>';
            echo '<p><strong>hobbies:</strong> ' . $user_data->hobbies . '</p>';
            echo "</div>";
            echo'</div>';
            echo '</div>';


// Add the following code to your main PHP file

// Handle update user form submission
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $linkedin = $_POST['linkedin'];
    $gender = $_POST['gender'];
    $hobbies = $_POST['hobbies'];

    $userdata = array(
        'ID' => $user_id,
        'user_login' => $username,
        'user_email' => $email,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'phone_number' => $phone_number,
        'linkedin' => $linkedin,
        'gender' => $gender,
        'hobbies' => $hobbies,
    );

    $user_id = wp_update_user($userdata);

    if (!is_wp_error($user_id)) {
        echo '<div class="updated"><p>User updated successfully!</p></div>';
    } else {
        echo '<div class="error"><p>' . $user_id->get_error_message() . '</p></div>';
    }
}

// Handle delete user form submission
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $result = wp_delete_user($user_id);

    if ($result) {
        echo '<div class="updated"><p>User deleted successfully!</p></div>';
    } else {
        echo '<div class="error"><p>Error deleting user.</p></div>';
    }
}

            // Display edit form
            echo '<div class="row mt-4">';
            echo '<div class="col-md-8 mx-auto bg-light p-4">';
            echo '<h3>Edit User Profile</h3>';
            echo'<div class="row">';
            echo'<div class="col-md-8 mx-auto ">';
            echo '<form method="post" class ="form-control p-5">';
            echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
            echo '<p class="row"><label class="col-md-3" for="username">Username:</label>
             <input class="col-md-8" type="text" name="username" id="username" value="' . $user_data->user_login . '" required></p>';
            echo '<p class="row"><label class="col-md-3" for="email">Email:</label>
             <input class="col-md-8" type="email" name="email" id="email" value="' . $user_data->user_email . '" required></p>';
            echo '<p class="row"><label class="col-md-3" for="first_name">First Name:</label>
             <input class="col-md-8" type="text" name="first_name" id="first_name" value="' . $user_data->first_name . '"></p>';
            echo '<p class="row"><label class="col-md-3" for="last_name">Last Name:</label>
             <input class="col-md-8" type="text" name="last_name" id="last_name" value="' . $user_data->last_name . '"></p>';
            echo '<p class="row"><label class="col-md-3" for="phone_number">Phone Number:</label>
             <input class="col-md-8" type="text" name="phone_number" id="phone_number" value="' . $user_data->phone_number . '"></p>';
            echo '<p class="row"><label class="col-md-3" for="linkedin">LinkedIn:</label>
             <input class="col-md-8" type="text" name="linkedin" id="linkedin" value="' . $user_data->linkedin . '"></p>';
            echo '<p class="row"><label class="col-md-3" for="gender">Gender:</label> ';
            echo '<input type="radio" name="gender" value="male" ' . ($user_data->gender == 'male' ? 'checked' : '') . '> Male ';
            echo '<input type="radio" name="gender" value="female" ' . ($user_data->gender == 'female' ? 'checked' : '') . '> Female ';
            echo '<input type="radio" name="gender" value="other" ' . ($user_data->gender == 'other' ? 'checked' : '') . '> Other</p>';
            echo '<p class="row"><label class="col-md-3" for="hobbies">Hobbies:</label> <textarea class="col-md-8" name="hobbies" id="hobbies">' . $user_data->hobbies . '</textarea></p>';
            echo '<p class="row"><input class="btn btn-secondary w-25" type="submit" name="update_user" value="Update User"></p>';
            echo '</form>';
            echo '</div>';
            echo '</div>';

            // Display delete button
            echo '<div class="row mt-4">';
            echo '<div class="col-md-8 mx-auto bg-light p-4">';
            echo '<h3>Delete User</h3>';
            echo '<form method="post">';
            echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
            echo '<p class="row">Are you sure you want to delete this user?</p>';
            echo '<p class="row"><input type="submit" class="btn btn-danger w-25" name="delete_user" value="Delete User"></p>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<p class="row">User not found.</p>';
        }
    } else {
        echo '<p>No user ID provided.</p>';
    }





}



// function custom_add_profile_submenu() {
//     add_submenu_page(
//         'custom-user-management', // Parent menu slug
//         'Manage Profiles', // Page title
//         'Manage Profiles', // Menu title
//         'manage_options', // Capability required to access the page
//         'custom-manage-profiles', // Menu slug
//         'custom_manage_user_profiles_page' // Callback function to render the page content
//     );
// }
// Add menu page for creating users and viewing user list




function custom_user_management_menu() {
    add_menu_page(
        'User Management',
        'User Management',
        'manage_options',
        'custom-user-management',
        'custom_create_user_page'
    );

    add_submenu_page(
        'custom-user-management',
        'All Users',
        'All Users',
        'manage_options',
        'custom-user-list',
        'custom_user_list'
    );
      add_submenu_page(
        'custom-user-management', // Parent menu slug
        'Manage Profiles', // Page title
        'Manage Profiles', // Menu title
        'manage_options', // Capability required to access the page
        'custom-manage-profiles', // Menu slug
        'custom_manage_user_profiles_page' // Callback function to render the page content
    );
      add_submenu_page(
        'custom-user-management',
        'Voting Submenu',
        'Voting Submenu',
        'manage_options',
        'custom-voting-submenu',
        'custom_voting_submenu_page'
    );
       add_submenu_page(
        'custom-user-management', // Parent menu slug
        'Create Ad', // Page title
        'Create Ad', // Menu title
        'manage_options', // Capability required to access the page
        'create-ad', // Menu slug
        'create_ad_page' // Callback function to render the page content
    );
    add_submenu_page(
        'custom-user-management',
        'ViewAd Submenu',
        'ViewAd',
        'manage_options',
        'custom-viewadd-submenu',
        'custom_viewadd_submenu_page'
    );
}
// Handle AJAX request for sending emails
add_action('wp_ajax_send_emails_ajax', 'send_emails_ajax');
function send_emails_ajax() {
    $selectedUsers = isset($_POST['selected_users']) ? $_POST['selected_users'] : array();
    
    // Loop through selected user IDs and send emails
    foreach ($selectedUsers as $userId) {
        $user = get_userdata($userId);
        $to = $user->user_email;
        $subject = 'Your subject here';
        $message = 'Your message here';
        wp_mail($to, $subject, $message);
    }
    wp_die(); // Always include this line to terminate the script properly
}

add_action('admin_init', 'handle_voting_submission');

function handle_voting_submission() {
    if(isset($_POST['upvote_user']) || isset($_POST['downvote_user'])) {
        $user_id = $_POST['user_id'];
        $user_votes = get_user_meta($user_id, 'user_votes', true);

        if(empty($user_votes)) {
            $user_votes = 0;
        }

        if(isset($_POST['upvote_user'])) {
            $user_votes++;
        } elseif(isset($_POST['downvote_user'])) {
            $user_votes--;
        }

        // Update user's voting score
        update_user_meta($user_id, 'user_votes', $user_votes);
    }
}

// Voting submenu page
function custom_voting_submenu_page() {
    echo '<h2 class="text-center">Voting Submenu</h2>';
    echo '<p>Select a user to vote:</p>';
    custom_user_voting(); // Display user list with voting options
}

function custom_user_voting() {
    $users = get_users();

    echo '<h2 class="text-center">Select User </h2>';
    echo '<form method="post" action="">';
    echo '<div class="row">';
    echo '<div class="col-md-8 mx-auto">';
    echo '<ul class="list-group">';
    foreach ($users as $user) {
        $user_id = $user->ID;
        $user_login = esc_html($user->user_login);
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
        echo '<span><a href="admin.php?page=custom-voting-submenu&user_id=' . $user_id . '">' . $user_login . '</a></span>';
        echo '<button class="btn btn-primary btn-sm" onclick="voteUser(' . $user_id . ', \'' . $user_login . '\')">Vote</button>';
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
}



function add_ads_submenu() {
   
}

global $wpdb;
define('AD_TABLE_NAME', $wpdb->prefix . 'ads');

function create_ads_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = AD_TABLE_NAME;
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ad_name varchar(255) NOT NULL,
        ad_description text NOT NULL,
        image_url varchar(255) NOT NULL,
        user_id mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'create_ads_table' );

// Create ad page

function create_ad_page() {
    global $wpdb;


    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle form data and save to database
        $table_name = AD_TABLE_NAME;
        $ad_name = $_POST['ad_name'];
        $ad_description = $_POST['ad_description'];
        $image_url = ''; // Initialize image_url
        if (isset($_FILES['image_url'])) {
            $file = $_FILES['image_url'];
            $upload_dir = wp_upload_dir(); // Get upload directory
            $target_dir = $upload_dir['path'] . '/';
            $target_file = $target_dir . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $image_url = $upload_dir['url'] . '/' . basename($file['name']);
            } else {
                echo '<div class="error"><p>Failed to upload image.</p></div>';
            }
        }
        $user_id = get_current_user_id(); // Get current logged in user ID
        $wpdb->insert(
            $table_name,
            array(
                'ad_name' => $ad_name,
                'ad_description' => $ad_description,
                'image_url' => $image_url,
                'user_id' => $user_id
            )
        );
         
        echo '<div class="updated"><p>Ad created successfully!</p></div>';
    }
    ?>
    <div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card mx-auto d-flex justify-content-center ">
            <h2 class="text-center">Create Ad</h2>
            <form class="form-control p-4" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label" for="ad_name">Ad Name:</label><br>
                <input type="text" class="form-control"  id="ad_name" name="ad_name"><br>
            </div>
            <div class="mb-3">
                <label class="form-label"  for="ad_description">Ad Description:</label><br>
                <textarea class="form-control" id="ad_description" name="ad_description"></textarea><br>
            </div>
            <div class="mb-3">
                <label class="form-label" for="image_url">Image:</label><br>
                <input class="form-control" type="file" id="image_url" name="image_url"><br>
            </div>
                <input class="btn btn-success" type="submit" value="Create Ad">
            </form>
        </div>
    </div>
</div>
    <?php
}

// Function to generate shortcode for listing ads
// Function to handle editing an ad


// Function to handle deleting an ad
// Function to handle viewing a single ad
// Function to handle viewing all ads
function custom_viewadd_submenu_page() {
    global $wpdb;

    // Check if the constant AD_TABLE_NAME is defined
    if (!defined('AD_TABLE_NAME')) {
        echo '<div class="error"><p>AD_TABLE_NAME constant is not defined.</p></div>';
        return;
    }

    // Fetch all ads from the database
    $table_name = $wpdb->prefix . 'ads'; // Assuming AD_TABLE_NAME constant is defined elsewhere
    $ads = $wpdb->get_results("SELECT * FROM $table_name");

    // Display ads list
    if ($ads) {
        echo '<h2>All Ads</h2>';
        echo '<div class="row">';
        echo '<div class="col-md-8 mx-auto">';
        echo '<table class="table table-bordered table-hover p-3">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Name</th>';
        echo '<th>Description</th>';
        echo '<th>Image</th>';
        echo '<th>Actions</th>'; // Actions column for Update and Delete buttons
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($ads as $ad) {
            echo '<tr>';
            echo '<td>' . esc_html($ad->ad_name) . '</td>';
            echo '<td>' . esc_html($ad->ad_description) . '</td>';
            echo '<td><img class="rounded-circle" style="width: 50px; height: 50px;" alt="Avatar" src="' . esc_url($ad->image_url) . '" alt="Ad Image"></td>';
            echo '<td  colspan="4" class="d-flex">';
            // Update Form
            echo '<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            ';
            echo '<div class="modal-dialog">';
            echo ' <div class="modal-content p-5">';
            echo '<form class="form-control mb-2" method="post" action="">';
            echo '<input class="form-control mb-2" type="hidden" name="ad_id" value="' . $ad->id . '">'; // Hidden field to store ad ID
            echo '<input class="form-control mb-2" type="text" name="new_name" value="' . esc_attr($ad->ad_name) . '">'; // Input field for new name
            echo '<input class="form-control mb-2" type="text" name="new_description" value="' . esc_attr($ad->ad_description) . '">'; // Input field for new description
            echo '<input class="form-control mb-2" type="file" name="new_image_url" value="' . esc_attr($ad->image_url) . '">'; // Input field for new image URL
          
            echo '<input class="btn btn-secondary" type="submit" name="update_ad" value="Update">'; // Submit button for update
            echo '</form>';
            echo'</div>';
            echo'</div>';
            echo'</div>';

            echo '<button class = "btn btn-secondary mx-3 h-100" data-bs-toggle="modal" data-bs-target="#staticBackdrop"  >Update </button>'; // Submit button for update
          
            // echo '<button class = "btn btn-danger"  >Delete </button>'; // Submit button for update
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="ad_id" value="' . $ad->id . '">'; // Hidden field to store ad ID
            echo '<input class = "btn btn-danger" type="submit" name="delete_ad" value="Delete" onclick="return confirm(\'Are you sure you want to delete this ad?\')">'; // Submit button for delete
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="error"><p>No ads found.</p></div>';
    }

    // Handle form submission for updating ad
    if (isset($_POST['update_ad'])) {
        $ad_id = $_POST['ad_id']; // Get ad ID from the form
        $new_name = $_POST['new_name']; // Get new name from the form
        $new_description = $_POST['new_description']; // Get new description from the form
        $new_image_url = $_POST['new_image_url']; // Get new image URL from the form

        // Update the ad data in the database
        $wpdb->update(
            $table_name,
            array(
                'ad_name' => $new_name,
                'ad_description' => $new_description,
                'image_url' => $new_image_url
            ),
            array('id' => $ad_id)
        );

        // Redirect or display a success message as needed
    }
    if (isset($_POST['delete_ad'])) {
        $ad_id = $_POST['ad_id']; // Get ad ID from the form

        // Delete the ad from the database
        $wpdb->delete(
            $table_name,
            array('id' => $ad_id)
        );

        // Redirect or display a success message as needed
    }
}






// Register the shortcode
add_shortcode('ads_list', 'ads_list_shortcode');


// Hook functions
add_action('admin_menu', 'custom_user_management_menu');
add_action('admin_init', 'handle_email_submission');

add_action('admin_menu', 'add_ads_submenu');





