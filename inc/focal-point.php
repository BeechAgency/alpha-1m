<?php
/**
 * PHP to support the focal point functionality for images
 *
 */
function enqueue_custom_media_scripts() {
    wp_enqueue_script( 'custom-media-scripts', get_template_directory_uri() . '/assets/js/admin/custom-media.js', array( 'jquery' ), null, true );
    wp_enqueue_style( 'custom-media-styles', get_template_directory_uri() . '/assets/css/admin/custom-media.css' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_custom_media_scripts' );


function add_focal_point_field( $form_fields, $post ) {
    $focal_point = get_post_meta( $post->ID, '_focal_point', true );
    $x = isset( $focal_point['x'] ) ? esc_attr( $focal_point['x'] ) : '50'; // Default to center
    $y = isset( $focal_point['y'] ) ? esc_attr( $focal_point['y'] ) : '50'; // Default to center

    $json = json_encode($focal_point);

    $form_fields['focal_point'] = array(
        'label' => 'Focal Point',
        'input' => 'html',
        'html'  => '
            <div class="image-editor">
            <!--  '.$json.' -->
                <img src="' . wp_get_attachment_url( $post->ID ) . '" class="details-image" style="position: relative; width: 100%; height: auto;">
                <div class="focal-point" style="position: absolute; width: 10px; height: 10px; background: red; border-radius: 50%; left: ' . $x . '%; top: ' . $y . '%;"></div>
                <input type="hidden" id="focal-point-id" name="focal_point_id" value="' . $post->ID . '" />
            </div>
            <div class="field-row">
            <p>
                <label for="focal-x">X:</label>
                <input type="text" id="focal-x" name="focal_x" value="' . $x . '">
            </p>
            <p>
                <label for="focal-y">Y:</label>
                <input type="text" id="focal-y" name="focal_y" value="' . $y . '">
            </p>
            </div>
        ',
    );
    
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'add_focal_point_field', 10, 2 );


/**
 * Saves the focal point data for an attachment post.
 *
 * @param int $post_id The ID of the attachment post.
 * @return void
 */
function save_focal_point_data() {
    // Verify user permissions (optional)
    if ( !current_user_can('edit_posts') ) {
        wp_send_json_error('Permission denied');
        return;
    }

    // Check if the data is set
    if ( !isset($_POST['focal_point']) || !isset($_POST['post_id']) ) {
        wp_send_json_error('Invalid data '. json_encode($_POST['focal_point']));
        return;
    }

    // Sanitize and validate input
    $focal_point = $_POST['focal_point'];
    $focal_point['x'] = floatval($focal_point['x']);
    $focal_point['y'] = floatval($focal_point['y']);

    $post_id = intval($_POST['post_id']);

    // Update post meta
    $result = update_post_meta($post_id, '_focal_point', $focal_point);

    if ($result) {
        wp_send_json_success('Focal point saved successfully: '.  json_encode($result));
    } else {
        wp_send_json_error('Failed to save focal point: ' .  json_encode($focal_point));
    }
}
add_action('wp_ajax_save_focal_point', 'save_focal_point_data');


function the_focal_point( $imageId ) {
    $focal_point = get_post_meta( $imageId, '_focal_point', true );
     if (isset($focal_point['x']) && isset($focal_point['y'])) {
        // Round x and y to one decimal place
        $focal_point['x'] = round($focal_point['x'], 1);
        $focal_point['y'] = round($focal_point['y'], 1);

        return $focal_point;
    }
    return false;
}