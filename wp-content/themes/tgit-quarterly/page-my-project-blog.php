<?php
/**
 * Template Name: My Project Blog
 * Front-end project submission (ported from jevelin-child templates/my-blog.php).
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$can_manage_project_blog = tgit_quarterly_user_can_manage_project_blog();
$result                  = $can_manage_project_blog ? tgit_quarterly_handle_project_submit() : array(
	'status'  => 'idle',
	'message' => '',
);
$posts                   = $can_manage_project_blog ? tgit_quarterly_recent_projects_for_submit_page( 4 ) : array();
?>
<main id="primary" class="site-main section-padding py-16 md:py-24" data-project-submit-page>
	<div class="mx-auto max-w-6xl px-4 md:px-6">
		<div class="mb-10 scroll-reveal" style="--reveal-delay: 0s">
			<h1 class="mb-3 font-editorial text-3xl font-semibold text-foreground md:text-4xl">
				<?php the_title(); ?>
			</h1>
			<?php if ( get_the_content() ) : ?>
				<div class="max-w-3xl font-body text-sm leading-7 text-muted-foreground">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! $can_manage_project_blog ) : ?>
			<div class="tgit-project-submit-alert tgit-project-submit-alert--error" role="alert">
				<strong><?php esc_html_e( 'Restricted', 'tgit-quarterly' ); ?></strong>
				<?php esc_html_e( 'This page is only available to site administrators.', 'tgit-quarterly' ); ?>
			</div>
		<?php else : ?>

		<?php if ( 'success' === $result['status'] ) : ?>
			<div class="tgit-project-submit-alert tgit-project-submit-alert--success" role="status">
				<strong><?php esc_html_e( 'Success!', 'tgit-quarterly' ); ?></strong>
				<?php echo esc_html( $result['message'] ); ?>
			</div>
		<?php elseif ( 'error' === $result['status'] && '' !== $result['message'] ) : ?>
			<div class="tgit-project-submit-alert tgit-project-submit-alert--error" role="alert">
				<strong><?php esc_html_e( 'Error!', 'tgit-quarterly' ); ?></strong>
				<?php echo esc_html( $result['message'] ); ?>
			</div>
		<?php endif; ?>

		<div class="tgit-project-submit-layout scroll-reveal" style="--reveal-delay: 0.05s">
			<form method="post" action="" id="blog-form" class="tgit-project-submit-form" enctype="multipart/form-data" novalidate>
				<?php wp_nonce_field( 'tgit_project_submit', 'tgit_project_nonce' ); ?>
				<input type="hidden" name="tgit_project_submit" value="1" />

				<div class="tgit-project-submit-field tgit-project-submit-field--full">
					<label for="blog-name">
						<?php esc_html_e( 'Project Name', 'tgit-quarterly' ); ?>
						<span class="tgit-required" aria-hidden="true">*</span>
					</label>
					<input type="text" id="blog-name" name="blog-name" class="tgit-project-submit-input" required maxlength="200" />
				</div>

				<div class="tgit-project-submit-grid">
					<div class="tgit-project-submit-field">
						<label for="blog-details">
							<?php esc_html_e( 'Project Details', 'tgit-quarterly' ); ?>
							<span class="tgit-required" aria-hidden="true">*</span>
						</label>
						<span id="rchars_bd" class="tgit-project-submit-counter">800 <?php esc_html_e( 'character(s) remaining', 'tgit-quarterly' ); ?></span>
						<textarea id="blog-details" name="blog-details" class="tgit-project-submit-textarea" required maxlength="800" rows="8"></textarea>
					</div>

					<div class="tgit-project-submit-field">
						<label for="business-user"><?php esc_html_e( 'Business User Testimony (Optional)', 'tgit-quarterly' ); ?></label>
						<span id="rchars_ut" class="tgit-project-submit-counter">600 <?php esc_html_e( 'character(s) remaining', 'tgit-quarterly' ); ?></span>
						<textarea id="business-user" name="business-user" class="tgit-project-submit-textarea" maxlength="600" rows="8"></textarea>
					</div>
				</div>

				<div class="tgit-project-submit-field tgit-project-submit-field--full">
					<label for="business-value">
						<?php esc_html_e( 'Business Value', 'tgit-quarterly' ); ?>
						<span class="tgit-required" aria-hidden="true">*</span>
					</label>
					<input type="text" id="business-value" name="business-value" class="tgit-project-submit-input" required maxlength="300" />
				</div>

				<div class="tgit-project-submit-grid">
					<div class="tgit-project-submit-field">
						<label for="featured-image"><?php esc_html_e( 'Featured Image (Optional)', 'tgit-quarterly' ); ?></label>
						<p class="tgit-project-submit-note"><?php esc_html_e( 'Recommended size: 500 × 500 px. JPG, PNG, or GIF.', 'tgit-quarterly' ); ?></p>
						<input id="uploadImageFile" class="tgit-project-submit-filename" placeholder="<?php esc_attr_e( 'No file chosen', 'tgit-quarterly' ); ?>" readonly />
						<label class="tgit-project-submit-upload-btn">
							<span><?php esc_html_e( 'Upload', 'tgit-quarterly' ); ?></span>
							<input name="featured-image" id="featured-image" type="file" accept="image/png,image/jpeg,image/jpg,image/gif" />
						</label>
					</div>

					<div class="tgit-project-submit-field">
						<label for="featured-video"><?php esc_html_e( 'Testimony Image/Video (Optional)', 'tgit-quarterly' ); ?></label>
						<p class="tgit-project-submit-note"><?php esc_html_e( 'Video must be less than 20 MB.', 'tgit-quarterly' ); ?></p>
						<input id="uploadVideoFile" class="tgit-project-submit-filename" placeholder="<?php esc_attr_e( 'No file chosen', 'tgit-quarterly' ); ?>" readonly />
						<label class="tgit-project-submit-upload-btn">
							<span><?php esc_html_e( 'Upload', 'tgit-quarterly' ); ?></span>
							<input name="featured-video" id="featured-video" type="file" accept="image/*,video/*" />
						</label>
					</div>
				</div>

				<div class="tgit-project-submit-actions">
					<button type="submit" class="tgit-project-submit-button" id="blog-submit" name="blog-submit">
						<?php esc_html_e( 'Submit', 'tgit-quarterly' ); ?>
					</button>
					<p class="tgit-project-submit-required-note">
						<?php esc_html_e( 'Fields marked with * are mandatory.', 'tgit-quarterly' ); ?>
					</p>
				</div>
			</form>
		</div>

		<section class="tgit-project-submit-list scroll-reveal" style="--reveal-delay: 0.1s" aria-labelledby="tgit-submit-projects-heading">
			<h2 id="tgit-submit-projects-heading" class="mb-8 font-editorial text-2xl font-semibold text-foreground md:text-3xl">
				<?php esc_html_e( 'Projects', 'tgit-quarterly' ); ?>
			</h2>
			<?php if ( empty( $posts ) ) : ?>
				<p class="font-body text-sm text-muted-foreground"><?php esc_html_e( 'Published projects will appear here.', 'tgit-quarterly' ); ?></p>
			<?php else : ?>
				<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
					<?php foreach ( $posts as $post ) : ?>
						<?php
						$project = tgit_quarterly_project_payload( $post );
						$image   = $project['featuredImage'];
						?>
						<article class="tgit-project-submit-card group">
							<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="block overflow-hidden border border-editorial-border bg-background transition-shadow hover:shadow-sm">
								<div class="tgit-project-submit-card-image">
									<?php if ( $image ) : ?>
										<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $post ) ); ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]" />
									<?php else : ?>
										<div class="flex h-full items-center justify-center bg-secondary font-editorial text-lg text-muted-foreground">
											<?php echo esc_html( tgit_quarterly_initials_from_title( get_the_title( $post ) ) ); ?>
										</div>
									<?php endif; ?>
								</div>
								<div class="p-4">
									<h3 class="mb-2 font-editorial text-base font-semibold text-foreground"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
									<p class="font-body text-xs leading-6 text-muted-foreground">
										<?php echo esc_html( wp_trim_words( $project['impact'], 17, '...' ) ); ?>
									</p>
								</div>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>

		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
