<?php
/**
 * Default page template.
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
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1 class="mb-8 font-editorial text-3xl font-semibold text-foreground md:text-4xl"><?php the_title(); ?></h1>
				<div class="entry-content font-body leading-relaxed text-muted-foreground [&_a]:text-primary [&_h2]:mt-8 [&_h2]:font-editorial [&_h2]:text-2xl [&_h2]:text-foreground">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
