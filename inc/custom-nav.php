<?php

/* THE MEGA MENU!!!! */
add_filter('wp_nav_menu_objects', 'bfc_wp_nav_menu_objects', 10, 2);

function bfc_wp_nav_menu_objects( $items, $args ) {
    // loop
    $slug = $args->menu->slug;

    if( $slug === 'header' ):
        foreach( $items as &$item ) {
            // vars
            $subtitle = get_field('subtitle', $item);
            $featured = get_field('featured', $item);

            $hasChildren = in_array('menu-item-has-children', $item->classes);

            $item->featured = $featured;
            $item->subtitle = $subtitle;

            $item->_children_count = 0;

            for($i=1, $l=count($items); $i<=$l; ++$i) {
                if($items[$i]->menu_item_parent == $item->ID) {
                    $item->_children_count++;
                }
            }   

            // If there is children append the class and related stuff.
            if( $hasChildren ) {
                $item->classes[] = 'child-count-'.$item->_children_count;
            }
            
            // Add the description text in. Navwalker will pick it up.
            /*
            if( $text ) {
                $item->description = $text;
            }
            */
            
            // append icon
            if( $featured ) {
                $item->classes[] = 'has-featured';
            }
            /*
            echo '<pre>';
            esc_html(var_dump( $item));
            echo '</pre>';
            */
        }


        /*echo '<pre>';
        esc_html(var_dump( $items));
        echo '</pre>';*/
    endif;
    
    // return
    return $items;
    
}

class bfc_Mega_Menu extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth=0, $args=[], $id=0) {
        $output .= "<li class='" .  implode(" ", $item->classes) . "'>";
 
        //$output .= '<div class="transition-div"></div>'; //Put your content here
        $has_children = $args->walker->has_children;
        $svg_arrow = '<svg class="mobile-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="17" viewBox="0 0 10 17" fill="none"><path d="M6.38889 8.50006L0.0694444 2.30006L1.80556 0.500061L10 8.50006L1.80556 16.5001L0 14.7001L6.31944 8.50006H6.38889Z" fill="currentColor"/></svg>';
        //$svg_arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="19.628" height="11.052" viewBox="0 0 19.628 11.052"><path id="Path_558" data-name="Path 558" d="M-2564.355-12657.974l9.2,9.2,9.2-9.2" transform="translate(2564.974 12658.593)" fill="none" stroke="currentColor" stroke-width="1.75"/></svg>';
        
        if ($item->url && $item->url != '#') {
            $output .= '<a href="' . $item->url . '">';
        } else {
            $output .= '<span>';
        }
 
        $output .= $item->title;
 
        if ($item->url && $item->url != '#') {
            if($has_children) {
                $output .= $svg_arrow;
            }
            $output .= '</a>';
        } else {
            $output .= '</span>';
        }
 
        if ($args->walker->has_children) {
            // Add the SVG Arrow
            $output .= '';
            $output .= '<div class="mega-menu__page depth-'.$depth.'">';
            
            if($depth === 0) {
                    $close_svg = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L13 13" stroke="currentColor" stroke-width="2"/><path d="M1 13L13 1" stroke="currentColor" stroke-width="2"/></svg>';
                    $mobile_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="17" viewBox="0 0 10 17" fill="none"><path d="M3.61111 8.50003L9.93056 14.7L8.19444 16.5L-2.54292e-07 8.50003L8.19445 0.500031L10 2.30003L3.68056 8.50003L3.61111 8.50003Z" fill="currentColor"/></svg>';
                    $output .= '<div class="mega-menu__page--inner">';
                    $output .= '<div class="mega-menu__page--aside"><h6 class="eyebrow">'. $item->subtitle.'</h6></div>';
                    $output .= '<div class="mega-menu__page--title"><h5><a href="'.$item->url.'">'.$item->title.'</a></h5><button class="cta cta__text cta__white"><span class="icon">'.$close_svg.'</span><span class="text">CLOSE MENU</span></button><button class="btn-back-mobile">'.$mobile_svg.'</button></div>';
            }

            if($depth === 0) {
                $output .= '</div><!-- Title End: '.json_encode($item->featured).' -->';
            }
        }
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}


        if( !empty($item->classes) && 
            is_array($item->classes) && 
            in_array('menu-item-has-children', $item->classes) ):

            // THANK YOU! https://wordpress.stackexchange.com/questions/198824/has-children-in-custom-nav-walker

            if($depth === 0 && !empty($item->featured)) {
                $output .= '<div class="mega-menu__page--featured"><div class="mega-menu__page--aside"><h6 class="eyebrow">Featured</h6></div>';
                ob_start(); // Start buffering
                get_template_part( 'template-parts/components/card/card', 'horizontal', array('post_id' => $item->featured) );

                $output .= ob_get_clean();

                $output .= '</div>'; 
            }

            $output .= '</div><!-- Depth:'.$depth.json_encode($item->featured).' -->';
        endif;


		$output .= "</li>{$n}";
	}
}