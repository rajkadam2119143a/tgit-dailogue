<?php
/**
 * Front-end project submission (legacy "My Blog" page flow).
 *
 * @package TGIT_Quarterly
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Suggested page slug for the submit form (create a page with this slug + template).
 */
function tgit_quarterly_project_submit_page_slug(): string {
	return 'my-project-blog';
}

/**
 * Public URL for the project submission page.
 */
function tgit_quarterly_project_submit_page_url(): string {
	$page = get_page_by_path( tgit_quarterly_project_submit_page_slug() );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}

	return home_url( '/' . tgit_quarterly_project_submit_page_slug() . '/' );
}

/**
 * Resolved URL for the submit-project page (theme option or default page).
 */
function tgit_quarterly_resolved_project_submit_url(): string {
	$custom = tgit_editorial_option( 'project_submit_url' );
	if ( '' !== trim( $custom ) ) {
		return $custom;
	}

	return tgit_quarterly_project_submit_page_url();
}

/**
 * Whether the current request uses the project submit page template.
 */
function tgit_quarterly_is_project_submit_page(): bool {
	if ( ! is_page() ) {
		return false;
	}

	$template = get_page_template_slug( get_queried_object_id() );
	return 'page-my-project-blog.php' === $template;
}

/**
 * Whether the current user may see and use the "My Project Blog" submission tab.
 * Restricted to full WP Administrators and the custom "policy-admin" role.
 */
function tgit_quarterly_user_can_manage_project_blog(): bool {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	$user = wp_get_current_user();
	return in_array( 'policy-admin', (array) $user->roles, true );
}

/**
 * Handle front-end project form POST (creates draft tgit_project).
 *
 * @return array{status: string, message: string}
 */
function tgit_quarterly_handle_project_submit(): array {
	if ( ! tgit_quarterly_user_can_manage_project_blog() ) {
		return array(
			'status'  => 'error',
			'message' => __( 'You do not have permission to submit a project.', 'tgit-quarterly' ),
		);
	}

	if (
		! isset( $_POST['tgit_project_submit'], $_POST['tgit_project_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tgit_project_nonce'] ) ), 'tgit_project_submit' )
	) {
		return array(
			'status'  => 'idle',
			'message' => '',
		);
	}

	$title           = isset( $_POST['blog-name'] ) ? sanitize_text_field( wp_unslash( $_POST['blog-name'] ) ) : '';
	$details         = isset( $_POST['blog-details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['blog-details'] ) ) : '';
	$business_user   = isset( $_POST['business-user'] ) ? sanitize_textarea_field( wp_unslash( $_POST['business-user'] ) ) : '';
	$business_value  = isset( $_POST['business-value'] ) ? sanitize_text_field( wp_unslash( $_POST['business-value'] ) ) : '';

	if ( '' === $title || '' === $details || '' === $business_value ) {
		return array(
			'status'  => 'error',
			'message' => __( 'Please fill all required fields.', 'tgit-quarterly' ),
		);
	}

	if ( mb_strlen( $details ) > 800 ) {
		$details = mb_substr( $details, 0, 800 );
	}
	if ( mb_strlen( $business_user ) > 600 ) {
		$business_user = mb_substr( $business_user, 0, 600 );
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_content' => $details,
			'post_type'    => 'tgit_project',
			'post_status'  => 'draft',
			'post_author'  => get_current_user_id(),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return array(
			'status'  => 'error',
			'message' => __( 'Could not save your project. Please try again.', 'tgit-quarterly' ),
		);
	}

	if ( '' !== $business_user ) {
		update_post_meta( $post_id, '_tgit_business_user_testimony', $business_user );
	}
	update_post_meta( $post_id, '_tgit_business_value', $business_value );

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$mimes = array(
		'bmp'     => 'image/bmp',
		'gif'     => 'image/gif',
		'jpe'     => 'image/jpeg',
		'jpeg'    => 'image/jpeg',
		'jpg'     => 'image/jpeg',
		'png'     => 'image/png',
		'tif'     => 'image/tiff',
		'tiff'    => 'image/tiff',
		'mp4|m4v' => 'video/mp4',
		'webm'    => 'video/webm',
		'ogg'     => 'video/ogg',
		'mov'     => 'video/quicktime',
		'docx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'pdf'     => 'application/pdf',
		'doc'     => 'application/msword',
	);
	$overrides = array(
		'mimes'     => $mimes,
		'test_form' => false,
	);

	if ( ! empty( $_FILES['featured-image']['name'] ) ) {
		$attachment_id = media_handle_upload( 'featured-image', $post_id );
		if ( ! is_wp_error( $attachment_id ) ) {
			update_post_meta( $post_id, '_tgit_featured_image_id', (int) $attachment_id );
		}
	}

	if ( ! empty( $_FILES['featured-video']['name'] ) ) {
		$uploaded = wp_handle_upload( $_FILES['featured-video'], $overrides );
		if ( is_array( $uploaded ) && ! isset( $uploaded['error'] ) ) {
			$attachment = array(
				'guid'           => $uploaded['url'],
				'post_mime_type' => $uploaded['type'],
				'post_title'     => basename( $uploaded['file'] ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			$attachment_id = wp_insert_attachment( $attachment, $uploaded['file'], $post_id );
			if ( ! is_wp_error( $attachment_id ) ) {
				update_post_meta( $post_id, '_tgit_testimony_media_id', (int) $attachment_id );
			}
			update_post_meta( $post_id, '_tgit_testimony_media_url', esc_url_raw( $uploaded['url'] ) );
		}
	}

	return array(
		'status'  => 'success',
		'message' => __( 'Your project was submitted successfully and is pending review.', 'tgit-quarterly' ),
	);
}

/**
 * Recent published projects for the submit page grid.
 *
 * @return array<int, WP_Post>
 */
function tgit_quarterly_recent_projects_for_submit_page( int $limit = 4 ): array {
	$q = new WP_Query(
		array(
			'post_type'              => 'tgit_project',
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
		)
	);

	return $q->posts;
}
