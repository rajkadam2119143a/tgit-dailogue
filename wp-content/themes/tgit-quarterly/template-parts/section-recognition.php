<?php
/**
 * Quarterly recognition — CPT tgit_recognition, optional Wall of Fame layout + certificate modal.
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$opts         = wp_parse_args( (array) get_option( TGIT_EDITORIAL_OPTION, array() ), tgit_editorial_default_options() );
$layout       = isset( $opts['recognition_layout'] ) && is_string( $opts['recognition_layout'] ) ? $opts['recognition_layout'] : 'wall_of_fame';
$posts        = tgit_quarterly_get_recognitions();
$kicker       = tgit_editorial_option( 'recognition_kicker' );
$title        = tgit_editorial_option( 'recognition_title' );
$intro        = tgit_editorial_option( 'recognition_intro' );
$arch_text    = tgit_editorial_option( 'recognition_archive_text' );
$arch_url     = tgit_editorial_option( 'recognition_archive_url' );
$wall_header  = ( 'wall_of_fame' === $layout );
?>
<section id="recognition" class="section-padding py-20 md:py-32" data-recognition-section>
	<div class="mx-auto max-w-6xl">
		<div class="scroll-reveal <?php echo $wall_header ? 'text-center' : ''; ?>" style="--reveal-delay: 0s">
			<?php if ( $wall_header ) : ?>
				<p class="mb-4 font-body text-sm font-semibold uppercase tracking-[0.25em] text-primary">
					<?php echo esc_html( $kicker ); ?>
				</p>
			<?php else : ?>
				<div class="mb-3 flex items-center gap-3">
					<?php echo tgit_quarterly_icon( 'award', 'h-5 w-5 text-gold', array( 'aria-hidden' => 'true' ) ); ?>
					<p class="font-body text-xs font-semibold uppercase tracking-[0.3em] text-gold">
						<?php echo esc_html( $kicker ); ?>
					</p>
				</div>
			<?php endif; ?>
			<h2 class="mb-4 max-w-2xl font-editorial text-3xl font-semibold text-foreground md:text-4xl lg:text-5xl <?php echo $wall_header ? 'mx-auto text-center' : ''; ?>">
				<?php echo esc_html( $title ); ?>
			</h2>
			<?php if ( $intro !== '' ) : ?>
				<p class="<?php echo $wall_header ? 'mx-auto text-center ' : ''; ?>mb-4 max-w-xl font-body text-base text-muted-foreground">
					<?php echo esc_html( $intro ); ?>
				</p>
			<?php endif; ?>
			<div class="<?php echo $wall_header ? 'editorial-divider mb-14' : 'editorial-divider-left mb-14'; ?>"></div>
		</div>

		<?php if ( empty( $posts ) ) : ?>
			<p class="font-body text-muted-foreground">
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<?php esc_html_e( 'No honourees yet. Add them under Quarterly Recognition in the admin menu.', 'tgit-quarterly' ); ?>
				<?php else : ?>
					<?php esc_html_e( 'Recognition stories will appear here soon.', 'tgit-quarterly' ); ?>
				<?php endif; ?>
			</p>
		<?php else : ?>
			<?php echo do_shortcode('[tgit-recognition-slider]'); ?>
		<?php endif; ?>

		<div class="scroll-reveal mt-16 text-center" style="--reveal-delay: 0.3s">
			<div class="editorial-divider mb-6"></div>
			<a href="<?php echo esc_url( $arch_url ); ?>" class="font-body text-xs uppercase tracking-[0.2em] text-muted-foreground transition-colors hover:text-foreground">
				<?php echo esc_html( $arch_text ); ?>
			</a>
		</div>
	</div>

	<div
		id="tgit-recognition-modal"
		class="fixed inset-0 z-[70] items-center justify-center bg-foreground/50 p-4 backdrop-blur-sm"
		hidden
		data-recognition-modal
		role="dialog"
		aria-modal="true"
		aria-labelledby="tgit-recognition-modal-title"
	>
		<div
			class="relative flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-sm border border-border bg-background shadow-xl"
			data-recognition-modal-panel
		>
			<button
				type="button"
				class="absolute right-3 top-3 z-10 rounded-sm bg-background/90 p-2 text-muted-foreground ring-1 ring-editorial-border transition-colors hover:text-foreground"
				data-recognition-modal-close
				aria-label="<?php esc_attr_e( 'Close', 'tgit-quarterly' ); ?>"
			>
				<?php echo tgit_quarterly_icon( 'x', 'h-5 w-5', array( 'aria-hidden' => 'true' ) ); ?>
			</button>
			<div class="overflow-y-auto p-6 md:p-8">
				<h3 id="tgit-recognition-modal-title" class="sr-only" data-recognition-modal-title></h3>
				<div class="mb-4 text-center" data-recognition-modal-heading>
					<p class="font-body text-sm font-medium text-foreground" data-recog-name></p>
					<p class="mt-1 font-body text-xs italic text-muted-foreground" data-recog-designation></p>
				</div>
				<div class="mb-6 flex justify-center" data-recog-cert-wrap>
					<img alt="" class="max-h-[55vh] w-auto max-w-full rounded-sm object-contain shadow-sm ring-1 ring-editorial-border" data-recog-cert-img hidden />
					<p class="font-body text-sm text-muted-foreground" data-recog-cert-missing hidden><?php esc_html_e( 'Upload a certificate image for this honouree in the admin.', 'tgit-quarterly' ); ?></p>
				</div>
				<p class="mb-6 text-center font-body text-sm leading-relaxed text-muted-foreground" data-recog-citation></p>
				<div class="flex flex-wrap items-center justify-center gap-3 border-t border-editorial-border pt-6" data-recog-actions>
					<div class="flex flex-wrap items-center justify-center gap-2" data-recog-socials></div>
					<a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-sm bg-primary text-primary-foreground transition-opacity hover:opacity-90" data-recog-download hidden download aria-label="<?php esc_attr_e( 'Download certificate', 'tgit-quarterly' ); ?>">
						<?php echo tgit_quarterly_icon( 'download', 'h-4 w-4', array( 'aria-hidden' => 'true' ) ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
