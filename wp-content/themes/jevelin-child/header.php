<?php
/**
 * Header
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<?php if( jevelin_option('responsive_layout') ) : ?>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php endif; ?>

	<?php
	/* Callback for older WordPress versions */
	if( !function_exists( 'has_site_icon' ) || ! has_site_icon() ) : ?>
		<?php if( jevelin_option_image('favicon') ) : ?>
			<link rel="icon" type="image/png" href="<?php echo jevelin_option_image('favicon'); ?>">
		<?php endif; ?>
	<?php endif; ?>

	<?php wp_head(); ?>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116837391-1"></script>
    
    <!-- Google tag (gtag.js) --> 
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-E91M0GTWQL"></script> <script>   window.dataLayer = window.dataLayer || [];   function gtag(){dataLayer.push(arguments);}   gtag('js', new Date());   gtag('config', 'G-E91M0GTWQL'); 
    </script>
    
</head>
<body <?php body_class(); ?>>
<?php
	/* Get Titlebar options */
	$titlebar_style_val = jevelin_post_option( jevelin_page_id(), 'header_style' );
	$titlebar_style_val2 = ( isset( $titlebar_style_val['header_style'] ) ) ? esc_attr($titlebar_style_val['header_style']) : 'default';

	/* Inlcude page loader HTML */
	get_template_part('inc/templates/page_loader' );

	/* Header popup search */
	if( jevelin_option( 'header_search_style', 'style1' ) != 'style1' ) :
		get_template_part('inc/headers/header-search');
	endif;
?>

	<?php
		if( jevelin_header_right() ) :
			/* Inlcude right header HTML */
			get_template_part('inc/headers/header-right-1' );
		endif;
	?>

	<div id="page-container" class="<?php echo jevelin_header_page_container(); ?>">
		<?php
			/* Inlcude page notice HTML */
			get_template_part('inc/templates/notice' );

			/* Inlcude breadcrumbs HTML, if bottom titlebar style */
			if( $titlebar_style_val2 == 'bottom_titlebar' ) :
				get_template_part('inc/templates/titlebar' );
			endif;
		?>


		<?php if( jevelin_post_option( jevelin_page_id(), 'header', 'on' ) != 'off' ) : ?>
			<header class="primary-mobile<?php echo jevelin_header_mobile_style(); ?>">
				<?php /* Inlcude mobile header */
					get_template_part('inc/headers/header-mobile' );
				?>
			</header>
			<header class="primary-desktop<?php echo jevelin_header_desktop_style(); ?>">
				<?php /* Inlcude desktop header */
					get_template_part('inc/headers/header-'.jevelin_header_layout() );
				?>
			</header>
		<?php endif; ?>


		<?php
			/* Inlcude breadcrumbs HTML, if not bottom titlebar style */
			if( $titlebar_style_val2 != 'bottom_titlebar' ) :
				get_template_part('inc/templates/titlebar' );
			endif;

			$class = '';
			if( jevelin_post_option( jevelin_page_id(), 'page_layout', 'default' ) != 'full' ) {
				$class = ' sh-page-layout-default';
			} else if( jevelin_post_option( jevelin_page_id(), 'page_layout', 'default' ) == 'full' ) {
				$class = ' sh-page-layout-full';
			}

		?>


			<div id="wrapper">
				<div class="content-container<?php echo esc_attr( $class ); ?>">
				<?php if( jevelin_post_option( jevelin_page_id(), 'page_layout', 'default' ) != 'full' ) : ?>
					<div class="container entry-content">
				<?php endif; ?>


				<?php
					/* Inlcude white borders option HTML */
					get_template_part('inc/templates/white_borders' );
				?>