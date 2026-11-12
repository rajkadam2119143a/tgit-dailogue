<?php
/**
 * Query helpers for editorial CPTs (front-end templates).
 *
 * @package TGIT_Quarterly
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Testimony image/video URL for a project.
 */
function tgit_quarterly_project_testimony_media_url( int $post_id ): string {
	$media_id = (int) get_post_meta( $post_id, '_tgit_testimony_media_id', true );
	if ( $media_id > 0 ) {
		$url = wp_get_attachment_url( $media_id );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	$url = get_post_meta( $post_id, '_tgit_testimony_media_url', true );
	return is_string( $url ) ? $url : '';
}

/**
 * @return array<int, WP_Post>
 */
function tgit_quarterly_get_projects(): array {
	$q = new WP_Query(
		array(
			'post_type'              => 'tgit_project',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	return $q->posts;
}

/**
 * @param array<int, WP_Post> $posts Project posts.
 * @return list<string>
 */
function tgit_quarterly_project_categories( array $posts ): array {
	$categories = get_terms( array(
		'taxonomy'   => 'tgit_project_category',
		'hide_empty' => true,
		'fields'     => 'names',
	) );
	
	if ( is_wp_error( $categories ) ) {
		return array();
	}
	
	return (array) $categories;
}

/**
 * Check if comments are allowed for a project.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function tgit_quarterly_project_comments_enabled( int $post_id ): bool {
	$allow_comments = get_post_meta( $post_id, '_tgit_allow_comments', true );
	return $allow_comments === '1';
}

/**
 * Build modal/card payload for a project post.
 *
 * @param WP_Post $post Post.
 * @return array<string, mixed>
 */
function tgit_quarterly_project_payload( WP_Post $post ): array {
	$tags_raw = get_post_meta( $post->ID, '_tgit_tags', true );
	$tags_in  = is_string( $tags_raw ) ? $tags_raw : '';
	$tags     = array_filter( array_map( 'trim', explode( ',', $tags_in ) ) );

	$content = $post->post_content;
	$impact  = wp_strip_all_tags( $content );

	// Get featured image
	$featured_img_id = (int) get_post_meta( $post->ID, '_tgit_featured_image_id', true );
	$featured_img_url = $featured_img_id ? wp_get_attachment_image_url( $featured_img_id, 'full' ) : '';
	if ( ! is_string( $featured_img_url ) ) {
		$featured_img_url = '';
	}
	
	// Get category from taxonomy
	$category_terms = wp_get_post_terms( $post->ID, 'tgit_project_category', array( 'fields' => 'names' ) );
	$category = ! empty( $category_terms ) && ! is_wp_error( $category_terms ) ? $category_terms[0] : '';

	return array(
		'id'       => $post->ID,
		'name'     => get_the_title( $post ),
		'head'     => (string) get_post_meta( $post->ID, '_tgit_head', true ),
		'category' => (string) $category,
		'tags'     => array_values( $tags ),
		'impact'   => $impact,
		'status'   => (string) get_post_meta( $post->ID, '_tgit_status', true ),
		'businessValue' => (string) get_post_meta( $post->ID, '_tgit_business_value', true ),
		'businessUserTestimony' => (string) get_post_meta( $post->ID, '_tgit_business_user_testimony', true ),
		'testimonyMediaUrl' => tgit_quarterly_project_testimony_media_url( $post->ID ),
		'featuredImage' => $featured_img_url,
		'metrics'  => array(
			'timeline' => (string) get_post_meta( $post->ID, '_tgit_metric_timeline', true ),
			'scale'    => (string) get_post_meta( $post->ID, '_tgit_metric_scale', true ),
			'users'    => (string) get_post_meta( $post->ID, '_tgit_metric_users', true ),
		),
	);
}

/**
 * @return array<int, WP_Post>
 */
function tgit_quarterly_get_recognitions(): array {
	$q = new WP_Query(
		array(
			'post_type'              => 'tgit_recognition',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => array(
				'post_date'  => 'DESC',
				'menu_order' => 'ASC',
			),
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		)
	);
	return $q->posts;
}

/**
 * Modal payload for a recognition post (JSON on card).
 *
 * @param WP_Post $post Post.
 * @return array<string, mixed>
 */
function tgit_quarterly_recognition_payload( WP_Post $post ): array {
	$cert_id = (int) get_post_meta( $post->ID, '_tgit_certificate_image_id', true );
	$file_id = (int) get_post_meta( $post->ID, '_tgit_certificate_file_id', true );

	$cert_url = $cert_id ? wp_get_attachment_image_url( $cert_id, 'full' ) : '';
	if ( ! is_string( $cert_url ) ) {
		$cert_url = '';
	}
	$file_url = $file_id ? wp_get_attachment_url( $file_id ) : '';
	if ( ! is_string( $file_url ) ) {
		$file_url = '';
	}
	$download = $file_url !== '' ? $file_url : $cert_url;

	$citation = wp_strip_all_tags( $post->post_content );
	$citation = html_entity_decode( $citation, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$designation = (string) get_post_meta( $post->ID, '_tgit_designation', true );
	$share_url   = function_exists( 'tgit_quarterly_recognition_share_page_url' ) ? tgit_quarterly_recognition_share_page_url( $post->ID ) : '';
	$share_url   = '' !== $share_url ? $share_url : $download;
	$share_text  = sprintf(
		/* translators: %s: honouree name */
		__( 'Celebrating %s in TGIT Quarterly Recognition.', 'tgit-quarterly' ),
		get_the_title( $post )
	);
	$socials     = tgit_quarterly_recognition_social_links( $share_url, $share_text );

	return array(
		'id'              => $post->ID,
		'name'            => get_the_title( $post ),
		'designation'     => $designation,
		'awardCategory'   => (string) get_post_meta( $post->ID, '_tgit_award_category', true ),
		'citation'        => $citation,
		'certificateUrl'  => $cert_url,
		'downloadUrl'     => $download,
		'shareUrl'        => $share_url,
		'socials'         => $socials,
	);
}

/**
 * Build social share intents for recognition certificates.
 *
 * @param string $share_url Public URL to share.
 * @param string $share_text Pre-filled share text.
 * @return array<string, string>
 */
function tgit_quarterly_recognition_social_links( string $share_url, string $share_text ): array {
	if ( '' === $share_url ) {
		return array();
	}

	$encoded_text = rawurlencode( trim( $share_text ) );
	$encoded_url  = rawurlencode( $share_url );

	return array(
		'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
		'twitter'  => 'https://twitter.com/intent/tweet?text=' . $encoded_text . '&url=' . $encoded_url,
		'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url,
		'whatsapp' => 'https://wa.me/?text=' . rawurlencode( trim( $share_text . ' ' . $share_url ) ),
	);
}

/**
 * Two-letter style initials from post title.
 */
// function tgit_quarterly_initials_from_title( string $title ): string {
// 	$parts = preg_split( '/\s+/', trim( $title ), -1, PREG_SPLIT_NO_EMPTY );
// 	if ( ! $parts ) {
// 		return '';
// 	}
// 	$a = mb_substr( $parts[0], 0, 1, 'UTF-8' );
// 	$b = isset( $parts[1] ) ? mb_substr( $parts[1], 0, 1, 'UTF-8' ) : '';
// 	return mb_strtoupper( $a . $b, 'UTF-8' );
// }

function tgit_quarterly_initials_from_title( string $title ): string {
	$parts = preg_split( '/\s+/', trim( $title ), -1, PREG_SPLIT_NO_EMPTY );
	if ( ! $parts ) {
		return '';
	}

	if ( function_exists( 'mb_substr' ) && function_exists( 'mb_strtoupper' ) ) {
		$a = mb_substr( $parts[0], 0, 1, 'UTF-8' );
		$b = isset( $parts[1] ) ? mb_substr( $parts[1], 0, 1, 'UTF-8' ) : '';
		return mb_strtoupper( $a . $b, 'UTF-8' );
	}

	// Fallback when mbstring is unavailable.
	$a = substr( $parts[0], 0, 1 );
	$b = isset( $parts[1] ) ? substr( $parts[1], 0, 1 ) : '';
	return strtoupper( $a . $b );
}
