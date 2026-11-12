<?php
/**
 * Theme header.
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'min-h-screen bg-background' ); ?>>
<?php wp_body_open(); ?>
<?php if ( ! is_404() ) : ?>
	<?php get_template_part( 'template-parts/nav', 'editorial' ); ?>
<?php endif; ?>
