<?php 


/**
 * Get the data required for the header.
 */
function get_header_data($pageId = null) {
    $header_data = array();

	if(is_single()) {
		// Do something here
	}

	if(is_search()) {
		// Do something here
	}

	if( is_tax() || is_category() || is_tag() ) {
		// Do something here
	}
	if(is_404()) {
		// Do something here
	}

	return $header_data;
}

/**
 * Get the data required for blocks.
 */
function get_block_fields($pageId = null) {
	$block_fields = array();
	return $block_fields;
}


function get_block_name($string) {
    // Split the string by "__"
    $parts = explode('__', $string);

    // Check if the string contains "__" and return the part after it
    if (isset($parts[1])) {
        return $parts[1];
    }

    // Return an empty string or handle the case where "__" is not found
    return '';
}

/**
 * Taxonomy Utils
 */

function get_tax( $slug, $postId = null, $prop = 'slug' ) {
	$postId = $postId ? $postId : get_the_ID();
	$terms = get_the_terms( $postId, $slug );
	
	if( !$terms || is_wp_error( $terms ) ) return '';

	if( $prop === 'name' ) return $terms[0]->name;
    if( $prop === 'id' || $prop === 'ID' ) return $terms[0]->term_id;
	
	return $terms[0]->slug;
}


function get_user_data($userId = null) {
	if(!$userId) return array();

    /** 
     * Add custom author fields here
     */

	$data = array(
		'id' => $userId,
		'bfc_role' => get_field('bfc_role', 'user_' . $userId),
		'image' => get_field('profile', 'user_' . $userId),
		'images'=> get_field('images', 'user_' . $userId),
		'name' => get_the_author_meta( 'display_name', $userId ),
		'url' => get_author_posts_url( $userId ),
		'social' => get_field('social', 'user_' . $userId),
		'expertise' => get_field('expertise', 'user_' . $userId),
		'headshots' => get_field('headshots', 'user_' . $userId),
		'about' => get_field('about', 'user_' . $userId),
		'description' => get_the_author_meta( 'description', $userId ),
        'display_email' => get_field('display_email', 'user_' . $userId)
	);

	if(!empty($data['images'])) {
		$data['image'] = $data['images']['profile'];
	}

	return $data;
}
function get_author_data($postId = null) {
	$postId = $postId ? $postId : get_the_ID();

	$author_id = get_field('display_author', $postId);

    //var_dump($author_id);
    
	if(!$author_id) return array();
	return get_user_data($author_id[0]);
}