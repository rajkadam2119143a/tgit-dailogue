<?php
/**
 * Fixed editorial header / primary navigation (React: EditorialNav).
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nav_links = tgit_quarterly_default_menu_links();
?>
<header data-editorial-header>
	<div class="section-padding">
		<div class="flex h-16 items-center justify-between md:h-20">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3">
				<div class="flex h-8 w-8 items-center justify-center rounded-sm bg-primary">
					<span class="font-editorial text-sm font-bold text-primary-foreground">IT</span>
				</div>
				<div class="hidden sm:block">
					<p class="font-editorial text-lg font-semibold leading-none tracking-tight text-foreground">
						<?php esc_html_e( 'The IT Quarterly', 'tgit-quarterly' ); ?>
					</p>
					<p class="mt-0.5 font-body text-[10px] font-medium uppercase tracking-[0.2em] text-muted-foreground">
						<?php esc_html_e( 'Technology • Strategy • People', 'tgit-quarterly' ); ?>
					</p>
				</div>
			</a>

			<nav class="hidden items-center gap-8 md:flex" aria-label="<?php esc_attr_e( 'Primary', 'tgit-quarterly' ); ?>">
				<?php
				if ( has_nav_menu( 'header' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'header',
							'container'      => false,
							'menu_class'     => 'flex items-center gap-8',
							'fallback_cb'    => false,
							'depth'          => 2,
						)
					);
				} else {
					foreach ( $nav_links as $link ) :
						?>
						<a
							href="<?php echo esc_url( $link['href'] ); ?>"
							class="font-body text-sm font-medium uppercase tracking-wide text-muted-foreground transition-colors hover:text-foreground"
						>
							<?php echo esc_html( $link['label'] ); ?>
						</a>
						<?php
					endforeach;
				}
				?>
				<button type="button" class="text-muted-foreground transition-colors hover:text-foreground" aria-label="<?php esc_attr_e( 'Search', 'tgit-quarterly' ); ?>">
					<?php echo tgit_quarterly_icon( 'search', 'h-4 w-4', array( 'aria-hidden' => 'true' ) ); ?>
				</button>
			</nav>

			<button
				type="button"
				class="text-foreground md:hidden"
				data-mobile-nav-toggle
				aria-expanded="false"
				aria-controls="editorial-mobile-nav"
				aria-label="<?php esc_attr_e( 'Menu', 'tgit-quarterly' ); ?>"
			>
				<span class="icon-open"><?php echo tgit_quarterly_icon( 'menu', 'h-5 w-5', array( 'aria-hidden' => 'true' ) ); ?></span>
				<span class="icon-close hidden"><?php echo tgit_quarterly_icon( 'x', 'h-5 w-5', array( 'aria-hidden' => 'true' ) ); ?></span>
			</button>
		</div>
	</div>

	<div id="editorial-mobile-nav" class="md:hidden" data-mobile-nav-panel aria-hidden="true">
		<div class="inner">
			<nav class="section-padding flex flex-col gap-4 py-6" aria-label="<?php esc_attr_e( 'Mobile primary', 'tgit-quarterly' ); ?>">
				<?php
				if ( has_nav_menu( 'mobile' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'mobile',
							'container'      => false,
							'menu_class'     => 'flex flex-col gap-4',
							'fallback_cb'    => false,
							'depth'          => 2,
						)
					);
				} elseif ( has_nav_menu( 'header' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'header',
							'container'      => false,
							'menu_class'     => 'flex flex-col gap-4',
							'fallback_cb'    => false,
							'depth'          => 2,
						)
					);
				} else {
					foreach ( $nav_links as $link ) :
						?>
						<a
							href="<?php echo esc_url( $link['href'] ); ?>"
							class="font-body text-sm font-medium uppercase tracking-wide text-muted-foreground transition-colors hover:text-foreground"
							data-mobile-nav-link
						>
							<?php echo esc_html( $link['label'] ); ?>
						</a>
						<?php
					endforeach;
				}
				?>
			</nav>
		</div>
	</div>
</header>
