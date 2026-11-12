<?php
/**
 * Main fallback template (blog index / generic pages without a specific template).
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main section-padding py-16 md:py-24">
	<div class="mx-auto max-w-3xl">
		<?php if ( have_posts() ) : ?>
			<?php if ( is_home() && ! is_front_page() ) : ?>
				<h1 class="mb-10 font-editorial text-3xl font-semibold text-foreground md:text-4xl"><?php single_post_title(); ?></h1>
			<?php endif; ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'mb-12 border-b border-editorial-border pb-12' ); ?>>
					<h2 class="mb-2 font-editorial text-2xl font-semibold text-foreground">
						<a href="<?php the_permalink(); ?>" class="hover:text-navy-light"><?php the_title(); ?></a>
					</h2>
					<div class="font-body text-muted-foreground">
						<?php the_excerpt(); ?>
					</div>
				</article>
			<?php endwhile; ?>
			<div class="font-body text-sm text-muted-foreground">
				<?php the_posts_pagination(); ?>
			</div>
		<?php else : ?>
			<p class="font-body text-muted-foreground"><?php esc_html_e( 'No posts found.', 'tgit-quarterly' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
