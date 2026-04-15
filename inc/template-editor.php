<?php




// Callback function to insert 'styleselect' into the $buttons array
function bfc_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
// Register our callback to the appropriate filter
add_filter( 'mce_buttons_2', 'bfc_mce_buttons_2' );


function bfc_tiny_mce_before_init($init_array) {
    $style_formats = array(
        array(
            'title' => 'Eyebrow',
            'block' => 'h6',
            'classes' => 'eyebrow has-blue-color',
            'wrapper' => false,
        ),
        array(
            'title' => 'Highlighted Text',
            'inline' => 'span',
            'classes' => 'highlighted-text',
            'wrapper' => false,
        ),
        array(
            'title' => 'Text Button',
            'inline' => 'a',
            'classes' => 'cta cta__text',
            'wrapper' => false,
        ),
        array(
            'title' => 'Pill Button',
            'inline' => 'a',
            'classes' => 'cta cta__pill',
            'wrapper' => false,
        )
        // Add more styles here
    );

    // Add the styles to TinyMCE's style_formats
    $init_array['style_formats'] = json_encode($style_formats);

    return $init_array;
}

add_filter('tiny_mce_before_init', 'bfc_tiny_mce_before_init');



function bfc_theme_after_wp_tiny_mce() {
?>
    <script type="text/javascript">
		console.log('tinymce init')
        jQuery( document ).on( 'tinymce-editor-init', function( event, editor ) {
            tinymce.activeEditor.formatter.register( 'p-p2', {
                block : 'p',
                classes : 'p2',
				wrapper : true
            } );
        } );
    </script>
<?php
}
add_action( 'after_wp_tiny_mce', 'bfc_theme_after_wp_tiny_mce' );
/* read this and fix it up here https://wordpress.org/support/topic/wysiwyg-custom-buttons-not-showing/ */


/* Add color picker to the ACF WYSIWYG editor  */
add_filter( 'acf/fields/wysiwyg/toolbars' , 'bfc_add_acf_color'  );
function bfc_add_acf_color( $toolbars ) {
    array_unshift( $toolbars['Basic' ][1], 'forecolor' );
    return $toolbars;
}

if ( ! function_exists ( 'bfc_mce4_options' ) ) {
/* Default brand colors for MCE color picker */

	function bfc_mce4_options($init) {

		// Loop through THEME_COLORS and add them to the MCE color picker
		$THEME_COLORS = $GLOBALS['THEME_COLORS'];

		$custom_colours = "";

		foreach($THEME_COLORS as $name => $hex) {
			$custom_colours .= "'$hex',' $name',";
		}

		// build colour grid default+custom colors
		$init['textcolor_map'] = '['.$custom_colours.']';

		// change the number of rows in the grid if the number of colors changes
		// 8 swatches per row
		$init['textcolor_rows'] = 6;

		return $init;
	}

	add_filter('tiny_mce_before_init', 'bfc_mce4_options');

}


/** Users Admin Columns */
function add_display_name_column($columns) {
    // Add a new column after the 'username' column
    $columns['display_name'] = __('Display Name', 'textdomain');
    return $columns;
}
add_filter('manage_users_columns', 'add_display_name_column');

function display_name_column_content($value, $column_name, $user_id) {
    if ($column_name == 'display_name') {
        $user = get_userdata($user_id);
        return $user->display_name; // Return the display name of the user
    }
    return $value;
}
add_filter('manage_users_custom_column', 'display_name_column_content', 10, 3);



// Replace the Gravatar image with a custom field image in the user list
add_filter('get_avatar', 'replace_gravatar_with_custom_image', 10, 5);
function replace_gravatar_with_custom_image($avatar, $id_or_email, $size, $default, $alt) {
    // Check if we're on the admin user list page
    if (is_admin() && strpos($_SERVER['REQUEST_URI'], 'users.php') !== false) {
        // Get the user ID from the $id_or_email parameter
        $user_id = is_numeric($id_or_email) ? $id_or_email : (isset($id_or_email->ID) ? $id_or_email->ID : 0);

        // Get the custom field value (URL of the image)
        $custom_image = get_user_meta($user_id, 'images_profile', true);
        $custom_image_url = wp_get_attachment_url($custom_image);

        if ($custom_image_url) {
            // Replace the avatar with the custom image
            $avatar = '<img src="' . esc_url($custom_image_url) . '" alt="' . esc_attr($alt) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" style="border-radius:0%;">';
        }
    }
    return $avatar;
}

