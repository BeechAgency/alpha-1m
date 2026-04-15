<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Beech_ACF_Boilerplate
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php  
		get_template_part( 'template-parts/header', null, array() ); 
	?>

	<div class="page-content">

		<?php
		get_template_part('template-parts/sections/stories', null, array() );
		get_template_part('template-parts/sections/churches', null, array() );
		get_template_part('template-parts/sections/form', null, array() );
		get_template_part('template-parts/sections/cards', null, array() );
		get_template_part('template-parts/sections/gallery', null, array() );

		?>

	</div><!-- .page-content -->

</article><!-- #post-<?php the_ID(); ?> -->
