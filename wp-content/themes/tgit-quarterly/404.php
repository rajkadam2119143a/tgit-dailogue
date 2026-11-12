<?php
/**
 * 404 template (React: NotFound).
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="flex min-h-screen items-center justify-center bg-muted">
	<div class="text-center">
		<h1 class="mb-4 text-4xl font-bold text-foreground"><?php esc_html_e( '404', 'tgit-quarterly' ); ?></h1>
		<p class="mb-4 text-xl text-muted-foreground"><?php esc_html_e( 'Oops! Page not found', 'tgit-quarterly' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-primary underline hover:text-primary/90">
			<?php esc_html_e( 'Return to Home', 'tgit-quarterly' ); ?>
		</a>
	</div>
</main>
<?php
get_footer();
