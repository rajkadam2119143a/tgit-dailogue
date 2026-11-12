<?php
/**
 * "Email Templates" picker for Appearance > Menus, so past Newsletter-plugin
 * issues (wp_newsletter_emails) can be added to the ARCHIVE menu without
 * hand-typing "?na=view&id=" custom links.
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the Newsletter plugin's emails table is available to query.
 */
function tgit_quarterly_newsletter_emails_table_exists(): bool {
	global $wpdb;

	$table = $wpdb->prefix . 'newsletter_emails';
	return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
}

/**
 * Register the "Email Templates" meta box on the nav menus screen.
 */
function tgit_quarterly_register_email_archive_metabox(): void {
	add_meta_box(
		'tgit-email-archive-menu',
		__( 'Email Templates', 'tgit-quarterly' ),
		'tgit_quarterly_render_email_archive_metabox',
		'nav-menus',
		'side',
		'default'
	);
}
add_action( 'load-nav-menus.php', 'tgit_quarterly_register_email_archive_metabox' );

/**
 * Render the checklist of Newsletter email templates plus an "Add to Menu" button.
 */
function tgit_quarterly_render_email_archive_metabox(): void {
	if ( ! tgit_quarterly_newsletter_emails_table_exists() ) {
		echo '<p>' . esc_html__( 'The Newsletter plugin is not active, so past email templates cannot be listed here.', 'tgit-quarterly' ) . '</p>';
		return;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'newsletter_emails';
	$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT id, subject, created FROM {$table} WHERE type = %s ORDER BY id DESC LIMIT 300", 'message' ) );

	if ( empty( $rows ) ) {
		echo '<p>' . esc_html__( 'No email templates found yet.', 'tgit-quarterly' ) . '</p>';
		return;
	}
	?>
	<div id="tgit-email-archive-menu" class="posttypediv">
		<p class="description"><?php esc_html_e( 'Check the issues to add, then click "Add to Menu" and drag them under ARCHIVE.', 'tgit-quarterly' ); ?></p>
		<div class="tabs-panel" style="max-height: 200px; overflow-y: auto;">
			<ul class="categorychecklist">
				<?php foreach ( $rows as $row ) : ?>
					<?php
					$id    = (int) $row->id;
					$title = '' !== $row->subject ? $row->subject : sprintf( '#%d', $id );
					$url   = add_query_arg(
						array(
							'na' => 'view',
							'id' => $id,
						),
						home_url( '/' )
					);
					?>
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="tgit-email-archive-checkbox" data-title="<?php echo esc_attr( $title ); ?>" data-url="<?php echo esc_url( $url ); ?>" />
							<?php echo esc_html( $title ); ?>
							<span class="description"> &mdash; <?php echo esc_html( mysql2date( get_option( 'date_format' ), $row->created ) ); ?></span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<p class="button-controls">
			<span class="add-to-menu">
				<input type="submit" id="tgit-email-archive-submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'tgit-quarterly' ); ?>" name="tgit-email-archive-submit" />
				<span class="spinner" id="tgit-email-archive-spinner"></span>
			</span>
		</p>
	</div>
	<?php
}

/**
 * Inline admin JS: wires the "Add to Menu" button to core's add-menu-item AJAX action.
 */
function tgit_quarterly_email_archive_menu_script(): void {
	?>
	<script>
	( function ( $ ) {
		'use strict';

		$( '#tgit-email-archive-submit' ).on( 'click', function ( e ) {
			e.preventDefault();

			var $checked = $( '.tgit-email-archive-checkbox:checked' );
			if ( ! $checked.length ) {
				return;
			}

			var $spinner = $( '#tgit-email-archive-spinner' );
			$spinner.addClass( 'is-active' );

			var menuItems = {};
			$checked.each( function ( index ) {
				var $cb = $( this );
				menuItems[ '-' + ( index + 1 ) ] = {
					'menu-item-type': 'custom',
					'menu-item-url': $cb.data( 'url' ),
					'menu-item-title': $cb.data( 'title' )
				};
			} );

			$.post( ajaxurl, {
				action: 'add-menu-item',
				'menu-settings-column-nonce': $( '#menu-settings-column-nonce' ).val(),
				'menu-item': menuItems
			}, function ( response ) {
				$spinner.removeClass( 'is-active' );
				$( '#menu-to-edit' ).append( response );
				$checked.prop( 'checked', false );
				if ( window.wpNavMenu ) {
					wpNavMenu.refreshKeyboardAccessibility();
					wpNavMenu.refreshAdvancedAccessibility();
				}
			} );
		} );
	} )( jQuery );
	</script>
	<?php
}
add_action( 'admin_footer-nav-menus.php', 'tgit_quarterly_email_archive_menu_script' );
