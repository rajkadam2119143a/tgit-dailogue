<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 */

$custom_url = jevelin_post_option( get_the_ID(), 'custom_url' );
if( $custom_url ) :
	wp_redirect( esc_url( $custom_url ) );
	exit;
endif;

$fw_ext_projects_gallery_image = fw()->extensions->get( 'portfolio' )->get_config( 'image_sizes' );
$fw_ext_projects_gallery_image = $fw_ext_projects_gallery_image['gallery-image'];

$ext_portfolio_instance = fw()->extensions->get( 'portfolio' );
$ext_portfolio_settings = $ext_portfolio_instance->get_settings();
$portfolio_categories_url = $ext_portfolio_settings['taxonomy_slug'];
/*dss*/
if( defined('FW') ) :
	$elements = jevelin_option( 'post_elements' );
	if( jevelin_option( 'post_layout' ) == 'sidebar-left' || jevelin_option( 'post_layout' ) == 'sidebar-right' ) :
		$layout_sidebar = esc_attr( jevelin_option( 'post_layout' ) );
	endif;
else :
	$layout_sidebar = 'sidebar-right';
endif;

get_header(); ?>

	<?php
		$prev_post = get_previous_post();
		$next_post = get_next_post();
		$page_switcher = '<div class="sh-page-switcher">';

		if( isset($prev_post->ID) && get_permalink($prev_post->ID) ) :
			$page_switcher.= '<a class="sh-page-switcher-button" href="'.esc_url( get_permalink($prev_post->ID) ).'"><i class="ti-arrow-left"></i></a>';
		else :
			$page_switcher.= '<a class="sh-page-switcher-button sh-page-switcher-disabled" href="#"><i class="ti-arrow-left"></i></a>';
		endif;

		if( isset($next_post->ID) && get_permalink($next_post->ID) ) :
			$page_switcher.= '<a class="sh-page-switcher-button" href="'.esc_url( get_permalink($next_post->ID) ).'"><i class="ti-arrow-right"></i></a>';
		else :
			$page_switcher.= '<a class="sh-page-switcher-button sh-page-switcher-disabled" href="#"><i class="ti-arrow-right"></i></a>';
		endif;

		$page_switcher.= '</div>';
		
	?>
	
<div class="blog-single blog-style-large">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if( jevelin_post_option( get_the_ID(), 'style' ) == 'video-slider' ) : ?>

			<div class="sh-portfolio-single-slider">
				<div class="sh-portfolio-single-video">
					<div class="sh-ratio">
						<div class="sh-ratio-container">
							<div class="sh-ratio-content">
								<?php echo wp_oembed_get( esc_url(jevelin_post_option( get_the_ID(), 'video' )) ); ?>
							</div>
						</div>
					</div>
				</div>

				<div class="sh-portfolio-single row">
					<div class="sh-portfolio-single-right col-md-8">

						<div class="sh-portfolio-single-buttons">
							<?php echo wp_kses_post( $page_switcher ); ?>
						</div>

						<h1 class="sh-portfolio-single-title"><?php the_title(); ?></h1>
						<div class="sh-portfolio-single-description"><?php the_content(); ?></div>

					</div>
					<div class="sh-portfolio-single-left col-md-4">
						<?php if( jevelin_option('portfolio_share', true) == true ) : ?>
					    	<?php jevelin_share( 'portfolio' ); ?>
					    <?php endif; ?>
						<div class="sh-portfolio-single-info">
							<?php if( jevelin_post_option( get_the_ID(), 'info' ) ) : ?>
								<?php foreach ( jevelin_post_option( get_the_ID(), 'info' ) as $info ) : ?>
									<div class="sh-portfolio-single-info-item sh-table">
										<?php if( $info['icon'] ) : ?>
											<div class="sh-portfolio-single-info-left sh-table-cell-top">
												<i class="<?php echo esc_attr( $info['icon'] ); ?>"></i>
											</div>
										<?php endif; ?>
										<div class="sh-portfolio-single-info-right sh-table-cell-top">
											<div><strong><?php echo esc_attr( $info['title'] ); ?></strong></div>
											<div><?php echo jevelin_link_it( wp_kses_post( $info['description'] ) ); ?></div>
										</div>
									</div>
								<?php endforeach ?>
							<?php endif; ?>

							<?php if( jevelin_post_option( get_the_ID(), 'field_client' ) ) : ?>
								<div class="sh-portfolio-single-info-item sh-table">
									<div class="sh-portfolio-single-info-left sh-table-cell-top">
										<i class="icon-briefcase"></i>
									</div>
									<div class="sh-portfolio-single-info-right sh-table-cell-top">
										<div><strong><?php esc_html_e( 'Client', 'jevelin' ); ?></strong></div>
										<div><?php echo esc_attr( jevelin_post_option( get_the_ID(), 'field_client' ) ); ?></div>
									</div>
								</div>
							<?php endif; ?>
							<?php if( jevelin_post_option( get_the_ID(), 'field_link' ) ) : ?>
								<div class="sh-portfolio-single-info-item sh-table">
									<div class="sh-portfolio-single-info-left sh-table-cell-top">
										<i class="icon-link"></i>
									</div>
									<div class="sh-portfolio-single-info-right sh-table-cell-top">
										<div><strong><?php esc_html_e( 'URL', 'jevelin' ); ?></strong></div>
										<div>
											<a href="<?php echo esc_url( jevelin_post_option( get_the_ID(), 'field_link' )); ?>" target="_blank" class="sh-accent-color">
												<?php echo jevelin_gethost( esc_url( jevelin_post_option( get_the_ID(), 'field_link' ) ) ); ?>
											</a>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<!--<div class="sh-portfolio-single-info-item sh-table">
								<div class="sh-portfolio-single-info-left sh-table-cell-top">
									<i class="icon-folder-alt"></i>
								</div>
								<div class="sh-portfolio-single-info-right sh-table-cell-top">
									<div><strong><?php esc_html_e( 'Categories','jevelin' ); ?></strong></div>
									<div>
										<?php
										$categories = wp_get_post_terms( get_the_ID(), 'fw-portfolio-category', array("fields" => "all"));
											foreach($categories as $category) :
												echo '<a href="'.esc_attr( get_home_url('/') ).'/'.esc_attr( $portfolio_categories_url ).'/'.esc_attr( $category->slug ).'/" class="sh-default-color">'.esc_attr( $category->name ).'</a>';
												if($category !== end($categories)) :
													echo ', ';
												endif;
											endforeach;
										?>
									</div>
								</div>
							</div>-->
						</div>
					</div>
				</div>
			</div>

		<?php elseif( jevelin_post_option( get_the_ID(), 'style' ) == 'iframe-slider' ) : ?>

			<div class="sh-portfolio-single-slider">
				<div class="sh-portfolio-single-video">
					<div class="sh-ratio">
						<div class="sh-ratio-container">
							<div class="sh-ratio-content">
								<?php echo '<iframe src="'.esc_url(jevelin_post_option( get_the_ID(), 'iframe' )).'"></iframe>'; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="sh-portfolio-single row">
					<div class="sh-portfolio-single-right col-md-8">

						<div class="sh-portfolio-single-buttons">
							<?php echo wp_kses_post( $page_switcher ); ?>
						</div>

						<h1 class="sh-portfolio-single-title"><?php the_title(); ?></h1>
						<div class="sh-portfolio-single-description"><?php the_content(); ?></div>

					</div>
					<div class="sh-portfolio-single-left col-md-4">
						<?php if( jevelin_option('portfolio_share', true) == true ) : ?>
					    	<?php jevelin_share( 'portfolio' ); ?>
					    <?php endif; ?>
						<div class="sh-portfolio-single-info">
							<?php if( jevelin_post_option( get_the_ID(), 'info' ) ) : ?>
								<?php foreach ( jevelin_post_option( get_the_ID(), 'info' ) as $info ) : ?>
									<div class="sh-portfolio-single-info-item sh-table">
										<?php if( $info['icon'] ) : ?>
											<div class="sh-portfolio-single-info-left sh-table-cell-top">
												<i class="<?php echo esc_attr( $info['icon'] ); ?>"></i>
											</div>
										<?php endif; ?>
										<div class="sh-portfolio-single-info-right sh-table-cell-top">
											<div><strong><?php echo esc_attr( $info['title'] ); ?></strong></div>
											<div><?php echo jevelin_link_it( wp_kses_post( $info['description'] ) ); ?></div>
										</div>
									</div>
								<?php endforeach ?>
							<?php endif; ?>

							<?php if( jevelin_post_option( get_the_ID(), 'field_client' ) ) : ?>
								<div class="sh-portfolio-single-info-item sh-table">
									<div class="sh-portfolio-single-info-left sh-table-cell-top">
										<i class="icon-briefcase"></i>
									</div>
									<div class="sh-portfolio-single-info-right sh-table-cell-top">
										<div><strong><?php esc_html_e( 'Client', 'jevelin' ); ?></strong></div>
										<div><?php echo esc_attr( jevelin_post_option( get_the_ID(), 'field_client' ) ); ?></div>
									</div>
								</div>
							<?php endif; ?>
							<?php if( jevelin_post_option( get_the_ID(), 'field_link' ) ) : ?>
								<div class="sh-portfolio-single-info-item sh-table">
									<div class="sh-portfolio-single-info-left sh-table-cell-top">
										<i class="icon-link"></i>
									</div>
									<div class="sh-portfolio-single-info-right sh-table-cell-top">
										<div><strong><?php esc_html_e( 'URL', 'jevelin' ); ?></strong></div>
										<div>
											<a href="<?php echo esc_url( jevelin_post_option( get_the_ID(), 'field_link' )); ?>" target="_blank" class="sh-accent-color">
												<?php echo jevelin_gethost( esc_url( jevelin_post_option( get_the_ID(), 'field_link' ) ) ); ?>
											</a>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<!--<div class="sh-portfolio-single-info-item sh-table">
								<div class="sh-portfolio-single-info-left sh-table-cell-top">
									<i class="icon-folder-alt"></i>
								</div>
								<div class="sh-portfolio-single-info-right sh-table-cell-top">
									<div><strong><?php esc_html_e( 'Categories','jevelin' ); ?></strong></div>
									<div>
										<?php
										$categories = wp_get_post_terms( get_the_ID(), 'fw-portfolio-category', array("fields" => "all"));
											foreach($categories as $category) :
												echo '<a href="'.esc_attr( get_home_url('/') ).'/'.esc_attr( $portfolio_categories_url ).'/'.esc_attr( $category->slug ).'/" class="sh-default-color">'.esc_attr( $category->name ).'</a>';
												if($category !== end($categories)) :
													echo ', ';
												endif;
											endforeach;
										?>
									</div>
								</div>
							</div>-->
						</div>
					</div>
				</div>
			</div>


		<?php elseif( jevelin_post_option( get_the_ID(), 'style' ) == 'slider' ) : ?>

			<div class="sh-portfolio-single-slider">
				<div class="sh-gallery">

					<?php
						$thumbnail_id = get_post_thumbnail_id();
						$url = jevelin_get_small_thumb( $thumbnail_id, 'full' );
					?>

					<div class="sh-ratio">
						<div class="sh-ratio-container">
							<div class="sh-ratio-content" style="background-image: url(<?php echo esc_url($url); ?>);">

								<div class="sh-overlay-style1">
									<div class="sh-table-full">
										<a href="<?php echo esc_url( $url ); ?>" class="sh-overlay-lightbox sh-table-cell" rel="sh-lightbox">
											<i class="icon-magnifier-add"></i>
										</a>
									</div>
								</div>

							</div>
						</div>
					</div>

					<?php foreach ( fw_ext_portfolio_get_gallery_images() as $thumbnail ) :
						$url = jevelin_get_small_thumb( $thumbnail['attachment_id'], 'full' );
					?>

						<div class="sh-ratio">
							<div class="sh-ratio-container">
								<div class="sh-ratio-content" style="background-image: url(<?php echo esc_url($url); ?>);">

									<div class="sh-overlay-style1">
										<div class="sh-table-full">
											<a href="<?php echo esc_url( $url ); ?>" class="sh-overlay-lightbox sh-table-cell" rel="sh-lightbox">
												<i class="icon-magnifier-add"></i>
											</a>
										</div>
									</div>

								</div>
							</div>
						</div>

					<?php endforeach ?>
				</div>

				<div class="sh-portfolio-single row">
					<div class="sh-portfolio-single-right col-md-8">

						<div class="sh-portfolio-single-buttons">
							<?php echo wp_kses_post( $page_switcher ); ?>
						</div>

						<h1 class="sh-portfolio-single-title"><?php the_title(); ?></h1>
						<div class="sh-portfolio-single-description"><?php the_content(); ?></div>

					</div>
					<div class="sh-portfolio-single-left col-md-4">
						<?php if( jevelin_option('portfolio_share', true) == true ) : ?>
					    	<?php jevelin_share( 'portfolio' ); ?>
					    <?php endif; ?>
						<div class="sh-portfolio-single-info">
							<?php if( jevelin_post_option( get_the_ID(), 'info' ) ) : ?>
								<?php foreach ( jevelin_post_option( get_the_ID(), 'info' ) as $info ) : ?>
									<div class="sh-portfolio-single-info-item sh-table">
										<?php if( $info['icon'] ) : ?>
											<div class="sh-portfolio-single-info-left sh-table-cell-top">
												<i class="<?php echo esc_attr( $info['icon'] ); ?>"></i>
											</div>
										<?php endif; ?>
										<div class="sh-portfolio-single-info-right sh-table-cell-top">
											<div><strong><?php echo esc_attr( $info['title'] ); ?></strong></div>
											<div><?php echo jevelin_link_it( wp_kses_post( $info['description'] ) ); ?></div>
										</div>
									</div>
								<?php endforeach ?>
							<?php endif; ?>

							<?php if( jevelin_post_option( get_the_ID(), 'field_client' ) ) : ?>
								<div class="sh-portfolio-single-info-item sh-table">
									<div class="sh-portfolio-single-info-left sh-table-cell-top">
										<i class="icon-briefcase"></i>
									</div>
									<div class="sh-portfolio-single-info-right sh-table-cell-top">
										<div><strong><?php esc_html_e( 'Client', 'jevelin' ); ?></strong></div>
										<div><?php echo esc_attr( jevelin_post_option( get_the_ID(), 'field_client' ) ); ?></div>
									</div>
								</div>
							<?php endif; ?>
							<?php if( jevelin_post_option( get_the_ID(), 'field_link' ) ) : ?>
								<div class="sh-portfolio-single-info-item sh-table">
									<div class="sh-portfolio-single-info-left sh-table-cell-top">
										<i class="icon-link"></i>
									</div>
									<div class="sh-portfolio-single-info-right sh-table-cell-top">
										<div><strong><?php esc_html_e( 'URL', 'jevelin' ); ?></strong></div>
										<div>
											<a href="<?php echo esc_url( jevelin_post_option( get_the_ID(), 'field_link' )); ?>" target="_blank" class="sh-accent-color">
												<?php echo jevelin_gethost( esc_url( jevelin_post_option( get_the_ID(), 'field_link' ) ) ); ?>
											</a>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<!--<div class="sh-portfolio-single-info-item sh-table">
								<div class="sh-portfolio-single-info-left sh-table-cell-top">
									<i class="icon-folder-alt"></i>
								</div>
								<div class="sh-portfolio-single-info-right sh-table-cell-top">
									<div><strong><?php esc_html_e( 'Categories','jevelin' ); ?></strong></div>
									<div>
										<?php
										$categories = wp_get_post_terms( get_the_ID(), 'fw-portfolio-category', array("fields" => "all"));
											foreach($categories as $category) :
												echo '<a href="'.esc_attr( get_home_url('/') ).'/'.esc_attr( $portfolio_categories_url ).'/'.esc_attr( $category->slug ).'/" class="sh-default-color">'.esc_attr( $category->name ).'</a>';
												if($category !== end($categories)) :
													echo ', ';
												endif;
											endforeach;
										?>
									</div>
								</div>
							</div>-->
						</div>
					</div>
				</div>
			</div>

		<?php else : ?>

			<div class="sh-portfolio-single-default">
				<div class="sh-portfolio-single row">
					<div class="sh-portfolio-single-right col-md-5">

						<?php
							$thumbnail_id 		= get_post_thumbnail_id();
							$url 				= jevelin_get_small_thumb( $thumbnail_id, jevelin_option( 'portfolio_single_image', 'large' ) );
							$featured_video		= get_post_meta(get_the_ID(),'featured-video',true);
							$featuredProjectImageID = get_post_meta(get_the_ID(),'featured-project-image',true);
							$featuredProjectImageUrl 				= jevelin_get_small_thumb( $featuredProjectImageID, jevelin_option( 'portfolio_single_image', 'large' ) );
						?>
						<!--bkup--<a href="<?php echo esc_url( $url ); ?>" rel="sh-lightbox[showcase]" class="sh-portfolio-single-image" title="<?php echo esc_attr( jevelin_get_image_alt( $thumbnail_id ) ); ?>">
							<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( jevelin_get_image_alt( $thumbnail_id ) ); ?>" />
						</a>-->
						<a href="javascript:void(0);" class="sh-portfolio-single-image" title="<?php the_title(); ?>">
							<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( jevelin_get_image_alt( $thumbnail_id ) ); ?>" />
						</a>
						<?php
						if(isset($featuredProjectImageUrl) && $featuredProjectImageUrl !=''){?>
							<img src="<?php echo esc_url( $featuredProjectImageUrl ); ?>" alt="<?php echo esc_attr( jevelin_get_image_alt( $featuredProjectImageUrl ) ); ?>" />
						<?php } ?>
						<?php
						if(isset($featured_video) && $featured_video !=''){?>
						<div class="sh-portfolio-single-description single-videoframe">
							<video width="481" controls>
								<source src="<?php echo $featured_video; ?>" type="video/mp4">
								<source src="<?php echo $featured_video; ?>" type="video/ogg">
							</video>
							<!-- <span class="title"> <strong>Click to watch video</strong> </span> -->
						</div>
						<?php } ?>

						<div class="sh-portfolio-single-gallery row">
							<?php foreach ( fw_ext_portfolio_get_gallery_images() as $thumbnail ) :
								$attachment = get_post( $thumbnail['attachment_id'] );
								$image = jevelin_get_small_thumb( $thumbnail['attachment_id'], 'jevelin-square' );

								if( isset( $attachment->post_title ) && $attachment->post_title ) :
									$portfolio_title = $attachment->post_title;
								else:
									$portfolio_title = '';
								endif;
							?>
								<div class="sh-portfolio-single-gallery-item col-md-4 col-sm-4 col-xs-6">
									<a href="<?php echo jevelin_get_small_thumb( $thumbnail['attachment_id'], 'large' ); ?>" title="<?php echo esc_attr( $portfolio_title ); ?>" rel="sh-lightbox[showcase]">
										<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $portfolio_title ); ?>" />
									</a>
								</div>
							<?php endforeach ?>
						</div>
						<!--video code shifted code above-->
					</div>
					<div class="sh-portfolio-single-left col-md-7">
						<div class="sh-portfolio-single-whitespace hidden-md hidden-lg"></div>
						<div class="sh-portfolio-single-buttons">
							<?php echo wp_kses_post( $page_switcher ); ?>
						</div>
						<div class="sh-portfolio-single-description posted-author-box">
							<img style="width:40px;float:left" src ="<?php echo TOI_BASE_URL; ?>bccl/KXP009QW1229A/wp-content/uploads/sites/7/2025/01/380x380_usericon.png">
                            
							 <span class="posted-author">Posted by: <?php the_author(); ?> / <?php echo get_the_date('d-m-Y'); ?> </span>
							 
						</div>
						<h1 class="sh-portfolio-single-title"><?php the_title(); ?></h1>
						<span class="single-sub-heading"> <strong> Project Details: </strong>  </span>
						<div class="sh-portfolio-single-description single-bottom">
						<?php the_content(); ?>
						
						</div>
						<?php 
						$business_value 	= get_post_meta(get_the_ID(),'business-value',true); 
						$bus_usr_testimony  = get_post_meta(get_the_ID(),'business-user-testimony',true);
						
						
						if(isset($business_value) && $business_value !=''){?>
						<div class="sh-portfolio-single-description business-sub-heading">
							<strong>Business Value: </strong>
							<?php echo $business_value; ?>
						</div>
						<?php }

						if(isset($bus_usr_testimony) && $bus_usr_testimony !=''){?>
						<div class="sh-portfolio-single-description">
							<strong>Business User Testimony: </strong>
							<p><?php echo $bus_usr_testimony; ?></p>
						</div>
						<?php } 

						if( jevelin_option('portfolio_share', true) == true ) : ?>
					    	<?php jevelin_share( 'portfolio' ); ?>
					    <?php endif; ?>
						<div class="sh-portfolio-single-info">
							<?php if( jevelin_post_option( get_the_ID(), 'info' ) ) : ?>
								<?php foreach ( jevelin_post_option( get_the_ID(), 'info' ) as $info ) : ?>
									<div class="sh-portfolio-single-info-item sh-table">
										<?php if( $info['icon'] ) : ?>
											<div class="sh-portfolio-single-info-left sh-table-cell-top">
												<i class="<?php echo esc_attr( $info['icon'] ); ?>"></i>
											</div>
										<?php endif; ?>
										<div class="sh-portfolio-single-info-right sh-table-cell-top">
											<div><strong><?php echo esc_attr( $info['title'] ); ?></strong></div>
											<div><?php echo jevelin_link_it( wp_kses_post( $info['description'] ) ); ?></div>
										</div>
									</div>
								<?php endforeach ?>
							<?php endif; ?>

							<?php if( jevelin_post_option( get_the_ID(), 'field_client' ) ) : ?>
								<div class="sh-portfolio-single-info-item sh-table">
									<div class="sh-portfolio-single-info-left sh-table-cell-top">
										<i class="icon-briefcase"></i>
									</div>
									<div class="sh-portfolio-single-info-right sh-table-cell-top">
										<div><strong><?php esc_html_e( 'Client', 'jevelin' ); ?></strong></div>
										<div><?php echo esc_attr( jevelin_post_option( get_the_ID(), 'field_client' ) ); ?></div>
									</div>
								</div>
							<?php endif; ?>
							<?php if( jevelin_post_option( get_the_ID(), 'field_link' ) ) : ?>
								<div class="sh-portfolio-single-info-item sh-table">
									<div class="sh-portfolio-single-info-left sh-table-cell-top">
										<i class="icon-link"></i>
									</div>
									<div class="sh-portfolio-single-info-right sh-table-cell-top">
										<div><strong><?php esc_html_e( 'URL', 'jevelin' ); ?></strong></div>
										<div>
											<a href="<?php echo esc_url( jevelin_post_option( get_the_ID(), 'field_link' )); ?>" target="_blank" class="sh-accent-color">
												<?php echo jevelin_gethost( esc_url( jevelin_post_option( get_the_ID(), 'field_link' ) ) ); ?>
											</a>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<!--<div class="sh-portfolio-single-info-item sh-table">
								<div class="sh-portfolio-single-info-left sh-table-cell-top">
									<i class="icon-folder-alt"></i>
								</div>
								<div class="sh-portfolio-single-info-right sh-table-cell-top">
									<div><strong><?php esc_html_e( 'Categories','jevelin' ); ?></strong></div>
									<div>
										<?php
										$categories = wp_get_post_terms( get_the_ID(), 'fw-portfolio-category', array("fields" => "all"));
											foreach($categories as $category) :
												echo '<a href="'.esc_attr( get_home_url('/') ).'/'.esc_attr( $portfolio_categories_url ).'/'.esc_attr( $category->slug ).'/" class="sh-default-color">'.esc_attr( $category->name ).'</a>';
												if($category !== end($categories)) :
													echo ', ';
												endif;
											endforeach;
										?>
									</div>
								</div>
							</div>-->
						</div>
						<!--
						<div class="sh-button-container sh-button-style-1 custom_button_added">
							<a href="#comments_section" target="_self" class="sh-button sh-button-medium sh-button-icon-right">
								<span class="sh-button-icon" style="bottom: -6px;"><i class="ti-control-forward"></i></span>
								<span class="sh-button-text">Post Comment</span>
							</a>
						</div>
						-->
					</div>
				</div>
			</div>
		
	<!--Custom Commenting system-->
	<div class="sh-portfolio-single-info-item sh-table new_rating_section">
		<div class="sh-portfolio-single-info sh-table-cell-top rating_section_left">
			<h4>Rate Now:</h4>
			<?php 
				$postId = get_the_ID();
				$current_user = wp_get_current_user();
				$current_userId = $current_user->ID;

				$uservote = get_user_meta($current_userId, "user_submitted_rating_$postId", true);

				if ($uservote > 0 || current_user_can('administrator')) {
					$aleady_rated = 'aleady_rated';
				} else {
					$aleady_rated = '';
				}

				$get_total_sum = (int) get_post_meta($postId, 'submitted_rating', true);
				$total_num     = (int) get_post_meta($postId, 'submitted_rating_count', true);

				if ($total_num > 0) {
					$average_count = $get_total_sum / $total_num;
					$average_count = number_format($average_count, 2, '.', '');
				} else {
					$average_count = '0.0';
				}

				//echo '<script type="text-javascript">jQuery(".svg-star-rating").starRating({readOnly: true});</script>';
				//echo '<script type="text-javascript"></script>';
				
			?>
			<div class="svg-star-rating jq-stars <?php echo $aleady_rated; ?>" id="stars_<?php echo get_the_ID(); ?>" data-post-id="<?php echo get_the_ID(); ?>" data-rating="<?php echo $uservote; ?>"></div>
			<div class="sh-portfolio-single-info-right sh-table-cell-top rating_section_right">
			<h4><?php echo $average_count; ?></h4>
			<span><strong>(<i class="glyphicon glyphicon-user" style="color:#47c9e5;"></i><?php echo (is_numeric($total_num))? $total_num : 0;  ?> rated )</strong></span>
		</div>
		</div>
		
		<div class="ratingmessage" id="rating_<?php echo get_the_ID(); ?>"></div>
	</div>
	<?php endif; ?>
	<?php endwhile; ?>
	<!--Custom Adding Comment section Start-->
	<div id="comments_section">
		<?php
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

			if ( is_singular() ) :
				wp_enqueue_script( 'comment-reply' );
			endif;
		?>
	<!--Custom Adding Comment section End-->
	</div>
</div>
	
	
	<?php if( jevelin_option('portfolio_related', true) == true ) : ?>
		<div class="sh-portfolio-single-related">
			<h3><?php esc_html_e( 'Related items','jevelin' ); ?></h3>
			<div class="row">
				<?php
					$portfolio_items = new WP_Query(array(
						'post_type' => 'fw-portfolio',
						'posts_per_page' => 4,
						'orderby' => 'rand',
						'post_status' => 'publish',
						'post__not_in' => array( get_the_ID() )

					));
					while ( $portfolio_items->have_posts() ) : $portfolio_items->the_post();

						$categories = wp_get_post_terms( get_the_ID(), 'fw-portfolio-category', array("fields" => "names"));
						$categories2 = wp_get_post_terms( get_the_ID(), 'fw-portfolio-category', array("fields" => "all"));
						$item_category = '';
						foreach($categories2 as $category) :
							$item_category.= 'category-'.$category->slug.' ';
						endforeach;
					?>

					<div class="sh-portfolio-single-related-mini col-md-3 col-sm-6 sh-portfolio-overlay-style-overlay4">

						<div class="sh-portfolio-image">
							<a href="<?php echo get_permalink(); ?>">

								<div class="sh-portfolio-image-position">
									<img class="sh-portfolio-img" src="<?php echo jevelin_get_thumb( get_the_ID(), 'post-thumbnail' ); ?>" alt="" />
								</div>

								<div class="sh-portfolio-overlay sh-portfolio-overlay4">
									<div class="sh-portfolio-overlay4-container">
										<div class="sh-portfolio-overlay4-icons sh-table">

							                <a href="<?php the_permalink(); ?>" class="sh-overlay-item sh-table-cell">
							                    <div class="sh-overlay-item-container">
							                        <i class="icon-link"></i>
							                    </div>
							                </a>
							                <!--<a href="<?php echo jevelin_get_thumb( get_the_ID(), 'large' ); ?>" class="sh-overlay-item sh-table-cell" rel="lightbox">
							                    <div class="sh-overlay-item-container">
							                        <i class="icon-magnifier-add"></i>
							                    </div>
							                </a>-->

										</div>
									</div>
								</div>

							</a>
						</div>
						<a href="<?php the_permalink(); ?>">
							<h5>
								<?php the_title(); ?>
							</h5>
						</a>
						<div><?php echo jevelin_get_excerpt( 15, get_the_content() ); ?></div>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
	<?php endif; ?>

<?php get_footer();