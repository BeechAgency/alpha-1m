<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Beech_ACF_Boilerplate
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-5CWLT1XGKY"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'G-5CWLT1XGKY');
	</script>

	<?php wp_head(); ?>
</head>

<body <?php 
	$classes = array('debug', 'active:gradient-1');
	$post_type = get_post_type();
?>
 class="debug active:gradient-1">
<?php wp_body_open(); ?>
<div id="page" class="site debug">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'bfc' ); ?></a>
	<?php get_template_part('template-parts/nav/nav', null); ?>
