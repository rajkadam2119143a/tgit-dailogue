<?php
/**
 * Theme functions file
 */

/**
 * Enqueue parent theme styles first
 * Replaces previous method using @import
 * <http://codex.wordpress.org/Child_Themes>
 */

add_action( 'wp_enqueue_scripts', 'jevelin_child_enqueue', 99 );

function jevelin_child_enqueue() {
	wp_enqueue_style( 'jevelin-child-style', get_stylesheet_directory_uri() . '/style.css' );
	wp_enqueue_style( 'jevelin-child-owlcarouselcss', get_stylesheet_directory_uri() . '/css/owl.carousel.min.css' );
	wp_enqueue_style( 'jevelin-child-star-rating', get_stylesheet_directory_uri() . '/css/star-rating-svg.css' );
	//wp_enqueue_script( 'jevelin-child-scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery'),'',false );
	wp_enqueue_script( 'jevelin-child-owlcarouseljs', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'),'',false );
	wp_enqueue_script( 'jevelin-child-run-prettify', get_stylesheet_directory_uri() . '/js/run_prettify.js', array('jquery'),'',false );
	wp_enqueue_script( 'jevelin-child-star-rating', get_stylesheet_directory_uri() . '/js/jquery.star-rating-svg.js', array('jquery'),'',false );
}

add_action( 'admin_enqueue_scripts', 'jevelin_child_admin_enqueue', 99 );
function jevelin_child_admin_enqueue() {
	wp_enqueue_style( 'jevelin-admin-style', get_stylesheet_directory_uri() . '/admin-style.css' );
}

/*Portfolio Custom Shortcode*/
function portfolioShortcode(){
	// WP_Query arguments
	$args = array(
		'post_type'         => array( 'fw-portfolio' ),
		'post_status' 		=> array( 'publish' ),
		'posts_per_page' 	=> -1,
		'order' 			=> 'DESC',
		'orderby'			=> 'modified',
		
	);
	// The Query
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) { 
	?>
		<div id="purpose_sub_slider" class="owl-carousel subslider pupose-sub-section-slide">
		<?php
		while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full', false );
		?>			
		<div class="item">								
			<div class="category-art  sh-portfolio-masonry2 sh-portfolio-overlay-style-overlay4" >
				<div class="sh-portfolio-image">
				<div class="sh-portfolio-image-position">
					<div class="sh-ratio">
						<div class="sh-ratio-container sh-ratio-container-square">
						<div class="sh-ratio-content" style="background-image: url(<?php echo $thumbnail[0]; ?>);"></div>
						</div>
					</div>
				</div>
					<div class="sh-portfolio-overlay sh-portfolio-overlay4">
						<div class="sh-portfolio-overlay4-container">
						<div class="sh-portfolio-overlay4-title"><?php echo get_the_title(); ?></div>
							<div class="sh-portfolio-overlay4-icons sh-table custom_link_portfolio">
							<a href="<?php echo get_the_permalink(); ?>" class="sh-overlay-item sh-table-cell">
								<div class="sh-overlay-item-container">
									<i class="icon-link"></i>
								</div>
							</a>
							<!--<a href="<?php echo $thumbnail[0]; ?>" class="sh-overlay-item sh-table-cell" rel="lightbox">
							<div class="sh-overlay-item-container">
							<i class="icon-magnifier-add"></i>
							</div>
							</a>-->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		</div>
		<?php
		/* Restore original Post Data */
		wp_reset_postdata();
		} else {
		// no posts found
		}
		}
add_shortcode('portfolio-carousel', 'portfolioShortcode');

/*Portfolio Custom Shortcode*/
function tgitstarsShortcode(){
	// WP_Query arguments
	$args = array(
		'post_type'         => array( 'tgitstars' ),
		'post_status' 		=> array( 'publish' ),
		'posts_per_page' 	=> -1,
		'order' 			=> 'DESC',
		'orderby'			=> 'modified',
	);
	// The Query
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) {
		?>
		<div id="tgitstar_sub_slider" class="owl-carousel star tgitstar-sub-section-slide">
		<?php
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full', false );
			?>
			
	<div class="item">								
		<div class="sh-column">
			<div class="sh-column-wrapper">
				<div class="sh-team sh-team-style4 sh-team-social-standard" id="team_<?php echo get_the_ID(); ?>">
					<div class="sh-team-image">
						<!--<a href="<?php echo $thumbnail[0]; ?>" class="" rel="lightbox">-->
						<a href="javascript:void(0)" class="">
						<img src="<?php echo $thumbnail[0]; ?>" alt="">
						</a>
					</div>
						<div class="sh-team-aside">
						<div class="sh-team-name">
							<h3><?php echo get_the_title(); ?></h3>
						</div>
					<div class="sh-team-role"><?php echo get_the_excerpt(); ?></div>
					<div class="sh-team-description">
						<?php echo get_the_content(); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
		}
		?>
		</div>
		<?php
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		// no posts found
	}
}
add_shortcode('tgitstar-carousel', 'tgitstarsShortcode');

/*Rating Ajax*/
add_action('wp_ajax_rate_my_postnow','rate_my_postnow');
add_action('wp_ajax_nopriv_rate_my_postnow','rate_my_postnow');
function rate_my_postnow(){
	if(isset($_POST) && is_user_logged_in() == true){
		$current_user = wp_get_current_user(); 
		$current_userId = $current_user->ID;
		
		$postId = $_POST['cpost_id'];
		$postSubmitrating = $_POST['inputrating'];
		
		$userVoted = get_post_meta($postId , "user_have_voted_$current_userId", true);
	
		if($userVoted !== "1"){
			update_post_meta($postId , "user_have_voted_$current_userId", 1);
			update_user_meta($current_userId , "user_submitted_rating_$postId", $postSubmitrating);			
			update_post_meta($postId , "user_submitted_rating_$current_userId", $postSubmitrating);
			
			$submitted_count = get_post_meta($postId , "submitted_rating_count", true );
			$get_total_sum = get_post_meta($postId,'submitted_rating',true);
			
			if($get_total_sum > 0){
				$get_total_sum = ($get_total_sum + $postSubmitrating);
				$submitted_count = ($submitted_count + 1);
				update_post_meta($postId , "submitted_rating", $get_total_sum);
				update_post_meta($postId , "submitted_rating_count", $submitted_count );
			}
			else{
				update_post_meta($postId , "submitted_rating", $postSubmitrating);
				update_post_meta($postId , "submitted_rating_count", 1 );
			}
			echo "Rating submitted successfully";
		}
		else{
			echo "You have already rated";
		}
		
	}
	else{
		echo "Please login to rate";
	}
	
	die();
}


function myscript() {
?>
<script type="text/javascript">
  if ( undefined !== window.jQuery ) {
    // script dependent on jQuery
	jQuery('#purpose_sub_slider').owlCarousel({
		loop: true,
		autoplay:true,
		autoplayHoverPause:true,
		margin: 10,
		responsiveClass: true,
		nav: true,
		rewind:true,
		navText: ['<i class="ti-arrow-left"></i>','<i class="ti-arrow-right"></i>'],
		responsive: {
		0: {
			items: 1,
			nav: true
		},
		600: {
			items: 3,
			nav: true
		},
		1000: {
			items: 4,
			nav: true,
			loop: false,
			margin: 0
		}
		}
	});
	
	jQuery('#tgitstar_sub_slider').owlCarousel({
		loop: true,
		autoplay:true,
		autoplayHoverPause:true,
		margin: 10,
		responsiveClass: true,
		nav: true,
		rewind:true,
		navText: ['<i class="ti-arrow-left"></i>','<i class="ti-arrow-right"></i>'],
		responsive: {
		0: {
			items: 1,
			nav: true,
			dots:false,
			loop: true
		},
		600: {
			items: 2,
			nav: true,
			dots:false,
			loop: true
		},
		1000: {
			items: 3,
			nav: true,
			loop: true,
			margin: 0
		}
		}
	});
  } 
  
  jQuery(".svg-star-rating").starRating({
	  totalStars: 5,
	  starSize: 30,
	  emptyColor: '#fff',
	  hoverColor: '#47c9e5',
	  activeColor: '#47c9e5',
	  ratedColor: '#47c9e5',
	  initialRating: 0,
	  strokeWidth: 20,
	  strokeColor: '#47c9e5',
	  useGradient: false,
	  disableAfterRate: true,
	  //readOnly: true,
	  //setReadOnly: true,
	  callback: function(currentRating, $el){
			var currentPostid = $el.context.dataset.postId;
			var curretTotalrating = $el.context.dataset.rating;
			jQuery.ajax({
				url : "<?php echo admin_url('admin-ajax.php'); ?>",
				type : 'post',
				data : {
					'action' : 'rate_my_postnow',
					'cpost_id' : currentPostid,
					'totalrating' : curretTotalrating,
					'inputrating' : currentRating
				},
				success : function( response ) {
					console.log(response);
					jQuery('#rating_'+currentPostid+'').html(response);
				}
			});
		}
	});
  
  
  var h = jQuery('.showmore_contentclass')[0].scrollHeight;	
	jQuery('.custom_showmore_divarea a').toggle(function(e) {
		e.preventDefault();
		e.stopPropagation();
		//console.log(h);
		/*  $("#topbar-show").toggle(function(){
			$(this).animate({height:40},200);
		  },function(){
			$(this).animate({height:10},200);
		  }); */
		/* jQuery('.showmore_contentclass').animate({
			'height': h
		}); */
		jQuery('.showmore_contentclass').css('height','auto');		
		jQuery('.custom_showmore_divarea span.sh-button-text').text("Read Less");
		
	},function(){
		jQuery('.showmore_contentclass').animate({
			'height': '360px'
		});
		jQuery('.custom_showmore_divarea span.sh-button-text').text("Read More");
	});

	jQuery(document).click(function() {
		jQuery('.showmore_contentclass').animate({
			'height': '360px'
		});
		jQuery('.custom_showmore_divarea span.sh-button-text').text("Read More"); 
	});
</script>
<?php
}
add_action( 'wp_footer', 'myscript' );

/*Nav Menu Register*/
function register_newnav_menu() {
  register_nav_menu('header-menu-other-pages',__( 'Header Menu Other pages' ));
}
add_action( 'init', 'register_newnav_menu' );

add_theme_support( 'comments' );
function enable_comments_custom_post_type() {
 add_post_type_support( 'fw-portfolio', 'comments' );
}
add_action( 'init', 'enable_comments_custom_post_type', 11 ); 


add_filter( 'comment_form_defaults', 'wpsites_comment_form_defaults' );
function wpsites_comment_form_defaults( $defaults ) {
 
	//$defaults['title_reply'] = __( 'Add Your Story' );
	$defaults['label_submit'] = __( 'Post a Feedback', 'jevlin' );
	return $defaults;
}

/*Remove Replace howdy*/
/* function replace_howdy( $wp_admin_bar ) {
 $my_account=$wp_admin_bar->get_node('my-account');
 $newtitle = str_replace( 'Howdy,', '', $my_account->title );
 $wp_admin_bar->add_node( array('id' => 'my-account','title' => $newtitle,));
 }
 add_filter( 'admin_bar_menu', 'replace_howdy',25 ); */
 
 if(!is_super_admin()){
	 add_filter('show_admin_bar', '__return_false');
 }
/* $defaults = array(
    'fields' => apply_filters('comment_form_default_fields', $fields),
    'label_submit' => __('Post a Feedback')
);
comment_form($defaults); */

/**
* Curl send post request, support HTTPS protocol
* @param string $url The request url
* @param boolean $auth 
* @param array $data The post data
* @param string $refer The request refer
* @param int $timeout The timeout seconds
* @param array $header The other request header	
* @return mixed
*/
function postRequest($url){
	
	$curlObj = curl_init();
	$username = 'tgituser';
	$password = 'Tg!tu$er';
	curl_setopt($curlObj, CURLOPT_URL, $url );
	//curl_setopt($curlObj, CURLOPT_USERPWD, $username . ":" . $password);  
	curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($curlObj, CURLOPT_TIMEOUT,        60);
	curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curlObj, CURLOPT_HEADER,     false);
	$returnData = curl_exec($curlObj);
	if (curl_errno($curlObj)) {
		//error message
		$returnData = curl_error($curlObj);	
	}
	curl_close($curlObj);
	return $returnData;
} 
 
function postreq_get_content($url){	
	$responsedata = file_get_contents($url);
	return $responsedata;	
}


function check_login_user(){	
	if( !is_user_logged_in() && $GLOBALS['pagenow']!='wp-login.php' && !is_admin() ):
		if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) :

			header('WWW-Authenticate: Basic realm="Authentication Required"');
			header('HTTP/1.0 401 Unauthorized');
			exit;
		elseif(( isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER']!='' ) && ( isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_PW']!='' ) ):
			$url = 'https://pricewise.timesgroup.com/pw2/APIFactoryNew?page=drumrolllogin&loginId='.urlencode($_SERVER['PHP_AUTH_USER']).'&pwd='.urlencode($_SERVER['PHP_AUTH_PW']).'';			
			//$data = postRequest($url);
			$data = postreq_get_content($url);
			
			if( isset($data) && $data!='' ):
				$jsonData = json_decode($data,true);
			
				if( isset($jsonData) && !empty($jsonData) ):
					
					if( isset($jsonData['MESSAGE']) && $jsonData['MESSAGE']=="User Authentication Successful."):
						if( $user_id = email_exists($_SERVER['PHP_AUTH_USER']) ):
							// Set user current user
							wp_set_current_user($user_id);		
							// Set Auth cookie
							wp_set_auth_cookie($user_id, true);
						else:							
							$user_id = register_new_user($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_USER']);
							limitNewRegistrationNotifications('none');
							if( !is_wp_error($user_id) ):
								// Set user current user
								wp_set_current_user($user_id);		
								// Set Auth cookie
								wp_set_auth_cookie($user_id, true);
							endif;
						endif;
					else:
						header('WWW-Authenticate: Basic realm="Authentication Required"');
						header('HTTP/1.0 401 Unauthorized');
						exit;
					endif;
				endif;
			endif;	
		endif;
	endif;
}
add_action('init','check_login_user');


/*
 * Redirect non admin user's from wp-admin 
 */ 
function check_for_subscriber() {
	if( !current_user_can('administrator') && !is_admin() ):
		wp_redirect( home_url() );
        exit();
	endif;
}
add_action('admin_init','check_for_subscriber');

/*
 * Change comment form text.
 */ 
function change_comment_form_logged_in($args_logged_in, $commenter, $user_identity){
	$current_user = wp_get_current_user();	
	if( isset($current_user) && !empty($current_user) ):
		if( isset($current_user->user_login) && $current_user->user_login!='' ):
			$args_logged_in = '<p class="logged-in-as">Logged in as <span>'.$current_user->user_login.'</span></p>';
		else:
			$args_logged_in = '<p class="logged-in-as">Logged in as <span>'.$current_user->user_email.'</span></p>';
		endif;
	endif;
	
	return $args_logged_in;
}
add_filter( 'comment_form_logged_in', 'change_comment_form_logged_in', 10, 3 );

/*
 *Update auth cookie expiry 
 */

function custom_auth_cookie_expiration_filter($expiration, $user_id, $remember) {
    if ( $remember ) {
        return YEAR_IN_SECONDS;        
    }
    // default
    return $expiration;
} 
add_filter('auth_cookie_expiration', 'custom_auth_cookie_expiration_filter', 10, 3);

/*remove welcome mail*/
add_filter('send_password_change_email', '__return_false');
//add_filter( 'user_registration_email', '__return_false');


// The Parameter "$behaviour" can be set to:
// "none" (no notifications are send); 
// "default" (no changes); 
// "admin" (notifications send to admin only); 
// "user" (notification send to user only); 
// "both" (notifications send to admin + user)
function limitNewRegistrationNotifications($behaviour){
    // do nothing
    if ($behaviour == 'default'){
        return;
    }

    // handle user registrations (self registered users)
    remove_action('register_new_user', 'wp_send_new_user_notifications');

    // new users added via wp-admin are created using add_user() -> edit_user() chain, NOT register_new_user()
    // @see https://developer.wordpress.org/reference/functions/add_user/
    remove_action('edit_user_created_user', 'wp_send_new_user_notifications', 10, 2);

    // notifications disabled ?
    if ($behaviour == 'none'){
        return;
    }

    // add custom callback and override the $notify setting with custom behaviour
    add_action('register_new_user', function($user_id) use ($behaviour){
        // trigger notification
        wp_new_user_notification($user_id, null, $behaviour);
    });
    add_action('edit_user_created_user', function($user_id) use ($behaviour){
        // trigger notification
        wp_new_user_notification($user_id, null, $behaviour);
    });
}

add_action('phpmailer_init', 'wse199274_intercept_registration_email');
function wse199274_intercept_registration_email($phpmailer){
    $admin_email = get_option( 'admin_email' );

    # Intercept username and password email by checking subject line
    if( strpos($phpmailer->Subject, 'Your username and password info') ){
        # clear the recipient list
        $phpmailer->ClearAllRecipients();
        # optionally, send the email to the WordPress admin email
        $phpmailer->AddAddress($admin_email);
    }else{
        #not intercepted
    }
}

