<?php 

function get_post_list( $args = array() ) {
        // Default values
        $defaults = array(
            'post'               => 'post',
            'number'             => 4,
            'posts'              => null,
            'featured'           => false,
            'types'              => null,
            'paged'              => false,
            'options'            => array(),
            'category'           => null,
            'tag'                => null,
            'offset'             => null,
            'author'             => null,
            'show_hidden'        => false,
            'post_id'            => get_the_ID(),
        );

        // Merge defaults with passed args
        $args = wp_parse_args( $args, $defaults );

        /*
        echo '<pre>';
        print_r($args);
        echo '</pre>';
        */

		$queryArgs = array(
            'posts_per_page' => $args['number'],
            'post_type'      => $args['post'],
            'offset'         => $args['offset']
        );

        if( !empty($args['paged']) ) {
            $queryArgs['paged'] = $args['paged'];
        }

        // Taxonomy filters
        $taxonomies = array(
            'category'           => 'category',
            'tag'                => 'post-tag',
        );

        $taxQuery = array();
        $taxNames = array();
        $termIds = array();

        foreach ($taxonomies as $argKey => $taxonomy) {
            if (!empty($args[$argKey])) {
                $terms = is_array($args[$argKey]) ? $args[$argKey] : array($args[$argKey]);

                $taxNames[] = $taxonomy;

                $termSlugs = array();
                foreach ($terms as $term) {
                    $termSlugs[] = get_term_slug_by_id($term, $taxonomy);
                    
                    $termIds[] = $term;
                }


                $taxQuery[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $termSlugs,
                    'operator' => 'IN',
                );
            }
        }


        if (!empty($taxQuery)) {
            $queryArgs['tax_query'] = $taxQuery;
        }


        // Specific post IDs
        if (!empty($args['posts'])) {
            $queryArgs['post__in'] = $args['posts'];
        }

        // Exclude the current post
        if (get_post_type() !== 'page' && !is_archive() ) {
            $queryArgs['post__not_in'] = array($args['post_id']);
        }

        // Author filtering
        if (!empty($args['author'])) {
            //var_dump($args['author']);
            $authorIds = is_array($args['author']) ? $args['author'] : array($args['author']);
            
            // Meta query for display_author field
            // Prepare regex pattern for each author ID
            $metaQuery = array('relation' => 'AND');

            foreach ($authorIds as $authorId) {
                $metaQuery[] = array(
                    'key'     => 'display_author',
                    'value'   => sprintf('(^|;)s:\d+:"%s";', $authorId), // Match serialized author ID
                    'compare' => 'REGEXP',
                );
            }
			
			//var_dump($metaQuery);

            $queryArgs['meta_query'] = $metaQuery;
            // Include posts by standard post author
            //$queryArgs['author__in'] = $authorIds;
        }

        // Hidden posts filtering
        /*
        if ( !$args['show_hidden'] ) {
            // Add a meta query to exclude hidden posts
            if ( !isset($queryArgs['meta_query']) ) {
                $queryArgs['meta_query'] = array(); // Initialize if not set
            }

            $queryArgs['meta_query'][] = array(
                'key'     => 'hidden',
                'value'   => '1', // Assuming hidden is stored as '1' for true
                'compare' => '!=', // Exclude hidden posts
            );

        }
        */
        // Now, actually call the query
		$postQuery = new WP_Query(
			$queryArgs
		);

		//wp_reset_postdata();

		return $postQuery;
}

function get_post_list_ids( $args = array() ) {
    // Collect post IDs
    $post_ids = array();

    $postQuery = get_post_list($args);

    if(isset($args['list_style']) && $args['list_style'] === 'popular') {
        foreach($postQuery as $post) {
            $post_ids[] = $post->id;
        }
    } else {
        if ($postQuery->have_posts()) {
            while ($postQuery->have_posts()) {
                $postQuery->the_post();
                $post_ids[] = get_the_ID();
            }
        }
    }

    wp_reset_postdata();

    return $post_ids;
}



function get_terms_by_taxonomy($taxonomy_slugs = array(), $args = array()) {
    // Initialize the include array
    $include = array();

    if(!isset($args['hide_empty'])) { 
        $args['hide_empty'] = true;
    }

    foreach($taxonomy_slugs as $tax) {
        if(!empty($args[$tax])) {
            $include = array_merge($include, $args[$tax]);
        }
    }
    
    // Set default args
    $defaults = array(
        'taxonomy'   => $taxonomy_slugs,
        'include'    => $include, // Use the extracted term IDs
        'number'     => isset($args['number']) ? $args['number'] : 0, // Number of terms to return
        'hide_empty' =>  $args['hide_empty'], // Whether to hide terms not assigned to any posts
    );

    // Merge user-provided args with defaults, excluding the original taxonomy-specific key
    $final_args = wp_parse_args(array(), $defaults);

    // Fetch the terms based on the final arguments
    $terms = get_terms($final_args);

    // Check for errors and return the terms array if successful
    if (!is_wp_error($terms)) {
        return $terms;
    }

    // If there's an error, return an empty array
    return array();
}

/**
 * Get terms from taxonomies
 */
function get_taxonomies_index( $taxonomies = [], $number = 4, $collection_terms = []) {
    if(empty($taxonomies)) {
        return array();
    }

    if (!is_array($taxonomies)) {
        $taxonomies = array($taxonomies);
    }

    // Determine if filtering by collection based on whether $collection_terms is non-empty
    $filter_by_collection = !empty($collection_terms);

    // Ensure $collection_terms is an array
    if (!is_array($collection_terms)) {
        $collection_terms = array($collection_terms);
    }

    // Initialize array to hold the terms
    $all_terms = [];

    // If filtering by a specific collection, get posts with those collection term IDs
    $post_ids_with_collection = [];
    if ($filter_by_collection) {
        $collection_query = new WP_Query([
            'post_type' => 'post', // Adjust post type if needed
            'tax_query' => [
                [
                    'taxonomy' => 'collection',
                    'field'    => 'term_ID', // Use term IDs instead of slugs
                    'terms'    => $collection_terms,
                ],
            ],
            'fields' => 'ids', // Only get post IDs
            'posts_per_page' => -1, // Get all posts with the collection term(s)
        ]);

        if ($collection_query->have_posts()) {
            $post_ids_with_collection = $collection_query->posts;
        }
    }
    

    foreach ($taxonomies as $taxonomy) {
        // Prepare arguments for get_terms
        $args = [
            'taxonomy'   => $taxonomy,
            'number'     => $number, // Request up to the total number, will be limited by available terms
            'hide_empty' => true, // Only return terms with a non-zero count
        ];

        // Custom logic for handling 'post_tag' variations
        if (strpos($taxonomy, 'post_tag__') === 0) {
            $is_person = null;

            //print_r($taxonomy);

            // Handle different cases for 'post_tag__tag' and 'post_tag__people'
            if ($taxonomy === 'post_tag__tag') {
                $is_person = false; // Exclude people
            } elseif ($taxonomy === 'post_tag__people') {
                $is_person = true; // Only include people
            }

            

            if (!is_null($is_person)) {
                // Join termmeta to filter by custom field is_person
                $args['meta_query'] = [
                    [
                        'key'   => 'is_person',
                        'value' => $is_person ? '1' : '0',
                        'compare' => '='
                    ]
                ];

                // Use the actual 'post_tag' taxonomy, but filtered by is_person
                $args['taxonomy'] = 'post_tag';

                // If filtering by collection, add an additional filter
                if ($filter_by_collection && !empty($post_ids_with_collection)) {
                    $args['object_ids'] = $post_ids_with_collection;
                }

                if($filter_by_collection && empty($post_ids_with_collection)) {
                    $args['object_ids'] = array('0');
                }

                //var_dump( $args['meta_query'] );
            }
        }

        // Fetch terms based on arguments
        $terms = get_terms($args);

        // Merge the fetched terms into the final array
        if (!is_wp_error($terms) && !empty($terms)) {
            $all_terms = array_merge($all_terms, $terms);
        }

        // Reduce the number by the terms we've added
        $number -= count($terms);

        // Stop if we've reached the requested number of terms
        if ($number <= 0) {
            break;
        }
    }

    return $all_terms;
}