<?php
/**
 * Project detail page template.
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
	<?php
		$post_id = get_the_ID();
		$project = tgit_quarterly_project_payload( get_post() );
		$category_icons = array(
			'Infrastructure' => 'cloud',
			'AI'             => 'brain',
			'Data'           => 'database',
			'Security'       => 'shield',
			'Automation'     => 'workflow',
		);
		$icon_name = isset( $category_icons[ $project['category'] ] ) ? $category_icons[ $project['category'] ] : 'cloud';

		$prev_post = get_previous_post();
		$next_post = get_next_post();

		$all_projects = tgit_quarterly_get_projects();
		$related_projects = array_filter(
			$all_projects,
			function( $p ) use ( $post_id, $project ) {
				if ( $p->ID === $post_id ) {
					return false;
				}
				$p_payload = tgit_quarterly_project_payload( $p );
				return $p_payload['category'] === $project['category'];
			}
		);
		$related_projects = array_slice( $related_projects, 0, 4 );
	?>

	<main class="min-h-screen bg-background" data-project-detail>
		<section class="tgit-project-detail-shell py-16 md:py-24">
			<div class="mx-auto max-w-6xl px-4 md:px-6">
				<div class="scroll-reveal tgit-project-breadcrumb-row" style="--reveal-delay: 0s">
					<div class="tgit-project-breadcrumbs font-body text-xs text-muted-foreground">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-foreground transition-colors"><?php esc_html_e( 'Home', 'tgit-quarterly' ); ?></a>
						<span>/</span>
						<a href="<?php echo esc_url( home_url( '/' ) . '#projects' ); ?>" class="hover:text-foreground transition-colors"><?php esc_html_e( 'Projects', 'tgit-quarterly' ); ?></a>
						<span>/</span>
						<span class="text-foreground"><?php echo esc_html( $project['name'] ); ?></span>
					</div>

					<div class="tgit-project-nav">
						<?php if ( $prev_post ) : ?>
							<a
								href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>"
								class="tgit-project-nav-btn"
								aria-label="<?php esc_attr_e( 'Previous project', 'tgit-quarterly' ); ?>"
								data-project-nav-prev
							>
								<?php echo tgit_quarterly_icon( 'arrow-left', 'h-4 w-4', array( 'aria-hidden' => 'true' ) ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $next_post ) : ?>
							<a
								href="<?php echo esc_url( get_permalink( $next_post ) ); ?>"
								class="tgit-project-nav-btn"
								aria-label="<?php esc_attr_e( 'Next project', 'tgit-quarterly' ); ?>"
								data-project-nav-next
							>
								<?php echo tgit_quarterly_icon( 'arrow-right', 'h-4 w-4', array( 'aria-hidden' => 'true' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<div class="tgit-project-top">
					<div class="scroll-reveal tgit-project-media-col" style="--reveal-delay: 0.05s">
						<div class="tgit-project-image-panel">
							<?php if ( ! empty( $project['featuredImage'] ) ) : ?>
								<img
									src="<?php echo esc_url( $project['featuredImage'] ); ?>"
									alt="<?php echo esc_attr( $project['name'] ); ?>"
									class="tgit-project-main-image"
								/>
							<?php else : ?>
								<div class="tgit-project-image-fallback">
									<div class="flex h-16 w-16 items-center justify-center rounded-sm bg-primary text-primary-foreground">
										<?php echo tgit_quarterly_icon( $icon_name, 'h-8 w-8', array( 'aria-hidden' => 'true' ) ); ?>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<?php if ( ! empty( $project['testimonyMediaUrl'] ) ) : ?>
							<div class="tgit-project-testimony-media mt-4">
								<?php if ( preg_match( '/\.(mp4|webm|ogg|mov)$/i', $project['testimonyMediaUrl'] ) ) : ?>
									<video controls class="w-full rounded-sm border border-editorial-border">
										<source src="<?php echo esc_url( $project['testimonyMediaUrl'] ); ?>" type="video/mp4" />
									</video>
								<?php else : ?>
									<img
										src="<?php echo esc_url( $project['testimonyMediaUrl'] ); ?>"
										alt="<?php echo esc_attr( $project['name'] ); ?>"
										class="w-full rounded-sm border border-editorial-border object-cover"
									/>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $project['tags'] ) ) : ?>
							<div class="tgit-project-inline-tags">
								<h3 class="font-body text-sm font-semibold text-foreground">
									<?php esc_html_e( 'Technologies & Skills', 'tgit-quarterly' ); ?>
								</h3>
								<div class="flex flex-wrap gap-2">
									<?php foreach ( $project['tags'] as $tag ) : ?>
										<span class="rounded-sm bg-secondary px-2 py-1 font-body text-[10px] uppercase tracking-wider text-muted-foreground">
											<?php echo esc_html( $tag ); ?>
										</span>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<div class="scroll-reveal tgit-project-content-col" style="--reveal-delay: 0.1s">
						<div class="tgit-project-copy">
							<div class="tgit-project-meta-row">
								<div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
									<?php echo tgit_quarterly_icon( $icon_name, 'h-5 w-5', array( 'aria-hidden' => 'true' ) ); ?>
								</div>
								<p class="font-body text-sm text-muted-foreground">
									<?php
									printf(
										/* translators: 1: author display name, 2: post date */
										esc_html__( 'Posted by %1$s / %2$s', 'tgit-quarterly' ),
										esc_html( get_the_author() ),
										esc_html( get_the_date( 'd-m-Y' ) )
									);
									?>
								</p>
							</div>

							<h1 class="mb-4 font-editorial text-3xl md:text-4xl lg:text-5xl font-semibold text-foreground">
								<?php echo esc_html( $project['name'] ); ?>
							</h1>

							<div class="mb-5 flex flex-wrap items-center gap-2">
								<?php if ( ! empty( $project['category'] ) ) : ?>
									<span class="rounded-full border border-editorial-border px-3 py-1 font-body text-[11px] font-semibold uppercase tracking-[0.18em] text-accent">
										<?php echo esc_html( $project['category'] ); ?>
									</span>
								<?php endif; ?>
								<?php if ( ! empty( $project['status'] ) ) : ?>
									<span class="rounded-full bg-secondary px-3 py-1 font-body text-[11px] font-semibold uppercase tracking-[0.18em] text-muted-foreground">
										<?php echo esc_html( $project['status'] ); ?>
									</span>
								<?php endif; ?>
							</div>

							<div class="tgit-project-description font-body text-sm leading-7 text-muted-foreground">
								<h2 class="mb-3 font-body text-sm font-semibold text-foreground">
									<?php esc_html_e( 'Project Details:', 'tgit-quarterly' ); ?>
								</h2>
								<div class="space-y-4">
									<?php the_content(); ?>
								</div>
							</div>

							<div class="tgit-project-business-line">
								<strong><?php esc_html_e( 'Business Value:', 'tgit-quarterly' ); ?></strong>
								<span>
									<?php
									$business_value = trim( (string) ( $project['businessValue'] ?? '' ) );
									if ( '' === $business_value ) {
										$business_bits = array_filter(
											array(
												$project['category'],
												$project['status'],
												$project['metrics']['scale'],
											)
										);
										$business_value = ! empty( $business_bits ) ? implode( ', ', $business_bits ) : __( 'Operational efficiency, user experience', 'tgit-quarterly' );
									}
									echo esc_html( $business_value );
									?>
								</span>
							</div>

							<?php if ( ! empty( $project['businessUserTestimony'] ) ) : ?>
								<div class="tgit-project-testimony mt-6 font-body text-sm leading-7 text-muted-foreground">
									<strong class="text-foreground"><?php esc_html_e( 'Business User Testimony:', 'tgit-quarterly' ); ?></strong>
									<p class="mt-2"><?php echo esc_html( (string) $project['businessUserTestimony'] ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( array_filter( $project['metrics'] ) ) : ?>
								<div class="tgit-project-metrics-grid">
									<?php foreach ( $project['metrics'] as $key => $value ) : ?>
										<?php if ( $value ) : ?>
											<div class="tgit-project-metric">
												<p class="font-editorial text-2xl font-semibold text-primary"><?php echo esc_html( $value ); ?></p>
												<p class="font-body text-[11px] uppercase tracking-[0.18em] text-muted-foreground"><?php echo esc_html( ucfirst( $key ) ); ?></p>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $project['head'] ) ) : ?>
								<p class="font-body text-sm text-muted-foreground">
									<?php
									printf(
										/* translators: %s project lead */
										esc_html__( 'Project lead: %s', 'tgit-quarterly' ),
										esc_html( $project['head'] )
									);
									?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</section>

		<?php if ( tgit_quarterly_project_comments_enabled( $post_id ) ) : ?>
			<section class="tgit-project-comments-wrap" data-project-comments>
				<div class="mx-auto max-w-6xl px-4 md:px-6">
					<div class="scroll-reveal" style="--reveal-delay: 0s">
						<h2 class="mb-3 font-editorial text-3xl font-semibold text-foreground">
							<?php esc_html_e( 'Leave a Reply', 'tgit-quarterly' ); ?>
						</h2>
						<p class="mb-8 font-body text-sm text-muted-foreground">
							<?php esc_html_e( 'Share your feedback on this project.', 'tgit-quarterly' ); ?>
						</p>
					</div>

					<div class="tgit-project-comments-grid">
						<div class="scroll-reveal tgit-project-comments-form-col" style="--reveal-delay: 0.05s">
							<form data-comment-form class="tgit-project-comment-form">
								<input
									type="text"
									name="name"
									placeholder="<?php esc_attr_e( 'Your Name', 'tgit-quarterly' ); ?>"
									class="w-full rounded-sm border border-border bg-background px-4 py-3 font-body text-sm text-foreground placeholder-muted-foreground focus:border-foreground focus:outline-none"
									required
								/>
								<textarea
									name="comment"
									placeholder="<?php esc_attr_e( 'Write your message here...', 'tgit-quarterly' ); ?>"
									class="mt-4 w-full rounded-sm border border-border bg-background px-4 py-3 font-body text-sm text-foreground placeholder-muted-foreground focus:border-foreground focus:outline-none"
									rows="7"
									required
								></textarea>
								<div class="mt-4 flex items-center justify-between gap-4">
									<button
										type="submit"
										class="rounded-full bg-primary px-6 py-3 font-body text-xs font-semibold uppercase tracking-[0.18em] text-primary-foreground transition-all hover:opacity-90"
									>
										<?php esc_html_e( 'Send Feedback', 'tgit-quarterly' ); ?>
									</button>
									<p class="font-body text-xs text-muted-foreground">
										<?php esc_html_e( 'Required fields are marked.', 'tgit-quarterly' ); ?>
									</p>
								</div>
							</form>
						</div>

						<div class="scroll-reveal tgit-project-comments-list-col" style="--reveal-delay: 0.1s">
							<div data-comments-list class="space-y-4">
								<!-- Comments will be populated here via JavaScript -->
							</div>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $related_projects ) ) : ?>
			<section class="tgit-related-projects py-20 md:py-24">
				<div class="mx-auto max-w-6xl px-4 md:px-6">
					<div class="scroll-reveal mb-12" style="--reveal-delay: 0s">
						<h2 class="max-w-2xl font-editorial text-3xl font-semibold text-foreground">
							<?php esc_html_e( 'Related items', 'tgit-quarterly' ); ?>
						</h2>
					</div>

					<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
						<?php foreach ( $related_projects as $index => $related_post ) : ?>
							<?php
								$related_project = tgit_quarterly_project_payload( $related_post );
								$related_icon_name = isset( $category_icons[ $related_project['category'] ] ) ? $category_icons[ $related_project['category'] ] : 'cloud';
								$delay_val = number_format( $index * 0.05, 2, '.', '' );
								$delay_style = '--reveal-delay: ' . $delay_val . 's';
							?>
							<a
								href="<?php echo esc_url( get_permalink( $related_post ) ); ?>"
								class="project-card tgit-related-card group scroll-reveal flex h-full flex-col border border-border bg-background transition-all duration-300 hover:border-foreground/20"
								style="<?php echo esc_attr( $delay_style ); ?>"
							>
								<div class="flex h-full flex-col">
									<div class="tgit-related-card-image">
										<?php if ( ! empty( $related_project['featuredImage'] ) ) : ?>
											<img
												src="<?php echo esc_url( $related_project['featuredImage'] ); ?>"
												alt="<?php echo esc_attr( (string) $related_project['name'] ); ?>"
												class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
											/>
										<?php else : ?>
											<div class="flex h-full items-center justify-center bg-secondary text-foreground">
												<?php echo tgit_quarterly_icon( $related_icon_name, 'h-8 w-8', array( 'aria-hidden' => 'true' ) ); ?>
											</div>
										<?php endif; ?>
									</div>
									<div class="flex flex-1 flex-col p-4">
										<h3 class="mb-2 font-editorial text-base font-semibold text-foreground transition-colors group-hover:text-navy-light">
											<?php echo esc_html( (string) $related_project['name'] ); ?>
										</h3>
										<p class="mb-4 flex-1 font-body text-xs leading-6 text-muted-foreground">
											<?php echo esc_html( wp_trim_words( (string) $related_project['impact'], 20 ) ); ?>
										</p>
										<div class="mt-auto flex items-center gap-1 font-body text-xs font-medium text-foreground transition-all group-hover:gap-2">
											<?php esc_html_e( 'Read more', 'tgit-quarterly' ); ?>
											<?php echo tgit_quarterly_icon( 'arrow-right', 'h-3 w-3 shrink-0', array( 'aria-hidden' => 'true' ) ); ?>
										</div>
									</div>
								</div>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</main>

<?php endwhile; ?>

<?php get_footer();
