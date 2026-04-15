<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Beech_ACF_Boilerplate
 */

get_header();
?>

	<main id="primary" class="site-main is-archive">

		<?php if ( have_posts() ) : 
			
    		global $wp_query;
    		$total_results = $wp_query->found_posts;
			?>

			<header class="page-header search-header">
					<?php
					/* translators: %s: search query. */
					get_template_part( 'template-parts/components/nav-search','', array('location' => 'header', 'results' => $total_results, 'query' => get_search_query()) ); 
					?>

			</header><!-- .page-header -->
			<div class="container has-gutter search-results">
				<div class="grid cols__4 has-lines has-border-top inter">
			<?php
			/* Start the Loop */
			$i = 0;
			while ( have_posts() ) :
				the_post();

				if($i === 0) {
					get_template_part( 'template-parts/components/card/card', 'jumbo' );

					echo '</div><div class="grid cols__4 has-lines has-border-top inter posts-list has-lines-horizontal">';
				} else {
					get_template_part( 'template-parts/components/card/card' );
				}
				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */

				$i++;
			endwhile;
			echo '</div></div>';

			
			the_number_pagination();

		else :
			?>


			<header class="page-header search-header">
					<?php
					/* translators: %s: search query. */
					get_template_part( 'template-parts/components/nav-search','', array('location' => 'header', 'results' => 0, 'query' => get_search_query()) ); 
					?>
			</header><!-- .page-header -->
			<div class="container has-gutter no-results">
				<p>Sorry, but nothing matched your search terms. Please try again with some different keywords or a different combination of filters.</p>
			</div>

			<?php 
		endif;
		?>

	</main><!-- #main -->

<?php

get_footer();
