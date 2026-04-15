<?php

function the_image($imageId, $args = null) {
    if(empty($imageId)) return '';


    $size = !empty($args['size']) ? $args['size'] : 'full';
    $alt = !empty($args['alt']) ? $args['alt'] : '';
    $classes = !empty($args['classes']) ? $args['classes'] : '';
    $onClick = !empty($args['onClick']) ? $args['onClick'] : '';

    $loading = !empty($args['loading']) ? $args['loading'] : 'lazy';

    $img_src = wp_get_attachment_image_url( $imageId, $size );
    $img_src_lqip = wp_get_attachment_image_url( $imageId, 'lqip' );
    $img_srcset = wp_get_attachment_image_srcset( $imageId, $size );

    $focal_point = get_post_meta( $imageId, '_focal_point', true );

    $focal_string = '';


    //echo '<pre>'; print_r($img_src_lqip); echo '</pre>';

    if(!empty($focal_point)) {
        $focal_string = 'data-focal-x="true" style="--_focal-x: '. round($focal_point['x'],1) .'%; --_focal-y: '. round($focal_point['y'],1) .'%;" ';
    }
    $img = "<img data-src='".esc_url( $img_src )."' src='".esc_url( $img_src_lqip )."' srcset='".esc_attr( $img_srcset )."' sizes='(max-width: 161rem) 100vw, 680px' alt='".esc_attr( $alt )."' loading='".$loading."' class='lazy ".esc_attr( $classes )."'";
    $img .= $focal_string;
    $img = $onClick !== '' ? $img .= 'onClick="'.esc_attr($onClick).'" />' : $img.'/>';

    return $img;
}

function the_acf_content( $content ) {
    if(empty($content)) return '';
    return '<div class="prose">'.apply_filters('the_content', $content ).'</div>';
}


/* Handle video URL */
function the_video($url, $args = array()) {
    if(empty($url)) return;

    $type = !empty($args['type']) ? $args['type'] : 'url';
    $poster_image = !empty($args['image']) ? $args['image'] : '';
    $classes = !empty($args['classes']) ? $args['classes'] : '';

    $post_url = !empty($poster_image) ? wp_get_attachment_image_url( $poster_image, 'full' ) : '';
    $post_url_lqip = !empty($poster_image) ? wp_get_attachment_image_url( $poster_image, 'lqip' ) : '';

    if($type === 'url' || $type === 'direct') {
        $output = '<div class="lazy-video">';
        $output .= "<video class='video $classes' muted loop playsinline data-src='$post_url' poster='$post_url_lqip' preload='none' loading='lazy'><source src='$url' type='video/mp4'></video>";
        $output .= '<img class="video-poster lazy" src="'.$post_url_lqip.'" data-src="'.$post_url.'" alt="Video poster" />';
        $output .= '</div>';
        return $output;
    }
    elseif($type === 'youtube') {
        
        $id = get_youtube_id($url);
        return "<iframe class='video youtube $classes' id='video' width='100%' height='' src='https://www.youtube.com/embed/$id?rel=0&modestbranding=1&controls=0&color=009999' title='YouTube video player' frameborder='0' allow='autoplay; accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";

    } elseif($type === 'vimeo-auto') { //347119375 // 107178137

        $id = get_vimeo_id($url);

        return "<iframe class='video vimeo $classes' id='video' width='100%' height='' src='https://player.vimeo.com/video/$id?title=0&portrait=0&byline=0&color=009999&background=1' title='Vimeo video player' frameborder='0' allow='autoplay; clipboard-write; encrypted-media; picture-in-picture' allowfullscreen></iframe>";

    } elseif($type === 'vimeo') { //347119375 // 107178137

        $id = get_vimeo_id($url);

        return "<iframe class='video vimeo $classes' id='video' width='100%' height='' src='https://player.vimeo.com/video/$id?title=0&portrait=0&byline=0&color=009999&background=0' title='Vimeo video player' frameborder='0' allow='autoplay; clipboard-write; encrypted-media; picture-in-picture' allowfullscreen></iframe>";

    } elseif($type === 'dropbox') {

        $rep = str_replace('www.dropbox.com', 'dl.dropboxusercontent.com', $url);
        //$repParts = explode("?", $rep);
        $rep = $rep . '&dl=0';

        return "<video class='video $classes' autoplay muted loop playsinline poster='$post_url'  preload='none' loading='lazy'><source src='$rep' type='video/mp4'></video>";

    } else {
        return $url;
    }
}


/**
 * Generates a call-to-action (CTA) HTML element based on the provided arguments.
 *
 * @param array $args {
 *     An array of arguments containing the following keys:
 *     - url (string): The URL of the CTA link.
 *     - title (string): The title of the CTA link.
 *     - classes (string): Additional CSS classes to apply to the CTA element. Defaults to an empty string.
 *     - type (string): The type of CTA, either 'text' or 'pill'. Defaults to 'text'.
 *     - align (string): The alignment of the CTA element. Defaults to an empty string.
 *     - target (string): The target attribute of the CTA link.
 * }
 *
 * @return string|false The generated CTA HTML element, or false if the required arguments are not provided.
 */
function the_cta($args) {
    if(empty($args['url']) || empty($args['title'])) return false;
    if(empty($args['classes'])) $args['classes'] = '';
    if(empty($args['type'])) $args['type'] = 'text'; // is either text or pill

    $url = $args['url'];
    $title = $args['title'];
    $classes = $args['classes'];
    $type = $args['type'];
    $target = !empty($args['target']) ? $args['target'] : '_self';

    $alignment = !empty($args['align']) ? ' align-'.$args['align'] : '';

    $classes .= ' cta__'.$type.' '.$alignment;

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
    <path d="M0 10.3532L1.02152 7.23039L7.67261 9.37704L3.56199 3.75692L6.23638 1.82681L10.347 7.44692V0.5H13.6527V7.44692L17.7636 1.82681L20.438 3.75692L16.3271 9.37704L22.9785 7.23039L24 10.3532L17.3489 12.4999L24 14.6468L22.9785 17.7696L16.3271 15.623L20.438 21.2431L17.7636 23.1732L13.6527 17.5531V24.5H10.347V17.5531L6.23638 23.1732L3.56199 21.2431L7.67261 15.623L1.02152 17.7696L0 14.6468L6.65108 12.4999L0 10.3532Z" fill="white"/>
    </svg>';

    return '<a href="'.$url.'" class="cta '.$classes.'" target="'.$target.'" ><span class="icon">'.$svg.'</span><span class="text">'.$title.'</span></a>';
}


if ( ! function_exists( 'the_post_date') ) :
/**
 * Prints HTML with meta information for the current post-date/time.
 */
function the_post_date( $id = null ) {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U', $id ) !== get_the_modified_time( 'U' , $id ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr( get_the_date( DATE_W3C, $id  ) ),
        esc_html( get_the_date( null, $id ) ),
        esc_attr( get_the_modified_date( DATE_W3C, $id ) ),
        esc_html( get_the_modified_date( null, $id ) )
    );

    $posted_on = sprintf(
        /* translators: %s: post date. */
        esc_html_x( '%s', 'post date', 'bfc' ),
        '' . $time_string . ''
    );


    echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
endif;

if ( ! function_exists( 'the_post_author') ) :
function the_post_author() {
    $byline = sprintf(
        /* translators: %s: post author. */
        esc_html_x( 'by %s', 'post author', 'bfc' ),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
    );

    echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
endif;

if ( ! function_exists( 'the_social_sharers' ) ) :

	function the_social_sharers() {
		get_template_part('template-parts/components/social', 'sharer');
	}

endif;


if ( ! function_exists( 'the_number_pagination') ) :
	/**
	* Displays A List of Posts
	*/
	function the_number_pagination() {
		$args = array(
			'base'               => '%_%',
			'format'             => '?paged=%#%',
			'total'              => 1,
			'current'            => 0,
			'show_all'           => false,
			'end_size'           => 1,
			'mid_size'           => 2,
			'prev_next'          => true,
			'prev_text'          => __('«'),
			'next_text'          => __('»'),
			'type'               => 'plain',
			'add_args'           => false,
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => ''
		);
		
		global $wp_query;
		$big = 9999999; // need an unlikely integer

        

		
		echo '<div class="posts-pagination">';
		echo paginate_links( 
			array(
			   'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			   'format' => '?paged=%#%',
			   'current' => max( 1, get_query_var('paged') ),
			   'total' => $wp_query->max_num_pages,
			   'prev_text' => __('<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none"><path d="M9.83673 3L11.1837 4.35714L5.63848 10.0179L20 10.0179L20 11.9821L5.63848 11.9821L11.1837 17.625L9.83673 19L2 11L9.83673 3Z" fill="currentColor"/></svg>'),
			   'next_text' => __('Next')
			)
		);
		echo '</div>';
	}	/*»«*/

endif;

function the_custom_title( $id = null ) {
    $acf_header_text = get_field('header_text', $id);
    $acf_title = !empty($acf_header_text) ? $acf_header_text['title'] : false;

    if(!empty($acf_title)) {
        return $acf_title;
    }

    $title = get_the_title($id);

    return $title;
}

function the_logo( $args = array() ) {
    $variant = !empty($args['variant']) ? $args['variant'] : 'default';

    ?>
    <svg width="67" height="41" viewBox="0 0 67 41" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M45.0353 4.66312C45.8331 3.77669 46.7195 3.04539 47.6281 2.46921C49.2236 1.47198 50.9079 0.940125 52.6364 0.940125V15.411C51.3732 11.0232 48.6475 7.25591 45.0353 4.66312ZM66.5533 40.9401H15.2957C6.87461 40.9401 0.0712891 34.1146 0.0712891 25.7157C0.0712891 17.6714 6.3206 11.0675 14.232 10.5135V0.940125C16.0048 0.940125 17.7555 1.44982 19.3954 2.46921C20.304 3.02323 21.1904 3.75453 21.9882 4.59663C25.2458 2.31409 29.1904 0.984446 33.4674 0.984446C33.4674 10.2254 30.1433 20.9734 19.3289 20.9955H33.3566C32.9577 19.2005 31.3178 17.8709 29.3677 17.8487H37.5228C35.5727 17.8487 33.9328 19.2005 33.5339 21.0177H46.6087C49.2236 21.0177 51.8164 21.5274 54.2541 22.5468C56.6696 23.544 58.8857 25.0288 60.725 26.8681C62.5865 28.7296 64.0491 30.9235 65.0464 33.339C66.0436 35.7324 66.5533 38.3252 66.5533 40.9401ZM22.8525 10.7795C23.1849 11.6437 24.0713 12.6188 25.3123 13.3279C26.5533 14.0371 27.8386 14.3252 28.7472 14.1922C28.4148 13.3279 27.5284 12.3529 26.2874 11.6437C25.0464 10.9346 23.761 10.6465 22.8525 10.7795ZM41.5117 13.3279C40.2707 14.0371 38.9854 14.3252 38.0768 14.1922C38.4092 13.3279 39.2957 12.3529 40.5367 11.6437C41.7777 10.9346 43.063 10.6465 43.9716 10.7795C43.6613 11.6437 42.7527 12.6188 41.5117 13.3279Z" fill="currentColor"></path>
    </svg>
    <?php
}



/**
 * Simplfy the output of post terms
 */
function the_post_terms($post_id, $taxonomy, $link_terms = true, $hide_empty = false) {
    // Get terms for the specified taxonomy related to the post
    $terms = get_the_terms($post_id, $taxonomy);

    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }

    // Initialize an array to store term names
    $term_list = array();

    foreach ($terms as $term) {
        // Skip empty terms if requested
        if ($hide_empty && $term->count == 0) {
            continue;
        }

        $term_name = esc_html($term->name);

        if ($link_terms) {
            // Generate link to term archive page
            $term_link = esc_url(get_term_link($term->term_id, $taxonomy));
            $term_list[] = '<a href="' . $term_link . '">' . $term_name . '</a>';
        } else {
            // Just the term name without a link
            $term_list[] = $term_name;
        }
    }

    return $term_list;
}

function the_post_terms_list($post_id, $taxonomy, $link_terms = true, $hide_empty = false) {
    $terms_list = the_post_terms($post_id, $taxonomy, $link_terms, $hide_empty);

    // Convert the array to a comma-separated string
    return implode(', ', $term_list);
}

function the_post_categories($post_id, $link_categories = true) {
    return the_post_terms($post_id, 'category', $link_categories);
}

function the_post_categories_list($post_id, $link_categories = true) {
    return the_post_terms_list($post_id, 'category', $link_categories);
}

/**
 * Handle the instance where you need to output a block somewhere
 */

 function the_block( $name = '', $args = array() ) {
    if(empty($name)) return false;

    $args['block'] = 'block__'.$name;

    get_template_part('template-parts/blocks/block', null, $args);
 }


/**
 * Breadcrumbs
 */
function the_breadcrumbs() {
    $separator = '<svg xmlns="http://www.w3.org/2000/svg" width="7" height="10" viewBox="0 0 7 10" fill="none">
        <path d="M1 0.5L6 5L1 9.5" stroke="currentColor" stroke-width="1.25"/>
    </svg>';
    $home_title = 'Home';

    // If you are on the homepage, don't show breadcrumbs
    if (is_front_page()) {
        return;
    }

    echo '<ul class="breadcrumbs">';

    // Home page link
    echo '<li class="breadcrumbs__item"><a href="' . get_home_url() . '">' . $home_title . '</a></li>';
    echo '<li class="breadcrumbs__separator">' . $separator . '</li>';

    if (is_single()) {
        $category = get_the_category();
        if (!empty($category)) {
            echo '<li class="breadcrumbs__item"><a href="' . get_category_link($category[0]->term_id) . '">' . $category[0]->cat_name . '</a></li>';
            echo '<li class="breadcrumbs__separator">' . $separator . '</li>';
        }
        echo '<li class="breadcrumbs__item">' . get_the_title() . '</li>';
    } elseif (is_page()) {
        if ($post->post_parent) {
            $ancestors = get_post_ancestors($post->ID);
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor) {
                echo '<li class="breadcrumbs__item"><a href="' . get_permalink($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
                echo '<li class="breadcrumbs__separator">' . $separator . '</li>';
            }
        }
        echo '<li class="breadcrumbs__item">' . get_the_title() . '</li>';
    } elseif (is_category()) {
        echo '<li class="breadcrumbs__item">' . single_cat_title('', false) . '</li>';
    } elseif (is_tag()) {
        echo '<li class="breadcrumbs__item">' . single_tag_title('', false) . '</li>';
    } elseif (is_day()) {
        echo '<li class="breadcrumbs__item">' . get_the_time('Y') . '</li>';
        echo '<li class="breadcrumbs__separator">' . $separator . '</li>';
        echo '<li class="breadcrumbs__item">' . get_the_time('F') . '</li>';
        echo '<li class="breadcrumbs__separator">' . $separator . '</li>';
        echo '<li class="breadcrumbs__item">' . get_the_time('d') . '</li>';
    } elseif (is_month()) {
        echo '<li class="breadcrumbs__item">' . get_the_time('Y') . '</li>';
        echo '<li class="breadcrumbs__separator">' . $separator . '</li>';
        echo '<li class="breadcrumbs__item">' . get_the_time('F') . '</li>';
    } elseif (is_year()) {
        echo '<li class="breadcrumbs__item">' . get_the_time('Y') . '</li>';
    } elseif (is_author()) {
        echo '<li class="breadcrumbs__item">' . get_the_author() . '</li>';
    } elseif (is_search()) {
        echo '<li class="breadcrumbs__item">Search results for: ' . get_search_query() . '</li>';
    } elseif (is_404()) {
        echo '<li class="breadcrumbs__item">404 Not Found</li>';
    }

    echo '</ul>';
}
?>