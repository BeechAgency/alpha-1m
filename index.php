<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Beech_ACF_Boilerplate
 */

get_header();
$page_for_posts = get_option( 'page_for_posts' );
?>

	<main id="primary" class="site-main is-home swup-transition-fade">
		<?php 
			get_template_part( 'template-parts/header/header', null, array('post_id' => $page_for_posts) ); 
		?>
		<?php
		if ( have_posts() ) :

			echo '<section class="page-content container has-gutter block" data-block-style="cols-3 post-list" data-scroll>';
			echo '<div class="grid cols-3">';
			/* Start the Loop */
			$item_index = 0;
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'template-parts/components/card/card', null, array('position' => $item_index) );
				$item_index++;
			endwhile;
			echo '</div>';
			
			the_number_pagination();
			echo '</section>';

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
