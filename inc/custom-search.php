<?php 
/**
 * bfc Custom Search
 */
function bfc_custom_search_query( $query ) {
    if ( $query->is_search && !is_admin() && $query->is_main_query() ) {

        // Filter by post types (works!)
        if ( isset( $_GET['post_type'] ) && !empty( $_GET['post_type'] ) ) {
            $query->set( 'post_type', $_GET['post_type'] );
        }

        
        // Filter by taxonomies
        $tax_query = array();
        if ( isset( $_GET['category'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $_GET['category']
            );
        }
        
        // Media types
        if ( isset( $_GET['media-type'] ) && !empty( $_GET['media-type'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'media-type',
                'field'    => 'slug',
                'terms'    => $_GET['media-type']
            );
        }
        
        // Media types
        if ( isset( $_GET['collection'] ) && !empty( $_GET['collection'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'collection',
                'field'    => 'slug',
                'terms'    => $_GET['collection']
            );
        }

        // Post tags
        if ( isset( $_GET['post_tag'] ) && !empty( $_GET['post_tag'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'slug',
                'terms'    => $_GET['post_tag']
            );
        }
        // Repeat for other taxonomies

        if ( !empty( $tax_query ) ) {
            $query->set( 'tax_query', $tax_query );
        }
        
        // Filter by author
        if ( isset( $_GET['author'] ) && !empty( $_GET['author'] ) ) {
            $authorIds = is_array($_GET['author']) ? $_GET['author'] : array($_GET['author']);
            
            // Meta query for display_author field
            $metaQuery = array('relation' => 'OR');
            foreach ($authorIds as $authorId) {
                $metaQuery[] = array(
                    'key'     => 'display_author',
                    'value'   => sprintf('(^|;)s:\d+:"%s";', $authorId), // Match serialized author ID
                    'compare' => 'REGEXP',
                );
            }

            // Set the meta_query in the main query
            $query->set('meta_query', $metaQuery);

            // Optionally include posts by the standard post author as well
            // $query->set('author__in', $authorIds);
        }
        
        // Filter by date range
        if ( !empty( $_GET['start_date'] ) || !empty( $_GET['end_date'] ) ) {
            $date_query = array();
            if ( !empty( $_GET['start_date'] ) ) {
                $date_query['after'] = $_GET['start_date'];
            }
            if ( !empty( $_GET['end_date'] ) ) {
                $date_query['before'] = $_GET['end_date'];
            }
            $query->set( 'date_query', array( $date_query ) );
        }
    }

}
add_action( 'pre_get_posts', 'bfc_custom_search_query' );


// AJAX handler for logged-in users
function handle_ajax_search() {
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    $args = array(
        //'post_type' => 'post', // Change this to your post type if needed
        's'         => $search_query,
        'posts_per_page' => 10,
    );

    $query = new WP_Query($args);

    $results = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = array(
                'title' => get_the_title(),
                'url'   => get_permalink(),
                'type'  => get_post_type()
            );
        }
    }

    wp_send_json($results);

    wp_reset_postdata();
}
add_action('wp_ajax_search', 'handle_ajax_search'); // For logged-in users
add_action('wp_ajax_nopriv_search', 'handle_ajax_search'); // For guests