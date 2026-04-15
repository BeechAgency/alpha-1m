<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Beech_ACF_Boilerplate
 */

get_header();

$post_id = get_the_ID(); // Get the current post ID
$post_type = get_post_type();

?>

	<main id="primary" class="site-main swup-transition-fade">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type(), array('post_id' => $post_id, 'post_type' => $post_type) );


		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
