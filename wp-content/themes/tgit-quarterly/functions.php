<?php
/**
 * TGIT Quarterly theme � React UI port (no React runtime).
 *
 * @package TGIT_Quarterly
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TGIT_QUARTERLY_VERSION', '1.0.0' );
require_once get_stylesheet_directory() . '/custom/api/class-api-authentication.php';
require_once get_stylesheet_directory() . '/custom/api/class-api-response.php';

require_once get_template_directory() . '/inc/svg-icons.php';
require_once get_template_directory() . '/inc/editorial-options.php';
require_once get_template_directory() . '/inc/editorial-cpt.php';
require_once get_template_directory() . '/inc/editorial-queries.php';
require_once get_template_directory() . '/inc/project-submit.php';
require_once get_template_directory() . '/inc/email-archive-menu.php';

/**
 * Public URL for a file under /assets/img/.
 */
function tgit_quarterly_img_uri( string $filename ): string {
	return get_template_directory_uri() . '/assets/img/' . ltrim( $filename, '/' );
}

/**
 * Theme defaults.
 */
function tgit_quarterly_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'header' => __( 'Header', 'tgit-quarterly' ),
			'footer' => __( 'Footer', 'tgit-quarterly' ),
			'mobile' => __( 'Pyxis Mobile Menu', 'tgit-quarterly' ),
		)
	);
}
add_action( 'after_setup_theme', 'tgit_quarterly_setup' );

/**
 * Default section links used before a WordPress menu is assigned.
 *
 * @return array<int, array{label: string, href: string}>
 */
function tgit_quarterly_default_menu_links(): array {
	$links = array(
		array(
			'label' => __( 'CIO Address', 'tgit-quarterly' ),
			'href'  => '#cio-address',
		),
		array(
			'label' => __( 'Project Spotlight', 'tgit-quarterly' ),
			'href'  => '#projects',
		),
		array(
			'label' => __( 'Recognition', 'tgit-quarterly' ),
			'href'  => '#recognition',
		),
		array(
			'label' => __( 'Archive', 'tgit-quarterly' ),
			'href'  => '#archive',
		),
	);

	if (
		function_exists( 'tgit_quarterly_resolved_project_submit_url' )
		&& function_exists( 'tgit_quarterly_user_can_manage_project_blog' )
		&& tgit_quarterly_user_can_manage_project_blog()
	) {
		$links[] = array(
			'label' => tgit_editorial_option( 'project_submit_label', __( 'Submit Project', 'tgit-quarterly' ) ),
			'href'  => tgit_quarterly_resolved_project_submit_url(),
		);
	}

	return $links;
}

/**
 * Hide the "My Project Blog" menu item from users who may not manage it.
 *
 * @param array<int, WP_Post> $items Nav menu items.
 * @return array<int, WP_Post>
 */
function tgit_quarterly_filter_project_blog_menu_item( array $items ): array {
	if ( ! function_exists( 'tgit_quarterly_user_can_manage_project_blog' ) || tgit_quarterly_user_can_manage_project_blog() ) {
		return $items;
	}

	if ( ! function_exists( 'tgit_quarterly_resolved_project_submit_url' ) ) {
		return $items;
	}

	$submit_url = untrailingslashit( tgit_quarterly_resolved_project_submit_url() );

	foreach ( $items as $key => $item ) {
		if ( untrailingslashit( $item->url ) === $submit_url ) {
			unset( $items[ $key ] );
		}
	}

	return array_values( $items );
}
add_filter( 'wp_nav_menu_objects', 'tgit_quarterly_filter_project_blog_menu_item' );

/**
 * Apply theme classes to WordPress menu links.
 *
 * @param array<string, string> $atts Link attributes.
 * @param WP_Post              $item Menu item.
 * @param stdClass             $args Menu args.
 * @return array<string, string>
 */
function tgit_quarterly_nav_menu_link_attributes( array $atts, WP_Post $item, stdClass $args ): array {
	$location = isset( $args->theme_location ) ? (string) $args->theme_location : '';

	if ( in_array( $location, array( 'header', 'mobile' ), true ) ) {
		$atts['class'] = trim( ( $atts['class'] ?? '' ) . ' font-body text-sm font-medium uppercase tracking-wide text-muted-foreground transition-colors hover:text-foreground' );
	}

	if ( 'mobile' === $location || ( 'header' === $location && isset( $args->menu_class ) && false !== strpos( (string) $args->menu_class, 'flex-col' ) ) ) {
		$atts['data-mobile-nav-link'] = '';
	}

	if ( 'footer' === $location ) {
		$atts['class'] = trim( ( $atts['class'] ?? '' ) . ' font-body text-sm text-muted-foreground transition-colors hover:text-foreground' );
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'tgit_quarterly_nav_menu_link_attributes', 10, 3 );

/**
 * Flush rewrite rules once when CPT rewrite settings change.
 */
function tgit_quarterly_maybe_flush_rewrite_rules(): void {
	$rewrite_version = '20260607-project-rewrite-v2';

	if ( get_option( 'tgit_quarterly_rewrite_version' ) === $rewrite_version ) {
		return;
	}

	flush_rewrite_rules();
	update_option( 'tgit_quarterly_rewrite_version', $rewrite_version, false );
}
add_action( 'init', 'tgit_quarterly_maybe_flush_rewrite_rules', 100 );

/**
 * Enqueue compiled editorial CSS. Styles are pre-built in assets/css/editorial.css (no Node.js required on the server).
 */
function tgit_quarterly_enqueue_assets(): void {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	$css_path = $dir . '/assets/css/editorial.css';
	$css_ver  = file_exists( $css_path ) ? (string) filemtime( $css_path ) : TGIT_QUARTERLY_VERSION;
	wp_enqueue_style( 'tgit-quarterly-editorial', $uri . '/assets/css/editorial.css', array(), $css_ver );

	$custom_css_path = $dir . '/assets/css/custom.css';
	$custom_css_ver  = file_exists( $custom_css_path ) ? (string) filemtime( $custom_css_path ) : TGIT_QUARTERLY_VERSION;
	wp_enqueue_style( 'tgit-quarterly-custom', $uri . '/assets/css/custom.css', array( 'tgit-quarterly-editorial' ), $custom_css_ver );

	$js_path = $dir . '/assets/js/editorial.js';
	$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : TGIT_QUARTERLY_VERSION;
	wp_enqueue_script(
		'tgit-quarterly-editorial',
		$uri . '/assets/js/editorial.js',
		array(),
		$js_ver,
		true
	);

	if ( function_exists( 'tgit_quarterly_is_project_submit_page' ) && tgit_quarterly_is_project_submit_page() ) {
		$submit_js_path = $dir . '/assets/js/project-submit.js';
		$submit_js_ver    = file_exists( $submit_js_path ) ? (string) filemtime( $submit_js_path ) : TGIT_QUARTERLY_VERSION;
		wp_enqueue_script(
			'tgit-quarterly-project-submit',
			$uri . '/assets/js/project-submit.js',
			array(),
			$submit_js_ver,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'tgit_quarterly_enqueue_assets' );

/**
 * Public share URL for a recognition certificate preview page.
 */
function tgit_quarterly_recognition_share_page_url( int $post_id ): string {
	if ( $post_id <= 0 ) {
		return '';
	}

	return add_query_arg(
		array(
			'tgit_recognition_share' => $post_id,
		),
		home_url( '/' )
	);
}

/**
 * Render a lightweight share page for recognition certificates.
 */
function tgit_quarterly_render_recognition_share_page(): void {
	if ( ! isset( $_GET['tgit_recognition_share'] ) ) {
		return;
	}

	$post_id = absint( wp_unslash( $_GET['tgit_recognition_share'] ) );
	$post    = $post_id > 0 ? get_post( $post_id ) : null;

	if ( ! $post instanceof WP_Post || 'tgit_recognition' !== $post->post_type || 'publish' !== $post->post_status ) {
		status_header( 404 );
		nocache_headers();
		exit;
	}

	$cert_id = (int) get_post_meta( $post_id, '_tgit_certificate_image_id', true );
	$image   = $cert_id ? wp_get_attachment_image_url( $cert_id, 'full' ) : '';
	if ( ! is_string( $image ) ) {
		$image = '';
	}

	if ( '' === $image ) {
		$featured_id = (int) get_post_meta( $post_id, '_tgit_featured_image_id', true );
		$image       = $featured_id ? wp_get_attachment_image_url( $featured_id, 'full' ) : '';
		if ( ! is_string( $image ) ) {
			$image = '';
		}
	}

	$designation = (string) get_post_meta( $post_id, '_tgit_designation', true );
	$citation    = wp_strip_all_tags( $post->post_content );
	$share_url   = tgit_quarterly_recognition_share_page_url( $post_id );
	$title       = get_the_title( $post );
	$meta_title  = trim( $title . ' | TGIT Quarterly Recognition' );
	$meta_desc   = $citation !== '' ? wp_trim_words( $citation, 28, '...' ) : 'Quarterly recognition certificate from TGIT.';
	$image_alt   = trim( $title . ' certificate' );
	$image_type  = '';
	$image_width = 0;
	$image_height = 0;

	if ( $cert_id > 0 ) {
		$image_meta = wp_get_attachment_metadata( $cert_id );
		if ( is_array( $image_meta ) ) {
			$image_width  = isset( $image_meta['width'] ) ? (int) $image_meta['width'] : 0;
			$image_height = isset( $image_meta['height'] ) ? (int) $image_meta['height'] : 0;
		}

		$image_path = get_attached_file( $cert_id );
		if ( is_string( $image_path ) && '' !== $image_path ) {
			$filetype   = wp_check_filetype( $image_path );
			$image_type = isset( $filetype['type'] ) && is_string( $filetype['type'] ) ? $filetype['type'] : '';
		}
	}

	if ( ( 0 === $image_width || 0 === $image_height ) && '' !== $image ) {
		$img_src = @getimagesize( $image );
		if ( is_array( $img_src ) ) {
			$image_width  = isset( $img_src[0] ) ? (int) $img_src[0] : $image_width;
			$image_height = isset( $img_src[1] ) ? (int) $img_src[1] : $image_height;
		}
	}

	nocache_headers();
	status_header( 200 );
	?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title><?php echo esc_html( $meta_title ); ?></title>
	<meta name="description" content="<?php echo esc_attr( $meta_desc ); ?>" />
	<meta name="robots" content="noindex, nofollow" />
	<link rel="canonical" href="<?php echo esc_url( $share_url ); ?>" />
	<meta property="og:locale" content="<?php echo esc_attr( str_replace( '_', '-', get_locale() ) ); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo esc_attr( $meta_title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $meta_desc ); ?>" />
	<meta property="og:url" content="<?php echo esc_url( $share_url ); ?>" />
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
	<?php if ( '' !== $image ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $image ); ?>" />
		<meta property="og:image:url" content="<?php echo esc_url( $image ); ?>" />
		<meta property="og:image:secure_url" content="<?php echo esc_url( set_url_scheme( $image, 'https' ) ); ?>" />
		<meta property="og:image:alt" content="<?php echo esc_attr( $image_alt ); ?>" />
		<?php if ( $image_width > 0 ) : ?>
			<meta property="og:image:width" content="<?php echo esc_attr( (string) $image_width ); ?>" />
		<?php endif; ?>
		<?php if ( $image_height > 0 ) : ?>
			<meta property="og:image:height" content="<?php echo esc_attr( (string) $image_height ); ?>" />
		<?php endif; ?>
		<?php if ( '' !== $image_type ) : ?>
			<meta property="og:image:type" content="<?php echo esc_attr( $image_type ); ?>" />
		<?php endif; ?>
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:image:src" content="<?php echo esc_url( $image ); ?>" />
		<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" />
		<meta name="twitter:image:alt" content="<?php echo esc_attr( $image_alt ); ?>" />
	<?php else : ?>
		<meta name="twitter:card" content="summary" />
	<?php endif; ?>
	<meta name="twitter:url" content="<?php echo esc_url( $share_url ); ?>" />
	<meta name="twitter:title" content="<?php echo esc_attr( $meta_title ); ?>" />
	<meta name="twitter:description" content="<?php echo esc_attr( $meta_desc ); ?>" />
	<style>
		body {
			margin: 0;
			font-family: Georgia, "Times New Roman", serif;
			background: #f7f6f2;
			color: #242833;
		}
		.tgit-share-wrap {
			max-width: 960px;
			margin: 0 auto;
			padding: 48px 24px;
		}
		.tgit-share-card {
			background: #ffffff;
			border: 1px solid #d9dde6;
			box-shadow: 0 18px 45px rgba(25, 35, 52, 0.08);
			padding: 32px;
		}
		.tgit-share-card img {
			display: block;
			max-width: 100%;
			height: auto;
			margin: 0 auto 24px;
		}
		.tgit-share-title {
			margin: 0 0 8px;
			font-size: 36px;
			line-height: 1.1;
		}
		.tgit-share-designation {
			margin: 0 0 16px;
			font-size: 18px;
			color: #5e6676;
		}
		.tgit-share-copy {
			margin: 0;
			font-family: Arial, sans-serif;
			font-size: 16px;
			line-height: 1.7;
			color: #505867;
		}
	</style>
</head>
<body>
	<div class="tgit-share-wrap">
		<div class="tgit-share-card">
			<?php if ( '' !== $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
			<?php endif; ?>
			<h1 class="tgit-share-title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( '' !== $designation ) : ?>
				<p class="tgit-share-designation"><?php echo esc_html( $designation ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $citation ) : ?>
				<p class="tgit-share-copy"><?php echo esc_html( $citation ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</body>
</html>
	<?php
	exit;
}
add_action( 'template_redirect', 'tgit_quarterly_render_recognition_share_page', 0 );

// Include RevSlider classes if available
if ( defined( 'RS_PLUGIN_PATH' ) ) {
	require_once RS_PLUGIN_PATH . 'includes/slider.class.php';
	require_once RS_PLUGIN_PATH . 'includes/slide.class.php';
}

/**
 * Rating Ajax
 */
add_action('wp_ajax_rate_my_postnow', 'rate_my_postnow');
add_action('wp_ajax_nopriv_rate_my_postnow', 'rate_my_postnow');
function rate_my_postnow()
{
	if (isset($_POST) && is_user_logged_in() == true) {
		$current_user = wp_get_current_user();
		$current_userId = $current_user->ID;

		$postId = $_POST['cpost_id'];
		$postSubmitrating = $_POST['inputrating'];

		$userVoted = get_post_meta($postId, "user_have_voted_$current_userId", true);

		if ($userVoted !== "1") {
			update_post_meta($postId, "user_have_voted_$current_userId", 1);
			update_user_meta($current_userId, "user_submitted_rating_$postId", $postSubmitrating);
			update_post_meta($postId, "user_submitted_rating_$current_userId", $postSubmitrating);

			$submitted_count = get_post_meta($postId, "submitted_rating_count", true);
			$get_total_sum = get_post_meta($postId, 'submitted_rating', true);

			if ($get_total_sum > 0) {
				$get_total_sum = ($get_total_sum + $postSubmitrating);
				$submitted_count = ($submitted_count + 1);
				update_post_meta($postId, "submitted_rating", $get_total_sum);
				update_post_meta($postId, "submitted_rating_count", $submitted_count);
			} else {
				update_post_meta($postId, "submitted_rating", $postSubmitrating);
				update_post_meta($postId, "submitted_rating_count", 1);
			}
			echo "Rating submitted successfully";
		} else {
			echo "You have already rated";
		}
	} else {
		echo "Please login to rate";
	}

	die();
}

/**
 * Render a theme template part into a shortcode response.
 */
function tgit_quarterly_capture_template_part( string $slug, string $name = null ): string {
	ob_start();
	get_template_part( $slug, $name );
	return (string) ob_get_clean();
}

/**
 * Homepage section shortcode: CIO address.
 */
function tgit_quarterly_shortcode_cio_address(): string {
	return tgit_quarterly_capture_template_part( 'template-parts/section', 'cio-address' );
}
add_shortcode( 'tgit_cio_address', 'tgit_quarterly_shortcode_cio_address' );

/**
 * Homepage section shortcode: Project spotlight.
 */
function tgit_quarterly_shortcode_project_spotlight(): string {
	return tgit_quarterly_capture_template_part( 'template-parts/section', 'project-spotlight' );
}
add_shortcode( 'tgit_project_spotlight', 'tgit_quarterly_shortcode_project_spotlight' );

/**
 * Homepage section shortcode: Recognition.
 */
function tgit_quarterly_shortcode_recognition(): string {
	return tgit_quarterly_capture_template_part( 'template-parts/section', 'recognition' );
}
add_shortcode( 'tgit_recognition', 'tgit_quarterly_shortcode_recognition' );

/**
 * Homepage shortcode with the exact legacy front-page section stack.
 */
function tgit_quarterly_shortcode_frontpage_sections(): string {
	$content  = tgit_quarterly_capture_template_part( 'template-parts/section', 'cio-address' );
	$content .= tgit_quarterly_capture_template_part( 'template-parts/section', 'project-spotlight' );
	$content .= tgit_quarterly_capture_template_part( 'template-parts/section', 'recognition' );
	$content .= '<div id="archive" class="sr-only" aria-hidden="true"></div>';

	return $content;
}
add_shortcode( 'tgit_frontpage_sections', 'tgit_quarterly_shortcode_frontpage_sections' );

/**
 * Default editable homepage content.
 */
function tgit_quarterly_default_homepage_content(): string {
	return '[tgit_frontpage_sections]';
}

/**
 * Seed the assigned front page with the theme homepage shortcode when empty.
 *
 * @param mixed $old_value Previous page ID.
 * @param mixed $value     New page ID.
 */
function tgit_quarterly_seed_front_page_content( $old_value, $value ): void {
	$page_id = absint( $value );
	if ( $page_id <= 0 ) {
		return;
	}

	$page = get_post( $page_id );
	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		return;
	}

	$current_content = is_string( $page->post_content ) ? trim( $page->post_content ) : '';
	if ( '' !== $current_content ) {
		return;
	}

	remove_action( 'update_option_page_on_front', 'tgit_quarterly_seed_front_page_content', 10 );
	wp_update_post(
		array(
			'ID'           => $page_id,
			'post_content' => tgit_quarterly_default_homepage_content(),
		)
	);
	add_action( 'update_option_page_on_front', 'tgit_quarterly_seed_front_page_content', 10, 2 );
}
add_action( 'update_option_page_on_front', 'tgit_quarterly_seed_front_page_content', 10, 2 );

/**
 * Ensure the current homepage page has editable starter content if still empty.
 */
function tgit_quarterly_backfill_current_homepage(): void {
	$page_id = (int) get_option( 'page_on_front' );
	if ( $page_id <= 0 ) {
		return;
	}

	$page = get_post( $page_id );
	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		return;
	}

	$current_content = is_string( $page->post_content ) ? trim( $page->post_content ) : '';
	if ( '' !== $current_content ) {
		return;
	}

	wp_update_post(
		array(
			'ID'           => $page_id,
			'post_content' => tgit_quarterly_default_homepage_content(),
		)
	);
}
add_action( 'admin_init', 'tgit_quarterly_backfill_current_homepage' );

/**
 * Recognition Slider Shortcode
 */
add_shortcode('tgit-recognition-slider', 'tgit_recognition_slider_shortcode');
function tgit_recognition_slider_shortcode($atts) {
	$posts = tgit_quarterly_get_recognitions();
	if ( empty( $posts ) ) {
		return '<p class="font-body text-muted-foreground">' . esc_html__( 'Recognition stories will appear here soon.', 'tgit-quarterly' ) . '</p>';
	}

	$items_per_slide = 3;
	$desktop_carousel_id = 'carousel-desktop-' . uniqid();
	$mobile_carousel_id  = 'carousel-mobile-' . uniqid();

	$build_indicators = function( int $total_slides ): string {
		$indicators = '<ol class="carousel-indicators flex gap-2 justify-center">';
		for ( $i = 0; $i < $total_slides; $i++ ) {
			$indicators .= '<li data-slide-to="' . esc_attr( (string) $i ) . '" class="' . ( 0 === $i ? 'active bg-primary' : 'bg-muted' ) . ' h-2 w-2 rounded-full cursor-pointer transition-colors hover:bg-primary"></li>';
		}
		$indicators .= '</ol>';

		return $indicators;
	};

	$prev_icon = '<span style="display: inline-block; transform: rotate(180deg);">' . tgit_quarterly_icon( 'arrow-right', 'h-4 w-4', array( 'aria-hidden' => 'true' ) ) . '</span>';
	$next_icon = tgit_quarterly_icon( 'arrow-right', 'h-4 w-4', array( 'aria-hidden' => 'true' ) );

	$post_count           = count( $posts );
	$total_desktop_slides = (int) ceil( $post_count / $items_per_slide );
	$single_desktop_slide = $total_desktop_slides <= 1;
	$desktop_items        = '';
	for ( $slide = 0; $slide < $total_desktop_slides; $slide++ ) {
		$slide_posts = array_slice( $posts, $slide * $items_per_slide, $items_per_slide );
		$slide_count = count( $slide_posts );
		$desktop_items .= '<div class="carousel-item ' . ( 0 === $slide ? 'active' : '' ) . '">';
		$desktop_items .= '<div class="tgit-recognition-slide-row tgit-recognition-slide-row--' . esc_attr( (string) $slide_count ) . '">';
		foreach ( $slide_posts as $post ) {
			$desktop_items .= tgit_generate_recognition_slide_html( $post );
		}
		$desktop_items .= '</div>';
		$desktop_items .= '</div>';
	}

	$mobile_items = '';
	foreach ( $posts as $index => $post ) {
		$mobile_items .= '<div class="carousel-item ' . ( 0 === $index ? 'active' : '' ) . '">';
		$mobile_items .= '<div class="tgit-recognition-mobile-slide">';
		$mobile_items .= tgit_generate_recognition_slide_html( $post );
		$mobile_items .= '</div>';
		$mobile_items .= '</div>';
	}

	$desktop  = '<div id="' . esc_attr( $desktop_carousel_id ) . '" class="tgit-recognition-slider tgit-recognition-carousel-desktop carousel hidden md:block" data-carousel-id="' . esc_attr( $desktop_carousel_id ) . '">';
	$desktop .= '<div class="mb-6 flex items-center justify-between"></div>';
	$desktop .= '<div class="relative carousel-wrapper">';
	$desktop .= '<div class="carousel-inner">' . $desktop_items . '</div>';
	if ( ! $single_desktop_slide ) {
		$desktop .= '<button type="button" class="carousel-control-prev absolute left-2 top-1/2 z-20 -translate-y-1/2 inline-flex h-10 w-10 items-center justify-center rounded-full border border-editorial-border bg-white text-primary shadow-lg transition-colors hover:bg-primary hover:text-white" data-slide="prev" aria-label="' . esc_attr__( 'Previous slide', 'tgit-quarterly' ) . '">' . $prev_icon . '</button>';
		$desktop .= '<button type="button" class="carousel-control-next absolute right-2 top-1/2 z-20 -translate-y-1/2 inline-flex h-10 w-10 items-center justify-center rounded-full border border-editorial-border bg-white text-primary shadow-lg transition-colors hover:bg-primary hover:text-white" data-slide="next" aria-label="' . esc_attr__( 'Next slide', 'tgit-quarterly' ) . '">' . $next_icon . '</button>';
	}
	$desktop .= '</div>';
	if ( ! $single_desktop_slide ) {
		$desktop .= '<div class="mt-6">' . $build_indicators( $total_desktop_slides ) . '</div>';
	}
	$desktop .= '</div>';

	$mobile  = '<div id="' . esc_attr( $mobile_carousel_id ) . '" class="tgit-recognition-slider tgit-recognition-carousel-mobile carousel md:hidden" data-carousel-id="' . esc_attr( $mobile_carousel_id ) . '">';
	$mobile .= '<div class="relative carousel-wrapper">';
	$single_mobile_slide = $post_count <= 1;
	$mobile .= '<div class="carousel-inner">' . $mobile_items . '</div>';
	$mobile .= '</div>';
	if ( ! $single_mobile_slide ) {
		$mobile .= '<div class="mt-5">' . $build_indicators( $post_count ) . '</div>';
	}
	$mobile .= '</div>';

	return $desktop . $mobile;
}
function tgit_generate_recognition_slide_html($post) {
	$payload = wp_json_encode(tgit_quarterly_recognition_payload($post));
	$award = get_post_meta($post->ID, '_tgit_award_category', true);
	$award = is_string($award) ? $award : '';
	$initials = tgit_quarterly_initials_from_title(get_the_title($post));
	$featured_img_id = (int) get_post_meta( $post->ID, '_tgit_featured_image_id', true );
	$image = $featured_img_id ? wp_get_attachment_image_url( $featured_img_id, 'large' ) : '';
	$image = is_string( $image ) ? $image : '';
	$year = get_the_date( 'Y', $post );

	$html = '<div class="snap-start shrink-0 tgit-recognition-slide">';
	$html .= '<div class="group relative cursor-pointer overflow-hidden rounded border border-editorial-border bg-white transition-all duration-500 hover:shadow-sm h-full flex flex-col" data-recognition-card data-recognition="' . esc_attr($payload) . '" tabindex="0" role="button" aria-haspopup="dialog">';
	if ( $image ) {
		$html .= '<div class="overflow-hidden bg-muted flex-shrink-0 h-[400px]">';
		$html .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( get_the_title( $post ) ) . '" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" />';
		$html .= '</div>';
	} else {
		$html .= '<div class="flex h-[400px] w-full items-center justify-center bg-secondary font-editorial text-base font-semibold text-muted-foreground flex-shrink-0">' . esc_html( $initials ) . '</div>';
	}
	$html .= '<div class="flex-grow p-2 flex flex-col justify-between">';
	$html .= '<div>';
	$html .= '<h3 class="font-editorial text-xs font-semibold text-foreground leading-tight">' . esc_html( get_the_title( $post ) ) . '</h3>';
	$designation = get_post_meta( $post->ID, '_tgit_designation', true );
	$designation = is_string( $designation ) ? $designation : '';
	if ( $designation !== '' ) {
		$html .= '<p class="font-body text-[10px] italic text-muted-foreground mt-0.5">' . esc_html( $designation ) . '</p>';
	}
	$html .= '</div>';
	if ( $award !== '' || $year !== '' ) {
		$html .= '<div class="mt-1 flex items-center justify-between gap-0.5 pt-1 border-t border-editorial-border text-[6px]">';
		if ( $award !== '' ) {
			$html .= '<span class="font-body uppercase tracking-tight text-gold font-semibold truncate">' . esc_html( $award ) . '</span>';
		}
		$html .= '<span class="font-body uppercase tracking-tight text-muted-foreground flex-shrink-0">' . esc_html( $year ) . '</span>';
		$html .= '</div>';
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}

function check_login_user()
{
    $all_policy_admin = explode(',', ALL_ADMIN);

    // ? Whitelisted email list
    $whitelisted_users = array(
        'c-laxmi.akula@timesofindia.com',
        'mohd.bilal@timesofindia.com',
        'rekha.handa@timesofindia.com',
        'swati.koli@timesofindia.com',
        'vattackanayil.jacob@timesofindia.com',
        'c-khushbu.jaiswal@timesofindia.com',
        'c-dipti.maini@timesofindia.com',
        'c-anuradha.hardwani@timesofindia.com',
        'rubina.khan@timesofindia.com',
        'bijoya.chanda@timesofindia.com',
        'dipali.anerao@timesofindia.com',
    );

    if (!is_user_logged_in() && $GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

        // Cookie login (unchanged flow)
        if (!empty($_COOKIE['i_s']) && (empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']))) {
            $apiResponse = new apiResponse();
            $api_response = $apiResponse->curlresponse();

            if ($api_response['resp'] == 'true') {
                if ($user_id = email_exists($api_response['email'])) {
                    wp_set_current_user($user_id);
                    $my_user = wp_get_current_user();

                    if (in_array($user_id, $all_policy_admin)) {
                        $my_user->set_role('policy-admin');
                        wp_update_user(array('ID' => $user_id, 'role' => 'policy-admin'));
                    }

                    wp_update_user(array('ID' => $user_id, 'first_name' => $api_response['first_name'], 'last_name' => $api_response['last_name']));
                    update_user_meta($user_id, 'branch', $api_response['branch']);
                    update_user_meta($user_id, 'location', $api_response['location']);
                    update_user_meta($user_id, 'designation', $api_response['designation']);
                    update_user_meta($user_id, 'department', $api_response['department']);
                    update_user_meta($user_id, 'sap', $api_response['sap']);
                    if (isset($api_response['profile_picture'])) {
                        update_user_meta($user_id, 'profile_picture', $api_response['profile_picture']);
                    }
                    if (isset($api_response['manager_email'])) {
                        update_user_meta($user_id, 'manager_email', $api_response['manager_email']);
                    }
                } else {
                    $user_id = wp_create_user(strtolower($api_response['email']), "", $api_response['email']);
                    if (!is_wp_error($user_id)) {
                        wp_set_current_user($user_id);
                        $my_user = wp_get_current_user();
                        if (in_array($user_id, $all_policy_admin)) {
                            $my_user->set_role('policy-admin');
                            wp_update_user(array('ID' => $user_id, 'role' => 'policy-admin'));
                        }
                        wp_insert_user(array('ID' => $user_id, 'first_name' => $api_response['first_name'], 'last_name' => $api_response['last_name']));
                        add_user_meta($user_id, 'branch', $api_response['branch']);
                        add_user_meta($user_id, 'location', $api_response['location']);
                        add_user_meta($user_id, 'designation', $api_response['designation']);
                        add_user_meta($user_id, 'department', $api_response['department']);
                        add_user_meta($user_id, 'sap', $api_response['sap']);
                        if (isset($api_response['profile_picture'])) {
                            add_user_meta($user_id, 'profile_picture', $api_response['profile_picture']);
                        }
                        if (isset($api_response['manager_email'])) {
                            add_user_meta($user_id, 'manager_email', $api_response['manager_email']);
                        }
                    } else {
                        error_log('function.php: Login based on cookie: Error creating user -' . $user_id);
                    }
                }
            } else {
                header('WWW-Authenticate: Basic realm="Authentication Required"');
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            // ?? Basic Auth section
            if (!empty($_SERVER['PHP_AUTH_USER']) || !empty($_SERVER['PHP_AUTH_PW'])) {
                $username = strtolower(trim($_SERVER['PHP_AUTH_USER']));

                // ? Check if email is whitelisted
                if (in_array($username, array_map('strtolower', $whitelisted_users))) {
                    if ($user_id = email_exists($username)) {
                        // Already exists ? log in
                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id, true);
                        return;
                    } else {
                        // Create new WP user directly (skip API)
                        $user_id = wp_create_user($username, wp_generate_password(), $username);
                        if (!is_wp_error($user_id)) {
                            wp_set_current_user($user_id);
                            wp_set_auth_cookie($user_id, true);
                            return;
                        }
                    }
                }

                // ?? Non-whitelisted ? normal API flow
                $apiAuthentication = new apiAuthentication();
                $user_info = $apiAuthentication->curlrequest($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

                if (empty($user_info) || !isset($user_info['email'])) {
                    error_log('Authentication API returned no user data for: ' . $_SERVER['PHP_AUTH_USER']);
                    header('WWW-Authenticate: Basic realm="Authentication Required"');
                    exit;
                }

                if ($user_id = email_exists($user_info['email'])) {
                    wp_set_current_user($user_id);
                    $my_user = wp_get_current_user();

                    if (in_array($user_id, $all_policy_admin)) {
                        $my_user->set_role('policy-admin');
                        wp_update_user(array('ID' => $user_id, 'role' => 'policy-admin'));
                    }

                    wp_update_user(array('ID' => $user_id, 'first_name' => $user_info['first_name'], 'last_name' => $user_info['last_name']));
                    update_user_meta($user_id, 'branch', $user_info['branch']);
                    update_user_meta($user_id, 'location', $user_info['location']);
                    update_user_meta($user_id, 'designation', $user_info['designation']);
                    update_user_meta($user_id, 'department', $user_info['department']);
                    update_user_meta($user_id, 'sap', $user_info['sap']);
                    if (isset($user_info['profile_picture'])) {
                        update_user_meta($user_id, 'profile_picture', $user_info['profile_picture']);
                    }
                    if (isset($user_info['manager_email'])) {
                        update_user_meta($user_id, 'manager_email', $user_info['manager_email']);
                    }

                    if (!empty($user_info['i_s']) && !empty($user_info['i_s_expiry'])) {
                        setcookie('i_s', (string) $user_info['i_s'], (int) $user_info['i_s_expiry'], "/", ".timesgroup.com", true, true);
                    } else {
                        error_log('Authentication API returned user data without i_s cookie for: ' . $_SERVER['PHP_AUTH_USER']);
                    }
                } else {
                    if (filter_var($user_info['email'], FILTER_VALIDATE_EMAIL)) {
                        $email = $user_info['email'];
                        $user_id = wp_create_user(strtolower($email), "", $email);
                        if (!is_wp_error($user_id)) {
                            wp_set_current_user($user_id);
                            $my_user = wp_get_current_user();
                            if (in_array($user_id, $all_policy_admin)) {
                                $my_user->set_role('policy-admin');
                                wp_update_user(array('ID' => $user_id, 'role' => 'policy-admin'));
                            }
                            wp_insert_user(array('ID' => $user_id, 'first_name' => $user_info['first_name'], 'last_name' => $user_info['last_name']));
                            add_user_meta($user_id, 'branch', $user_info['branch']);
                            add_user_meta($user_id, 'location', $user_info['location']);
                            add_user_meta($user_id, 'designation', $user_info['designation']);
                            add_user_meta($user_id, 'department', $user_info['department']);
                            add_user_meta($user_id, 'sap', $user_info['sap']);
                            if (isset($user_info['profile_picture'])) {
                                add_user_meta($user_id, 'profile_picture', $user_info['profile_picture']);
                            }
                            if (isset($user_info['manager_email'])) {
                                add_user_meta($user_id, 'manager_email', $user_info['manager_email']);
                            }
                        } else {
                            error_log('function.php: Error creating user -' . $_SERVER['PHP_AUTH_USER']);
                        }
                        if (!empty($user_info['i_s']) && !empty($user_info['i_s_expiry'])) {
                            setcookie('i_s', (string) $user_info['i_s'], (int) $user_info['i_s_expiry'], "/", ".timesgroup.com", true, true);
                        } else {
                            error_log('Authentication API returned user data without i_s cookie for: ' . $_SERVER['PHP_AUTH_USER']);
                        }
                    } else {
                        error_log('Email not valid for-' . $_SERVER['PHP_AUTH_USER']);
                        header('WWW-Authenticate: Basic realm="Authentication Required"');
                        exit;
                    }
                }
            } else {
                header('WWW-Authenticate: Basic realm="Authentication Required"');
                echo '<script>window.close();</script>';
                exit;
            }
        }
    }
}
add_action('init', 'check_login_user', 10);

