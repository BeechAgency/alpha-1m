<?php
/**
 * Utility functions used throughout the theme.
 *
 * @package Beech_ACF_Boilerplate
 */

 /**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function bfc_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		//$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		//$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'bfc_body_classes' );


function bfc_human_readable_filesize($bytes, $decimals = 2) {
    $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
}

function bfc_get_file_extension_from_url($url) {
    // Parse the URL to get the path component
    $path = parse_url($url, PHP_URL_PATH);

    // Extract the file extension
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    return $extension;
}

function bfc_calculate_reading_time($content = null, $post_id = null) {
    if (empty($content)) {
        $content = get_the_content($post_id);
    }
    // Get the word count of the content
    $word_count = str_word_count(strip_tags($content));

    // Average reading speed in words per minute
    $words_per_minute = 200;

    // Calculate the reading time in minutes
    $reading_time = ceil($word_count / $words_per_minute);

    return $reading_time;
}

 function convert_to_camel_case($string) {
    $string = preg_replace('/[^a-zA-Z0-9]/', ' ', $string);
    $string = ucwords($string);
    $string = str_replace(' ', '', $string);
    $string = lcfirst($string);
    return $string;
}

function format_time($seconds) {
    if(empty($seconds)) return '';
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;

    $formattedTime = '';
    
    if ($minutes > 0) {
        $formattedTime .= $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ';
    }
    
    $formattedTime .= $remainingSeconds . ' sec' . ($remainingSeconds != 1 ? 's' : '');
    
    return trim($formattedTime);
}


/* Youtube ID */
function get_youtube_id($url) {

    $parsedUrl = parse_url($url);
    
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $query);
        if (isset($query['v'])) {
            return $query['v'];
        }
    }
    
    $path = ltrim($parsedUrl['path'], '/');

    if (strpos($parsedUrl['host'], 'youtu.be') !== false) {
        return $path;
    }
    
    return null;
}

/**
 * Get the Vimeo ID from a URL
 */
function get_vimeo_id($url) {
    if(empty($url)) return '';
    if(!str_contains( $url, '/' )) return $url;

    $exploded = explode('?', $url);
    $exploded = explode('/', $exploded[0]);

    $length = count($exploded);

    if($length !== 4) return '';

    return $exploded[$length - 1];
}


/**
 * Check if the post has content
 */
function has_content() {
    $content = get_the_content();
    $clean_content = trim( wp_strip_all_tags( strip_shortcodes( $content ) ) );

    if( !empty( $clean_content ) ) return true;

    $transcript = get_field('video_transcript');
    if( !empty( $transcript ) ) return true;
    
    return false;
}