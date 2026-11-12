<?php
/**
 * Site footer (React: EditorialFooter). Section anchors match nav (#projects for spotlight).
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_links = tgit_quarterly_default_menu_links();

$connect_links = array(
	__( 'Submit an Update', 'tgit-quarterly' ),
	__( 'Contact Editorial', 'tgit-quarterly' ),
	__( 'Subscribe to Newsletter', 'tgit-quarterly' ),
	__( 'Feedback', 'tgit-quarterly' ),
);
?>
<footer class="border-t border-editorial-border bg-secondary/30">
	<div class="section-padding py-16">
		<div class="mx-auto grid max-w-6xl grid-cols-1 gap-10 md:grid-cols-4">
			<div class="md:col-span-2">
				<div class="mb-4 flex items-center gap-3">
					<div class="flex h-8 w-8 items-center justify-center rounded-sm bg-primary">
						<span class="font-editorial text-sm font-bold text-primary-foreground">IT</span>
					</div>
					<p class="font-editorial text-lg font-semibold text-foreground"><?php esc_html_e( 'The IT Quarterly', 'tgit-quarterly' ); ?></p>
				</div>
				<p class="max-w-sm font-body text-sm leading-relaxed text-muted-foreground">
					<?php esc_html_e( 'The official editorial platform of the IT Department. Where technology meets storytelling, leadership meets transparency, and progress is visibly documented.', 'tgit-quarterly' ); ?>
				</p>
			</div>

			<div>
				<h4 class="mb-4 font-body text-xs font-semibold uppercase tracking-[0.2em] text-foreground">
					<?php esc_html_e( 'Sections', 'tgit-quarterly' ); ?>
				</h4>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'space-y-2',
							'fallback_cb'    => false,
							'depth'          => 1,
						)
					);
				} else {
					?>
					<ul class="space-y-2">
						<?php foreach ( $section_links as $item ) : ?>
							<li>
								<a href="<?php echo esc_url( $item['href'] ); ?>" class="font-body text-sm text-muted-foreground transition-colors hover:text-foreground">
									<?php echo esc_html( $item['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php
				}
				?>
			</div>

			<div>
				<h4 class="mb-4 font-body text-xs font-semibold uppercase tracking-[0.2em] text-foreground">
					<?php esc_html_e( 'Connect', 'tgit-quarterly' ); ?>
				</h4>
				<ul class="space-y-2">
					<?php foreach ( $connect_links as $item ) : ?>
						<li>
							<a href="#" class="font-body text-sm text-muted-foreground transition-colors hover:text-foreground">
								<?php echo esc_html( $item ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="mx-auto mt-12 flex max-w-6xl flex-col items-center justify-between gap-4 border-t border-editorial-border pt-8 md:flex-row">
			<p class="font-body text-xs text-muted-foreground">
				<?php esc_html_e( '© 2026 IT Department. Internal Use Only. All Rights Reserved.', 'tgit-quarterly' ); ?>
			</p>
			<p class="font-body text-xs text-muted-foreground">
				<?php esc_html_e( 'Published monthly · Next issue: March 2026', 'tgit-quarterly' ); ?>
			</p>
		</div>
	</div>
</footer>
