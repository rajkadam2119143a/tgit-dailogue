<?php
/**
 * Editorial homepage text options (Project + Recognition section headers).
 *
 * @package TGIT_Quarterly
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TGIT_EDITORIAL_OPTION = 'tgit_editorial_home_options';

/**
 * Default option values.
 *
 * @return array<string, string>
 */
function tgit_editorial_default_options(): array {
	return array(
		'project_kicker'           => 'Project Spotlight',
		'project_title'            => 'Go-Live Updates & Strategic Initiatives',
		'project_submit_label'     => 'Submit Project',
		'project_submit_url'       => '',
		'recognition_kicker'       => 'Quarterly Recognition · Q4 2025',
		'recognition_title'        => 'Celebrating Excellence',
		'recognition_intro'        => 'Recognising the individuals whose work this quarter went above and beyond — shaping outcomes, inspiring teams, and moving us forward.',
		'recognition_archive_text' => 'View Wall of Excellence Archive →',
		'recognition_archive_url'  => '#archive',
		'recognition_layout'       => 'wall_of_fame',
	);
}

/**
 * @param string $key Option key.
 * @param string $default Fallback.
 */
function tgit_editorial_option( string $key, string $default = '' ): string {
	$opts = get_option( TGIT_EDITORIAL_OPTION, array() );
	if ( ! is_array( $opts ) ) {
		$opts = array();
	}
	$defaults = tgit_editorial_default_options();
	if ( isset( $opts[ $key ] ) && is_string( $opts[ $key ] ) ) {
		return $opts[ $key ];
	}
	return $defaults[ $key ] ?? $default;
}

/**
 * Register settings and admin page.
 */
function tgit_editorial_register_settings(): void {
	register_setting(
		'tgit_editorial_home',
		TGIT_EDITORIAL_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'tgit_editorial_sanitize_options',
			'default'           => tgit_editorial_default_options(),
		)
	);
}
add_action( 'admin_init', 'tgit_editorial_register_settings' );

/**
 * @param mixed $input Raw option.
 * @return array<string, string>
 */
function tgit_editorial_sanitize_options( $input ): array {
	$defaults = tgit_editorial_default_options();
	$out      = $defaults;
	if ( ! is_array( $input ) ) {
		return $out;
	}
	foreach ( array_keys( $defaults ) as $key ) {
		if ( ! isset( $input[ $key ] ) ) {
			continue;
		}
		$val = $input[ $key ];
		if ( 'recognition_layout' === $key ) {
			$out[ $key ] = ( is_string( $val ) && in_array( $val, array( 'editorial', 'wall_of_fame' ), true ) ) ? $val : 'wall_of_fame';
			continue;
		}
		if ( ! is_string( $val ) ) {
			continue;
		}
		if ( 'recognition_intro' === $key || 'project_title' === $key || 'recognition_title' === $key ) {
			$out[ $key ] = sanitize_textarea_field( $val );
		} elseif ( 'recognition_archive_url' === $key || 'project_submit_url' === $key ) {
			// Allow same-page anchors (#archive) or full URLs.
			$out[ $key ] = preg_match( '/^#/', $val ) ? sanitize_text_field( $val ) : esc_url_raw( $val );
		} else {
			$out[ $key ] = sanitize_text_field( $val );
		}
	}
	return $out;
}

/**
 * Admin menu under Appearance.
 */
function tgit_editorial_options_menu(): void {
	add_theme_page(
		__( 'Editorial Homepage', 'tgit-quarterly' ),
		__( 'Editorial Homepage', 'tgit-quarterly' ),
		'manage_options',
		'tgit-editorial-home',
		'tgit_editorial_options_render_page'
	);
}
add_action( 'admin_menu', 'tgit_editorial_options_menu' );

/**
 * Settings page markup.
 */
function tgit_editorial_options_render_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$opts = wp_parse_args( (array) get_option( TGIT_EDITORIAL_OPTION, array() ), tgit_editorial_default_options() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Editorial Homepage Text', 'tgit-quarterly' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Edit section headings shown on the front page. Projects and honourees are managed under their own admin menus.', 'tgit-quarterly' ); ?>
		</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'tgit_editorial_home' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row" colspan="2"><h2><?php esc_html_e( 'Project Spotlight', 'tgit-quarterly' ); ?></h2></th>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_project_kicker"><?php esc_html_e( 'Eyebrow / kicker', 'tgit-quarterly' ); ?></label></th>
					<td><input name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[project_kicker]" type="text" id="tgit_project_kicker" class="regular-text" value="<?php echo esc_attr( $opts['project_kicker'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_project_title"><?php esc_html_e( 'Section title', 'tgit-quarterly' ); ?></label></th>
					<td><textarea name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[project_title]" id="tgit_project_title" class="large-text" rows="2"><?php echo esc_textarea( $opts['project_title'] ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_project_submit_label"><?php esc_html_e( 'Submit project nav label', 'tgit-quarterly' ); ?></label></th>
					<td><input name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[project_submit_label]" type="text" id="tgit_project_submit_label" class="regular-text" value="<?php echo esc_attr( $opts['project_submit_label'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_project_submit_url"><?php esc_html_e( 'Submit project page URL', 'tgit-quarterly' ); ?></label></th>
					<td>
						<input name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[project_submit_url]" type="text" id="tgit_project_submit_url" class="regular-text" value="<?php echo esc_attr( $opts['project_submit_url'] ); ?>" placeholder="<?php echo esc_attr( function_exists( 'tgit_quarterly_project_submit_page_url' ) ? tgit_quarterly_project_submit_page_url() : '/my-project-blog/' ); ?>" />
						<p class="description"><?php esc_html_e( 'Leave empty to use the My Project Blog page when it exists (recommended slug: my-project-blog).', 'tgit-quarterly' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h2><?php esc_html_e( 'Quarterly Recognition (Wall of Fame)', 'tgit-quarterly' ); ?></h2></th>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Recognition layout', 'tgit-quarterly' ); ?></th>
					<td>
						<fieldset>
							<label><input type="radio" name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[recognition_layout]" value="wall_of_fame" <?php checked( $opts['recognition_layout'] ?? 'wall_of_fame', 'wall_of_fame' ); ?> /> <?php esc_html_e( 'Wall of Fame (photo row + overlapping name plate, reference design)', 'tgit-quarterly' ); ?></label><br />
							<label><input type="radio" name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[recognition_layout]" value="editorial" <?php checked( $opts['recognition_layout'] ?? '', 'editorial' ); ?> /> <?php esc_html_e( 'Editorial cards (avatar + citation list)', 'tgit-quarterly' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_recognition_kicker"><?php esc_html_e( 'Eyebrow / kicker', 'tgit-quarterly' ); ?></label></th>
					<td><input name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[recognition_kicker]" type="text" id="tgit_recognition_kicker" class="regular-text" value="<?php echo esc_attr( $opts['recognition_kicker'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_recognition_title"><?php esc_html_e( 'Section title', 'tgit-quarterly' ); ?></label></th>
					<td><textarea name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[recognition_title]" id="tgit_recognition_title" class="large-text" rows="2"><?php echo esc_textarea( $opts['recognition_title'] ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_recognition_intro"><?php esc_html_e( 'Intro paragraph', 'tgit-quarterly' ); ?></label></th>
					<td><textarea name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[recognition_intro]" id="tgit_recognition_intro" class="large-text" rows="3"><?php echo esc_textarea( $opts['recognition_intro'] ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_recognition_archive_text"><?php esc_html_e( 'Archive link label', 'tgit-quarterly' ); ?></label></th>
					<td><input name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[recognition_archive_text]" type="text" id="tgit_recognition_archive_text" class="regular-text" value="<?php echo esc_attr( $opts['recognition_archive_text'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="tgit_recognition_archive_url"><?php esc_html_e( 'Archive link URL', 'tgit-quarterly' ); ?></label></th>
					<td><input name="<?php echo esc_attr( TGIT_EDITORIAL_OPTION ); ?>[recognition_archive_url]" type="text" id="tgit_recognition_archive_url" class="regular-text" value="<?php echo esc_attr( $opts['recognition_archive_url'] ); ?>" placeholder="#archive" /></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
