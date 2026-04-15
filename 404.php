<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Beech_ACF_Boilerplate
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found">
			<header class="page-header search-header">
					<?php
					/* translators: %s: search query. */
					get_template_part( 'template-parts/components/nav-search','', array('location' => 'header', 'results' => 0, 'query' => get_search_query()) ); 
					?>

			</header><!-- .page-header -->

			<section class="page-content container block grid">
				<div class="span__8">
					<?= the_acf_content(get_field('404_content', 'options')); ?>
				</div>
			</section>

			
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
