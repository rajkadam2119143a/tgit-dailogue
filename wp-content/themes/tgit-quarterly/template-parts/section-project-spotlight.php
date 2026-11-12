<?php
/**
 * Project grid, filters, and detail modal (data from CPT tgit_project).
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category_icons = array(
	'Infrastructure' => 'cloud',
	'AI'             => 'brain',
	'Data'           => 'database',
	'Security'       => 'shield',
	'Automation'     => 'workflow',
);

$projects_posts = tgit_quarterly_get_projects();
$filter_cats    = tgit_quarterly_project_categories( $projects_posts );
$filter_labels  = array_merge( array( 'All' ), $filter_cats );

$proj_kicker = tgit_editorial_option( 'project_kicker' );
$proj_title  = tgit_editorial_option( 'project_title' );
?>
<section id="projects" class="section-padding bg-secondary/30 py-20 md:py-32" data-project-spotlight>
	<div class="mx-auto max-w-6xl">
		<div class="scroll-reveal" style="--reveal-delay: 0s">
			<p class="mb-3 font-body text-xs font-semibold uppercase tracking-[0.3em] text-accent">
				<?php echo esc_html( $proj_kicker ); ?>
			</p>
			<h2 class="mb-4 max-w-2xl font-editorial text-3xl font-semibold text-foreground md:text-4xl lg:text-5xl">
				<?php echo esc_html( $proj_title ); ?>
			</h2>
			<div class="editorial-divider-left mb-10"></div>
		</div>

		<?php if ( empty( $projects_posts ) ) : ?>
			<p class="font-body text-muted-foreground">
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<?php esc_html_e( 'No projects yet. Add them under Project Spotlight in the admin menu.', 'tgit-quarterly' ); ?>
				<?php else : ?>
					<?php esc_html_e( 'Projects will appear here soon.', 'tgit-quarterly' ); ?>
				<?php endif; ?>
			</p>
		<?php else : ?>
			<div class="scroll-reveal mb-12 flex flex-wrap gap-2" style="--reveal-delay: 0.1s" data-project-filters role="group" aria-label="<?php esc_attr_e( 'Filter projects by category', 'tgit-quarterly' ); ?>">
				<?php foreach ( $filter_labels as $cat ) : ?>
					<button
						type="button"
						data-project-filter="<?php echo esc_attr( $cat ); ?>"
						class="<?php echo 'All' === $cat ? 'bg-primary text-primary-foreground' : 'bg-secondary text-muted-foreground hover:text-foreground'; ?> rounded-sm px-4 py-2 font-body text-xs uppercase tracking-wider transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
					>
						<?php echo esc_html( $cat ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3" data-project-grid>
				<?php
				foreach ( $projects_posts as $index => $post_obj ) :
					$project     = tgit_quarterly_project_payload( $post_obj );
					$icon_name   = isset( $category_icons[ $project['category'] ] ) ? $category_icons[ $project['category'] ] : 'cloud';
					$delay_val   = number_format( $index * 0.05, 2, '.', '' );
					$delay_style = '--reveal-delay: ' . $delay_val . 's';
					?>
					<a 
						href="<?php echo esc_url( get_permalink( $post_obj ) ); ?>"
						class="project-card tgit-spotlight-card group flex h-full cursor-pointer flex-col border border-border bg-background transition-all duration-300 hover:border-foreground/20"
						data-project-card
						data-category="<?php echo esc_attr( (string) $project['category'] ); ?>"
					>
						<div class="scroll-reveal h-full" style="<?php echo esc_attr( $delay_style ); ?>">
							<div class="flex h-full flex-col">
								<div class="tgit-spotlight-card-image">
									<?php if ( ! empty( $project['featuredImage'] ) ) : ?>
										<img
											src="<?php echo esc_url( $project['featuredImage'] ); ?>"
											alt="<?php echo esc_attr( (string) $project['name'] ); ?>"
											class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
										/>
									<?php else : ?>
										<div class="flex h-full items-center justify-center bg-secondary text-foreground">
											<?php echo tgit_quarterly_icon( $icon_name, 'h-10 w-10', array( 'aria-hidden' => 'true' ) ); ?>
										</div>
									<?php endif; ?>
								</div>
								<div class="flex flex-1 flex-col p-6">
									<div class="mb-4 flex items-start justify-between gap-4">
										<div class="flex h-10 w-10 items-center justify-center rounded-sm bg-secondary text-foreground">
											<?php echo tgit_quarterly_icon( $icon_name, 'h-5 w-5', array( 'aria-hidden' => 'true' ) ); ?>
										</div>
										<span class="font-body text-[10px] font-semibold uppercase tracking-wider text-accent">
											<?php echo esc_html( (string) $project['status'] ); ?>
										</span>
									</div>
									<h3 class="mb-1 font-editorial text-lg font-semibold text-foreground transition-colors group-hover:text-navy-light">
										<?php echo esc_html( (string) $project['name'] ); ?>
									</h3>
									<p class="mb-3 font-body text-xs text-muted-foreground">
										<?php
										printf(
											/* translators: %s project lead name */
											esc_html__( 'Led by %s', 'tgit-quarterly' ),
											esc_html( (string) $project['head'] )
										);
										?>
									</p>
									<p class="flex-1 font-body text-sm leading-relaxed text-muted-foreground">
										<?php echo esc_html( wp_trim_words( (string) $project['impact'], 28 ) ); ?>
									</p>
									<div class="mt-4 flex flex-wrap gap-1.5 border-t border-editorial-border pt-4">
										<?php foreach ( $project['tags'] as $tag ) : ?>
											<span class="rounded-sm bg-secondary px-2 py-1 font-body text-[10px] uppercase tracking-wider text-muted-foreground">
												<?php echo esc_html( (string) $tag ); ?>
											</span>
										<?php endforeach; ?>
									</div>
									<div class="mt-4 flex items-center gap-1 font-body text-xs font-medium text-foreground transition-all group-hover:gap-2">
										<?php esc_html_e( 'Read more', 'tgit-quarterly' ); ?>
										<?php echo tgit_quarterly_icon( 'arrow-right', 'h-3 w-3 shrink-0', array( 'aria-hidden' => 'true' ) ); ?>
									</div>
								</div>
							</div>
						</div>
					</a>
				<?php endforeach; ?>
			</div>

			<div class="tgit-project-see-all-wrap">
				<button
					type="button"
					class="hidden tgit-project-see-all-btn"
					data-project-see-all
				>
					<?php esc_html_e( 'See all', 'tgit-quarterly' ); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
</section>
