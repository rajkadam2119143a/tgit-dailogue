<?php
/**
 * Custom post types: Project Spotlight + Quarterly Recognition (Wall of Fame).
 *
 * @package TGIT_Quarterly
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register post types.
 */
function tgit_editorial_register_post_types(): void {
	register_post_type(
		'tgit_project',
		array(
			'labels'              => array(
				'name'          => __( 'Projects', 'tgit-quarterly' ),
				'singular_name' => __( 'Project', 'tgit-quarterly' ),
				'add_new'       => __( 'Add Project', 'tgit-quarterly' ),
				'add_new_item'  => __( 'Add New Project', 'tgit-quarterly' ),
				'edit_item'     => __( 'Edit Project', 'tgit-quarterly' ),
				'menu_name'     => __( 'Project Spotlight', 'tgit-quarterly' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-portfolio',
			'menu_position'       => 26,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'page-attributes' ),
			'has_archive'         => false,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'query_var'           => true,
			'rewrite'             => array(
				'slug'       => 'tgit_project',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => true,
			),
		)
	);

	register_post_type(
		'tgit_recognition',
		array(
			'labels'              => array(
				'name'          => __( 'Recognition', 'tgit-quarterly' ),
				'singular_name' => __( 'Honouree', 'tgit-quarterly' ),
				'add_new'       => __( 'Add Honouree', 'tgit-quarterly' ),
				'add_new_item'  => __( 'Add New Honouree', 'tgit-quarterly' ),
				'edit_item'     => __( 'Edit Honouree', 'tgit-quarterly' ),
				'menu_name'     => __( 'Quarterly Recognition', 'tgit-quarterly' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-awards',
			'menu_position'       => 27,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'has_archive'         => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'tgit_editorial_register_post_types' );

/**
 * Explicit project detail rewrite for hosts where CPT rules are not refreshed reliably.
 */
function tgit_editorial_project_rewrite_rule(): void {
	add_rewrite_rule(
		'^tgit_project/([^/]+)/?$',
		'index.php?post_type=tgit_project&name=$matches[1]',
		'top'
	);
}
add_action( 'init', 'tgit_editorial_project_rewrite_rule', 20 );

/**
 * Fallback parser for project detail URLs when rewrite rules miss the CPT route.
 *
 * @param array<string, mixed> $query_vars Parsed request variables.
 * @return array<string, mixed>
 */
function tgit_editorial_project_request_fallback( array $query_vars ): array {
	if ( ! empty( $query_vars['post_type'] ) || ! empty( $query_vars['tgit_project'] ) ) {
		return $query_vars;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$request_uri = strtok( $request_uri, '?' );
	if ( ! is_string( $request_uri ) || '' === $request_uri ) {
		return $query_vars;
	}

	$home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$home_path = '/' . trim( $home_path, '/' ) . '/';
	$path      = '/' . ltrim( $request_uri, '/' );

	if ( '/' !== $home_path && 0 === strpos( $path, $home_path ) ) {
		$path = '/' . substr( $path, strlen( $home_path ) );
	}

	$path = trim( $path, '/' );
	if ( ! preg_match( '#^tgit_project/([^/]+)/?$#', $path, $matches ) ) {
		return $query_vars;
	}

	$query_vars['post_type'] = 'tgit_project';
	$query_vars['name']      = sanitize_title( $matches[1] );

	return $query_vars;
}
add_filter( 'request', 'tgit_editorial_project_request_fallback' );

/**
 * Register custom taxonomy for project categories.
 */
function tgit_editorial_register_taxonomies(): void {
	register_taxonomy(
		'tgit_project_category',
		'tgit_project',
		array(
			'labels'            => array(
				'name'              => __( 'Project Categories', 'tgit-quarterly' ),
				'singular_name'     => __( 'Project Category', 'tgit-quarterly' ),
				'search_items'      => __( 'Search Categories', 'tgit-quarterly' ),
				'all_items'         => __( 'All Categories', 'tgit-quarterly' ),
				'edit_item'         => __( 'Edit Category', 'tgit-quarterly' ),
				'update_item'       => __( 'Update Category', 'tgit-quarterly' ),
				'add_new_item'      => __( 'Add New Category', 'tgit-quarterly' ),
				'new_item_name'     => __( 'New Category Name', 'tgit-quarterly' ),
				'menu_name'         => __( 'Project Categories', 'tgit-quarterly' ),
			),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'public'            => false,
			'rewrite'           => false,
		)
	);
}
add_action( 'init', 'tgit_editorial_register_taxonomies', 5 );

/**
 * Admin: enqueue media for recognition certificate fields.
 */
function tgit_editorial_admin_assets( string $hook ): void {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array( 'tgit_project', 'tgit_recognition' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'tgit-editorial-admin',
		get_template_directory_uri() . '/assets/js/editorial-admin.js',
		array( 'jquery' ),
		TGIT_QUARTERLY_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'tgit_editorial_admin_assets' );

/**
 * Project metabox.
 */
function tgit_project_metabox(): void {
	add_meta_box(
		'tgit_project_details',
		__( 'Project details', 'tgit-quarterly' ),
		'tgit_project_metabox_render',
		'tgit_project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tgit_project_metabox' );

/**
 * Project discussion metabox.
 */
function tgit_project_discussion_metabox(): void {
	add_meta_box(
		'tgit_project_discussion',
		__( 'Discussion', 'tgit-quarterly' ),
		'tgit_project_discussion_metabox_render',
		'tgit_project',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'tgit_project_discussion_metabox' );

/**
 * @param WP_Post $post Post.
 */
function tgit_project_discussion_metabox_render( WP_Post $post ): void {
	wp_nonce_field( 'tgit_save_project', 'tgit_project_nonce' );
	$allow_comments = get_post_meta( $post->ID, '_tgit_allow_comments', true );
	$allow_comments = $allow_comments === '1' ? '1' : '0';
	?>
	<p>
		<label for="tgit_allow_comments">
			<input type="checkbox" id="tgit_allow_comments" name="tgit_allow_comments" value="1" <?php checked( $allow_comments, '1' ); ?> />
			<strong><?php esc_html_e( 'Allow comments', 'tgit-quarterly' ); ?></strong>
		</label>
	</p>
	<p class="description"><?php esc_html_e( 'Enable the discussion section on the project detail page for visitors to leave comments.', 'tgit-quarterly' ); ?></p>
	<?php
}

/**
 * @param WP_Post $post Post.
 */
function tgit_project_metabox_render( WP_Post $post ): void {
	wp_nonce_field( 'tgit_save_project', 'tgit_project_nonce' );
	$head     = get_post_meta( $post->ID, '_tgit_head', true );
	$tags     = get_post_meta( $post->ID, '_tgit_tags', true );
	$status   = get_post_meta( $post->ID, '_tgit_status', true );
	$m_tl     = get_post_meta( $post->ID, '_tgit_metric_timeline', true );
	$m_sc     = get_post_meta( $post->ID, '_tgit_metric_scale', true );
	$m_us     = get_post_meta( $post->ID, '_tgit_metric_users', true );
	$featured_img_id = (int) get_post_meta( $post->ID, '_tgit_featured_image_id', true );
	$allow_comments = get_post_meta( $post->ID, '_tgit_allow_comments', true );
	
	if ( ! is_string( $head ) ) {
		$head = '';
	}
	if ( ! is_string( $tags ) ) {
		$tags = '';
	}
	if ( ! is_string( $status ) ) {
		$status = '';
	}
	if ( ! is_string( $m_tl ) ) {
		$m_tl = '';
	}
	if ( ! is_string( $m_sc ) ) {
		$m_sc = '';
	}
	if ( ! is_string( $m_us ) ) {
		$m_us = '';
	}
	
	$featured_img_url = $featured_img_id ? wp_get_attachment_image_url( $featured_img_id, 'medium' ) : '';
	if ( ! is_string( $featured_img_url ) ) {
		$featured_img_url = '';
	}
	?>
	<!-- Project Details Section -->
	<div style="margin-bottom: 24px;">
		<h3 style="margin-top: 0; margin-bottom: 16px; font-size: 13px; font-weight: 600;"><?php esc_html_e( 'Project Information', 'tgit-quarterly' ); ?></h3>
		<p><label for="tgit_head"><strong><?php esc_html_e( 'Project head (lead)', 'tgit-quarterly' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tgit_head" name="tgit_head" value="<?php echo esc_attr( $head ); ?>" /></p>
		
		<p><label for="tgit_tags"><strong><?php esc_html_e( 'Tags (comma-separated)', 'tgit-quarterly' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tgit_tags" name="tgit_tags" value="<?php echo esc_attr( $tags ); ?>" /></p>
		
		<p><label for="tgit_status"><strong><?php esc_html_e( 'Status label', 'tgit-quarterly' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tgit_status" name="tgit_status" value="<?php echo esc_attr( $status ); ?>" placeholder="Live, In Design, …" /></p>
	</div>
	<hr />
	
	<!-- Project Cover Image Section -->
	<div style="margin-bottom: 24px;">
		<h3 style="margin-top: 0; margin-bottom: 16px; font-size: 13px; font-weight: 600;"><?php esc_html_e( 'Project Cover Image', 'tgit-quarterly' ); ?></h3>
		<input type="hidden" id="tgit_featured_image_id" name="tgit_featured_image_id" value="<?php echo esc_attr( (string) $featured_img_id ); ?>" />
		<p>
			<button type="button" class="button" id="tgit_featured_img_btn"><?php esc_html_e( 'Click the image to edit or update', 'tgit-quarterly' ); ?></button>
			<button type="button" class="button" id="tgit_featured_img_clear" <?php echo $featured_img_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remove featured image', 'tgit-quarterly' ); ?></button>
		</p>
		<div id="tgit_featured_img_preview" style="margin-top: 12px;">
			<?php if ( $featured_img_url ) : ?>
				<img src="<?php echo esc_url( $featured_img_url ); ?>" alt="" style="max-width: 240px; height: auto; border-radius: 4px;" />
			<?php endif; ?>
		</div>
	</div>
	
	<hr />
	
	<!-- Metrics Section -->
	<div style="margin-bottom: 24px;">
		<h3 style="margin-top: 0; margin-bottom: 16px; font-size: 13px; font-weight: 600;"><?php esc_html_e( 'Metrics', 'tgit-quarterly' ); ?></h3>
		<p><label for="tgit_metric_timeline"><?php esc_html_e( 'Timeline', 'tgit-quarterly' ); ?></label><br />
		<input type="text" class="widefat" id="tgit_metric_timeline" name="tgit_metric_timeline" value="<?php echo esc_attr( $m_tl ); ?>" /></p>
		<p><label for="tgit_metric_scale"><?php esc_html_e( 'Scale', 'tgit-quarterly' ); ?></label><br />
		<input type="text" class="widefat" id="tgit_metric_scale" name="tgit_metric_scale" value="<?php echo esc_attr( $m_sc ); ?>" /></p>
		<p><label for="tgit_metric_users"><?php esc_html_e( 'Users', 'tgit-quarterly' ); ?></label><br />
		<input type="text" class="widefat" id="tgit_metric_users" name="tgit_metric_users" value="<?php echo esc_attr( $m_us ); ?>" /></p>
		<p class="description"><?php esc_html_e( 'Use the main editor above for the short impact summary shown on the card and on the detail page.', 'tgit-quarterly' ); ?></p>
	</div>

	<?php
	$business_value = get_post_meta( $post->ID, '_tgit_business_value', true );
	$testimony      = get_post_meta( $post->ID, '_tgit_business_user_testimony', true );
	$business_value = is_string( $business_value ) ? $business_value : '';
	$testimony      = is_string( $testimony ) ? $testimony : '';
	$testimony_media_id  = (int) get_post_meta( $post->ID, '_tgit_testimony_media_id', true );
	$testimony_media_url = get_post_meta( $post->ID, '_tgit_testimony_media_url', true );
	$testimony_media_url = is_string( $testimony_media_url ) ? $testimony_media_url : '';
	$testimony_preview   = $testimony_media_id ? wp_get_attachment_url( $testimony_media_id ) : $testimony_media_url;
	if ( ! is_string( $testimony_preview ) ) {
		$testimony_preview = '';
	}
	?>
	<hr />
	<div style="margin-bottom: 24px;">
		<h3 style="margin-top: 0; margin-bottom: 16px; font-size: 13px; font-weight: 600;"><?php esc_html_e( 'Business impact', 'tgit-quarterly' ); ?></h3>
		<p><label for="tgit_business_value"><strong><?php esc_html_e( 'Business value', 'tgit-quarterly' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tgit_business_value" name="tgit_business_value" value="<?php echo esc_attr( $business_value ); ?>" /></p>
		<p><label for="tgit_business_user_testimony"><strong><?php esc_html_e( 'Business user testimony', 'tgit-quarterly' ); ?></strong></label><br />
		<textarea class="widefat" id="tgit_business_user_testimony" name="tgit_business_user_testimony" rows="4"><?php echo esc_textarea( $testimony ); ?></textarea></p>
		<input type="hidden" id="tgit_testimony_media_id" name="tgit_testimony_media_id" value="<?php echo esc_attr( (string) $testimony_media_id ); ?>" />
		<input type="hidden" id="tgit_testimony_media_url" name="tgit_testimony_media_url" value="<?php echo esc_attr( $testimony_media_url ); ?>" />
		<p><strong><?php esc_html_e( 'Testimony image / video', 'tgit-quarterly' ); ?></strong></p>
		<p>
			<button type="button" class="button" id="tgit_testimony_media_btn"><?php esc_html_e( 'Select media', 'tgit-quarterly' ); ?></button>
			<button type="button" class="button" id="tgit_testimony_media_clear" <?php echo $testimony_preview ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remove', 'tgit-quarterly' ); ?></button>
		</p>
		<div id="tgit_testimony_media_preview" style="margin-top: 12px;">
			<?php if ( $testimony_preview ) : ?>
				<?php if ( preg_match( '/\.(mp4|webm|ogg|mov)$/i', $testimony_preview ) ) : ?>
					<video src="<?php echo esc_url( $testimony_preview ); ?>" controls style="max-width: 320px; height: auto;"></video>
				<?php else : ?>
					<img src="<?php echo esc_url( $testimony_preview ); ?>" alt="" style="max-width: 240px; height: auto; border-radius: 4px;" />
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	
	<?php
}

/**
 * Recognition metabox.
 */
function tgit_recognition_metabox(): void {
	add_meta_box(
		'tgit_recognition_details',
		__( 'Wall of Fame / certificate', 'tgit-quarterly' ),
		'tgit_recognition_metabox_render',
		'tgit_recognition',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tgit_recognition_metabox' );

/**
 * @param WP_Post $post Post.
 */
function tgit_recognition_metabox_render( WP_Post $post ): void {
	wp_nonce_field( 'tgit_save_recognition', 'tgit_recognition_nonce' );
	$designation = get_post_meta( $post->ID, '_tgit_designation', true );
	$award_cat   = get_post_meta( $post->ID, '_tgit_award_category', true );
	$cert_img    = (int) get_post_meta( $post->ID, '_tgit_certificate_image_id', true );
	$cert_file   = (int) get_post_meta( $post->ID, '_tgit_certificate_file_id', true );
	$fb = get_post_meta( $post->ID, '_tgit_social_facebook', true );
	$tw = get_post_meta( $post->ID, '_tgit_social_twitter', true );
	$li = get_post_meta( $post->ID, '_tgit_social_linkedin', true );
	$wa = get_post_meta( $post->ID, '_tgit_social_whatsapp', true );

	$designation = is_string( $designation ) ? $designation : '';
	$award_cat   = is_string( $award_cat ) ? $award_cat : '';
	$fb          = is_string( $fb ) ? $fb : '';
	$tw          = is_string( $tw ) ? $tw : '';
	$li          = is_string( $li ) ? $li : '';
	$wa          = is_string( $wa ) ? $wa : '';

	$cert_img_url = $cert_img ? wp_get_attachment_image_url( $cert_img, 'medium' ) : '';
	$file_url     = $cert_file ? wp_get_attachment_url( $cert_file ) : '';
	
	$featured_img_id = (int) get_post_meta( $post->ID, '_tgit_featured_image_id', true );
	$featured_img_url = $featured_img_id ? wp_get_attachment_image_url( $featured_img_id, 'medium' ) : '';
	?>
	<p><strong style="display: block; margin-bottom: 8px;"><?php esc_html_e( 'Portrait image (shown on slider card)', 'tgit-quarterly' ); ?></strong></p>
	<input type="hidden" id="tgit_featured_image_id" name="tgit_featured_image_id" value="<?php echo esc_attr( (string) $featured_img_id ); ?>" />
	<p>
		<button type="button" class="button" id="tgit_featured_img_btn"><?php esc_html_e( 'Select image', 'tgit-quarterly' ); ?></button>
		<button type="button" class="button" id="tgit_featured_img_clear" <?php echo $featured_img_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remove', 'tgit-quarterly' ); ?></button>
	</p>
	<div id="tgit_featured_img_preview" style="margin-top:8px;">
		<?php if ( $featured_img_url ) : ?>
			<img src="<?php echo esc_url( $featured_img_url ); ?>" alt="" style="max-width:240px;height:auto;border-radius:4px;" />
		<?php endif; ?>
	</div>
	<hr style="margin: 16px 0;" />
	<p><label for="tgit_designation"><strong><?php esc_html_e( 'Designation / title (shown on card, italic)', 'tgit-quarterly' ); ?></strong></label><br />
	<input type="text" class="widefat" id="tgit_designation" name="tgit_designation" value="<?php echo esc_attr( $designation ); ?>" placeholder="<?php esc_attr_e( 'e.g. Vice President', 'tgit-quarterly' ); ?>" /></p>
	<p><label for="tgit_award_category"><strong><?php esc_html_e( 'Award category (optional ribbon text)', 'tgit-quarterly' ); ?></strong></label><br />
	<input type="text" class="widefat" id="tgit_award_category" name="tgit_award_category" value="<?php echo esc_attr( $award_cat ); ?>" /></p>
	<hr />
	<p><strong><?php esc_html_e( 'Certificate image (modal)', 'tgit-quarterly' ); ?></strong></p>
	<input type="hidden" id="tgit_certificate_image_id" name="tgit_certificate_image_id" value="<?php echo esc_attr( (string) $cert_img ); ?>" />
	<p>
		<button type="button" class="button" id="tgit_cert_img_btn"><?php esc_html_e( 'Select image', 'tgit-quarterly' ); ?></button>
		<button type="button" class="button" id="tgit_cert_img_clear" <?php echo $cert_img ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remove', 'tgit-quarterly' ); ?></button>
	</p>
	<div id="tgit_cert_img_preview" style="margin-top:8px;">
		<?php if ( $cert_img_url ) : ?>
			<img src="<?php echo esc_url( $cert_img_url ); ?>" alt="" style="max-width:240px;height:auto;" />
		<?php endif; ?>
	</div>
	<p><strong><?php esc_html_e( 'Certificate file (download)', 'tgit-quarterly' ); ?></strong></p>
	<input type="hidden" id="tgit_certificate_file_id" name="tgit_certificate_file_id" value="<?php echo esc_attr( (string) $cert_file ); ?>" />
	<p>
		<button type="button" class="button" id="tgit_cert_file_btn"><?php esc_html_e( 'Select PDF or image file', 'tgit-quarterly' ); ?></button>
		<button type="button" class="button" id="tgit_cert_file_clear" <?php echo $cert_file ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remove', 'tgit-quarterly' ); ?></button>
	</p>
	<p id="tgit_cert_file_name" class="description"><?php echo $file_url ? esc_html( basename( $file_url ) ) : ''; ?></p>
	<hr />
	<p><strong><?php esc_html_e( 'Social share URLs (optional)', 'tgit-quarterly' ); ?></strong></p>
	<p><label for="tgit_social_facebook">Facebook</label><br />
	<input type="url" class="widefat" id="tgit_social_facebook" name="tgit_social_facebook" value="<?php echo esc_attr( $fb ); ?>" placeholder="https://…" /></p>
	<p><label for="tgit_social_twitter">X (Twitter)</label><br />
	<input type="url" class="widefat" id="tgit_social_twitter" name="tgit_social_twitter" value="<?php echo esc_attr( $tw ); ?>" /></p>
	<p><label for="tgit_social_linkedin">LinkedIn</label><br />
	<input type="url" class="widefat" id="tgit_social_linkedin" name="tgit_social_linkedin" value="<?php echo esc_attr( $li ); ?>" /></p>
	<p><label for="tgit_social_whatsapp">WhatsApp</label><br />
	<input type="url" class="widefat" id="tgit_social_whatsapp" name="tgit_social_whatsapp" value="<?php echo esc_attr( $wa ); ?>" placeholder="https://wa.me/…" /></p>
	<p class="description"><?php esc_html_e( 'Portrait image on slider, certificate in modal. Editor content = optional citation shown under certificate.', 'tgit-quarterly' ); ?></p>
	<?php
}

/**
 * Save project meta.
 *
 * @param int $post_id Post ID.
 */
function tgit_save_project_meta( int $post_id ): void {
	if ( ! isset( $_POST['tgit_project_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tgit_project_nonce'] ) ), 'tgit_save_project' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$type = get_post_type( $post_id );
	if ( 'tgit_project' !== $type ) {
		return;
	}

	$fields = array(
		'tgit_head'                       => '_tgit_head',
		'tgit_tags'                       => '_tgit_tags',
		'tgit_status'                     => '_tgit_status',
		'tgit_metric_timeline'            => '_tgit_metric_timeline',
		'tgit_metric_scale'               => '_tgit_metric_scale',
		'tgit_metric_users'               => '_tgit_metric_users',
		'tgit_business_value'             => '_tgit_business_value',
		'tgit_business_user_testimony'    => '_tgit_business_user_testimony',
		'tgit_testimony_media_url'        => '_tgit_testimony_media_url',
	);

	foreach ( array( 'tgit_business_user_testimony' ) as $textarea_key ) {
		if ( isset( $_POST[ $textarea_key ] ) ) {
			update_post_meta( $post_id, $fields[ $textarea_key ], sanitize_textarea_field( wp_unslash( $_POST[ $textarea_key ] ) ) );
		}
	}

	$skip_keys = array( 'tgit_business_user_testimony' );
	foreach ( $fields as $post_key => $meta_key ) {
		if ( in_array( $post_key, $skip_keys, true ) || ! isset( $_POST[ $post_key ] ) ) {
			continue;
		}
		$val = 'tgit_testimony_media_url' === $post_key
			? esc_url_raw( wp_unslash( $_POST[ $post_key ] ) )
			: sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) );
		update_post_meta( $post_id, $meta_key, $val );
	}

	$testimony_media = isset( $_POST['tgit_testimony_media_id'] ) ? absint( $_POST['tgit_testimony_media_id'] ) : 0;
	update_post_meta( $post_id, '_tgit_testimony_media_id', $testimony_media );
	
	// Handle featured image
	$featured_img = isset( $_POST['tgit_featured_image_id'] ) ? absint( $_POST['tgit_featured_image_id'] ) : 0;
	update_post_meta( $post_id, '_tgit_featured_image_id', $featured_img );
	
	// Handle allow comments
	$allow_comments = isset( $_POST['tgit_allow_comments'] ) ? '1' : '0';
	update_post_meta( $post_id, '_tgit_allow_comments', $allow_comments );
	
	// Handle category taxonomy
	if ( isset( $_POST['tgit_project_category'] ) ) {
		$category_id = absint( $_POST['tgit_project_category'] );
		if ( $category_id > 0 ) {
			wp_set_post_terms( $post_id, array( $category_id ), 'tgit_project_category' );
		} else {
			wp_set_post_terms( $post_id, array(), 'tgit_project_category' );
		}
	}
}
add_action( 'save_post_tgit_project', 'tgit_save_project_meta' );

/**
 * Save recognition meta.
 *
 * @param int $post_id Post ID.
 */
function tgit_save_recognition_meta( int $post_id ): void {
	if ( ! isset( $_POST['tgit_recognition_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tgit_recognition_nonce'] ) ), 'tgit_save_recognition' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'tgit_recognition' !== get_post_type( $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_tgit_designation', isset( $_POST['tgit_designation'] ) ? sanitize_text_field( wp_unslash( $_POST['tgit_designation'] ) ) : '' );
	update_post_meta( $post_id, '_tgit_award_category', isset( $_POST['tgit_award_category'] ) ? sanitize_text_field( wp_unslash( $_POST['tgit_award_category'] ) ) : '' );

	$featured_img = isset( $_POST['tgit_featured_image_id'] ) ? absint( $_POST['tgit_featured_image_id'] ) : 0;
	$img = isset( $_POST['tgit_certificate_image_id'] ) ? absint( $_POST['tgit_certificate_image_id'] ) : 0;
	$fil = isset( $_POST['tgit_certificate_file_id'] ) ? absint( $_POST['tgit_certificate_file_id'] ) : 0;
	update_post_meta( $post_id, '_tgit_featured_image_id', $featured_img );
	update_post_meta( $post_id, '_tgit_certificate_image_id', $img );
	update_post_meta( $post_id, '_tgit_certificate_file_id', $fil );

	$url_fields = array(
		'tgit_social_facebook'  => '_tgit_social_facebook',
		'tgit_social_twitter'   => '_tgit_social_twitter',
		'tgit_social_linkedin'  => '_tgit_social_linkedin',
		'tgit_social_whatsapp'  => '_tgit_social_whatsapp',
	);
	foreach ( $url_fields as $pk => $mk ) {
		$v = isset( $_POST[ $pk ] ) ? esc_url_raw( wp_unslash( $_POST[ $pk ] ) ) : '';
		update_post_meta( $post_id, $mk, $v );
	}
}
add_action( 'save_post_tgit_recognition', 'tgit_save_recognition_meta' );
