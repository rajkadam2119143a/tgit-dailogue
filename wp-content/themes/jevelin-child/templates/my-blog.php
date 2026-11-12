<?php 
/*
* Template Name: My Blog
*/

get_header();
	$success = '';
	if( isset( $_POST['blog-submit'] ) && $_POST['blog-submit'] !='' ){			
		
		$title 			= $_POST['blog-name'];
		$blog_details 	= $_POST['blog-details'];
		$business_user 	= $_POST['business-user'];
		$butiness_value = $_POST['business-value'];
		$featuredImage 	= $_FILES['featured-image'];
		$featuredVideo 	= $_FILES['featured-video'];
		
		if( !empty($title) && !empty($blog_details) && !empty($butiness_value)  ){
			
			$fileinfo = @getimagesize($_FILES["featured-image"]["tmp_name"]);
			$width = $fileinfo[0];
			$height = $fileinfo[1];
			
				$post_data = array('post_title' => $title, 'post_content'=> $blog_details,'post_type' => 'fw-portfolio', 'post_status' => 'draft');
				
				$post_id = wp_insert_post( $post_data );
				
				if( isset($post_id) && $post_id !=''){
					if( isset( $business_user ) && $business_user !='' ){
						update_post_meta( $post_id, 'business-user-testimony', $business_user );
					}
					if( isset( $butiness_value ) && $butiness_value !='' ){
						update_post_meta( $post_id, 'business-value', $butiness_value );
					}
					
					if ( ! function_exists( 'wp_handle_upload' ) )

					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
			 
					$mimes = array(
						'bmp'  => 'image/bmp',
						'gif'  => 'image/gif',
						'jpe'  => 'image/jpeg',
						'jpeg' => 'image/jpeg',
						'jpg'  => 'image/jpeg',
						'png'  => 'image/png',
						'tif'  => 'image/tiff',
						'tiff' => 'image/tiff',
						'mp4|m4v' => 'video/mp4/mp3/wma',
						'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						'pdf' => 'application/pdf',
						'doc' => 'application/msword',);
					$overrides = array('mimes'=> $mimes,'test_form' => false);
					
					if(isset($_FILES['featured-image']) && $_FILES['featured-image']!='' ){
						$attachment_id  = media_handle_upload( 'featured-image', $post_id );
						if ( is_wp_error( $attachment_id ) ) {                       
                            $form_status = "error";
                        }else{
                             update_post_meta($post_id,'blog_id',$attachment_id);
                             set_post_thumbnail( $post_id, $attachment_id );
                             $form_status = "success";
                        } 
					}
					if(isset($_FILES['featured-video']) && $_FILES['featured-video']!='' ){
						$image1 = wp_handle_upload( $_FILES['featured-video'], $overrides );
						$attachment_img = array('guid'=> $image1['url'],'post_content'   => $image1['url'],'post_mime_type' => $image1['type'],'post_title'=> basename( $image1['file'])
						);
						$attachment_id = wp_insert_attachment( $attachment_img, $image1['file'] );
						update_post_meta( $post_id, 'blog-featured-video-ID' ,$attachment_id );
						update_post_meta( $post_id, 'featured-video' ,$image1['url'] );
					}
					$success = 1;
				}
		}else{
			$success = 2;
		}
	}
?>
<div class="container blog-dialogue">
	
	<div class="row">
	   <div class="col-md-12 col-lg-12 col-sm-12 blog-form">
		   	<div class="result-class">
			   <?php if( isset($success) && $success == 1 ){?>
					 <div class="alert alert-success">
					  	<strong>Success!</strong> Your post successfully submitted.
					 </div>
					 <?php } ?>
				<?php if( isset($success) && $success == 2 ){?>
					 <div class="alert alert-danger">
					  	<strong>Error!</strong> please fill required field.
					 </div>
				<?php } ?>	
			</div>		
			<form method="post" action="" id="blog-form" enctype="multipart/form-data">
			    
				<div class="col-md-12">
				<div class="form-group">
					<label for="country">Project Name:</label>
					<i class="fa fa-check-circle-o" aria-hidden="true"></i>
					<input type="text" class="form-input" name="blog-name" required="required">
				</div>
				</div>
				
				<div class="col-md-6 textarea-box">
				<div class="form-group file-upload-box">
					<label for="country">Project Details:</label>
					<i class="fa fa-check-circle-o" aria-hidden="true"></i>
					<span id="rchars_bd" class="small">800 Character(s) Remaining</span>
					<textarea type="text" id="blog-details" name="blog-details" class="form-textarea" required="required" maxlength="800"></textarea>
				</div>
				</div>
				
				<div class="col-md-6 textarea-box">
				<div class="form-group file-upload-box">
					<label for="country">Business User Testimony (Optional):</label>
					<span id="rchars_ut" class="small">600 Character(s) Remaining</span>
					<textarea type="text" id="business-user" name="business-user" class="form-textarea" maxlength="600"></textarea>
				</div>
				</div>
				
				<div class="col-md-12">
				<div class="form-group">
					<label for="country">Business Value:</label>
					<i class="fa fa-check-circle-o" aria-hidden="true"></i>
					<input type="text" class="form-input" name="business-value" required="required">
				</div>
				</div> 
				
				<div class="col-md-6">
				<div class="form-group file-upload-box">
				 <label for="fimage">Featured Image (Optional): </label>
				 <span class="small">Note: Image size 500 x 500px</span>
				    <input id="uploadImageFile" placeholder="No file chosen" class="upload-input">
					<div class="fileUpload upload-button">
					     <span>Upload</span>
						<input name="featured-image" id="featured-image" type="file" class="upload" value="UPLOAD" accept="image/png, image/jpeg, image/jpg, image/gif" />
					</div>
				</div>
				</div>
				
				<div class="col-md-6">
				<div class="form-group file-upload-box">
				 <label for="fvideo">Testimony Image/Video (Optional):</label>
				 <span class="small">Note: Video size less than 20 MB</span>
				    <input id="uploadVideoFile" placeholder="No file chosen" class="upload-input">
					<div class="fileUpload upload-button">
					     <span>Upload</span>
						<input name="featured-video" id="featured-video" type="file" class="upload" value="Upload" accept="image/*, video/*" />
					</div>
				</div>
				</div>
				
				<div class="col-md-12">
				<div class="form-group buttom-box">
	               <input type="submit" class="button-theme" name="blog-submit" id="blog-submit" value="SUBMIT">
				   <span class="required-box"> Field marked with <i class="fa fa-check-circle-o" aria-hidden="true"></i> is mandatory. </span>
	            </div>
				 </div>
				 <div id="circle"></div>
			</form>
			
		</div>
		
	</div>
	<!-- Post start -->
	<?php 
	$blogsArr = array(
        'numberposts' => 4,
		'post_type' => 'fw-portfolio',
		'orderby'	=> 'date',
        'status'	=> 'publish'
    );
	$posts = get_posts($blogsArr);
	?>	
	<div class="col-md-12 post-section">
		<h2>Projects</h2>
		<div class="row">
		<?php 
		foreach($posts as $post){
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full');
			
			//$title = mb_strimwidth(get_the_title($post->ID), 0, 50,  '...' );
			?>
			<div class="col-md-3">
				<div class="blog-section sh-portfolio-image">
						<a href="<?php echo get_permalink($post->ID); ?>">
							<div class="sh-portfolio-image-position">
								<img class="sh-portfolio-img" src="<?php echo $featured_image[0]; ?>" alt="" />
							</div>
							<div class="sh-portfolio-overlay sh-portfolio-overlay4">
								<div class="sh-portfolio-overlay4-container">
									<div class="sh-portfolio-overlay4-icons sh-table">
										<a href="<?php the_permalink(); ?>" class="sh-overlay-item sh-table-cell">
											<div class="sh-overlay-item-container">
												<i class="icon-link"></i>
											</div>
										</a>
										<a href="<?php echo jevelin_get_thumb( $post->ID, 'large' ); ?>" class="sh-overlay-item sh-table-cell" rel="lightbox">
											<div class="sh-overlay-item-container">
												<i class="icon-magnifier-add"></i>
											</div>
										</a>

									</div>
								</div>
							</div>
						</a>
				</div>	
				<br><h5><?php the_title(); ?></h5>
				<?php echo wp_trim_words(apply_filters('the_content',$post->post_content), 17,'...'); ?>
			</div>
		<?php } ?>	
		</div>
	</div>
	
	<!-- Post End -->
	
</div>

<script src="<?php echo TOI_BASE_URL; ?>KXP009QW1229A/wp-content/themes/jevelin-child/js/scripts.js"></script>
<?php get_footer(); ?>
