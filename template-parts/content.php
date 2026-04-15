<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Beech_ACF_Boilerplate
 */

$defaults = array(
	'post_id' => get_the_ID(),
	'post_type' => 'post'
);

$type = 'post';

$front_page_id = get_option('page_on_front');

?>

<article id="post-<?= $post_id; ?>" <?php post_class('type-'.$post_type); ?>>
	
	
	<div class="entry-content__wrap container media-type__<?= $type ?> <?= !has_content() ? 'has-no-content' : '' ?>">
		<div class="entry-content container has-gutter grid">
			<div class="entry-content__inner start-4 span-6 lg:span-8 lg:start-3 md:start-auto md:span-full">
				<header>
					<h1 class="text-5xl"><?php echo get_field('church'); ?></h1>
					<hr />
				</header>
			<?php 
			the_content(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'bfc' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( the_custom_title() )
				)
			);
			?>
			</div>
		</div><!-- .entry-content -->
	</div><!-- .entry-content__wrap -->
	<?php get_template_part('template-parts/sections/gallery', null, array( 'post_id' => $front_page_id) ); ?>
</article><!-- #post-<?php the_ID(); ?> -->
