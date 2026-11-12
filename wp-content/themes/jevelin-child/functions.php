<?php

/**
 * Theme functions file
 */

/**
 * Enqueue parent theme styles first
 * Replaces previous method using @import
 * <http://codex.wordpress.org/Child_Themes>
 */

require_once get_stylesheet_directory() . '/custom/api/class-api-authentication.php';
require_once get_stylesheet_directory() . '/custom/api/class-api-response.php';


add_action('wp_enqueue_scripts', 'jevelin_child_enqueue', 99);

function jevelin_child_enqueue()
{
	wp_enqueue_style('jevelin-child-style', get_stylesheet_directory_uri() . '/style.css');
	wp_enqueue_style('jevelin-child-owlcarouselcss', get_stylesheet_directory_uri() . '/css/owl.carousel.min.css');
	wp_enqueue_style('jevelin-child-star-rating', get_stylesheet_directory_uri() . '/css/star-rating-svg.css');
	//wp_enqueue_script( 'jevelin-child-scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery'),'',false );
	wp_enqueue_script('jevelin-child-owlcarouseljs', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '', false);
	// wp_enqueue_script('jevelin-child-run-prettify', get_stylesheet_directory_uri() . '/js/run_prettify.js', array('jquery'), '', false);
	wp_enqueue_style('child-prettify-css', 'https://cdn.jsdelivr.net/npm/code-prettify@0.1.0/styles/desert.css');
wp_enqueue_script('child-prettify-js', 'https://cdn.jsdelivr.net/npm/code-prettify@0.1.0/loader/run_prettify.js', array(), null, true);

	wp_enqueue_script('jevelin-child-star-rating', get_stylesheet_directory_uri() . '/js/jquery.star-rating-svg.js', array('jquery'), '', false);
	wp_enqueue_script('modal-scripts', get_stylesheet_directory_uri() . '/js/modal_scripts2.js', array('jquery'), '', false);
}

add_action('admin_enqueue_scripts', 'jevelin_child_admin_enqueue', 99);
function jevelin_child_admin_enqueue()
{
	wp_enqueue_style('jevelin-admin-style', get_stylesheet_directory_uri() . '/admin-style.css');
}

/*Portfolio Custom Shortcode*/
function portfolioShortcode()
{
	// WP_Query arguments
	$args = array(
		'post_type'         => array('fw-portfolio'),
		'post_status' 		=> array('publish'),
		'posts_per_page' 	=> -1,
		'order' 			=> 'DESC',
		'orderby'			=> 'modified',

	);
	// The Query
	$the_query = new WP_Query($args);

	// The Loop
	if ($the_query->have_posts()) {
?>
		<div id="purpose_sub_slider" class="owl-carousel subslider pupose-sub-section-slide">
			<?php
			while ($the_query->have_posts()) {
				$the_query->the_post();
				$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full', false);
			?>
				<div class="item">
					<div class="category-art  sh-portfolio-masonry2 sh-portfolio-overlay-style-overlay4">
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
function tgitstarsShortcode()
{
	// WP_Query arguments
	$args = array(
		'post_type'         => array('tgitstars'),
		'post_status' 		=> array('publish'),
		'posts_per_page' 	=> -1,
		'order' 			=> 'DESC',
		'orderby'			=> 'modified',
	);
	// The Query
	$the_query = new WP_Query($args);

	// The Loop
	if ($the_query->have_posts()) {
	?>
		<div id="tgitstar_sub_slider" class="owl-carousel star tgitstar-sub-section-slide">
			<?php
			while ($the_query->have_posts()) {
				$the_query->the_post();
				$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full', false);
			$certificate_image = get_field('certificate_image', get_the_ID());
			if ( is_array( $certificate_image ) ) {
				$img_url = isset( $certificate_image['url'] ) ? $certificate_image['url'] : '';
			} elseif ( is_string( $certificate_image ) ) {
				$img_url = $certificate_image;
			} else {
				$img_url = '';
			}
				//print_r($certificate_image['url']);die;
			?>

				<div class="item">
					<div class="sh-column">
						<div class="sh-column-wrapper">
							<div class="sh-team sh-team-style4 sh-team-social-standard" id="team_<?php echo get_the_ID(); ?>">
								<div class="sh-team-image">
									<!--<a href="<?php echo $thumbnail[0]; ?>" class="" rel="lightbox">-->
									<a href="#" data-url="<?php echo $img_url; ?>" data-toggle="modal" data-target="#myModal" class="">
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
add_action('wp_ajax_rate_my_postnow', 'rate_my_postnow');
add_action('wp_ajax_nopriv_rate_my_postnow', 'rate_my_postnow');
function rate_my_postnow()
{
	if (isset($_POST) && is_user_logged_in() == true) {
		$current_user = wp_get_current_user();
		$current_userId = $current_user->ID;

		$postId = $_POST['cpost_id'];
		$postSubmitrating = $_POST['inputrating'];

		$userVoted = get_post_meta($postId, "user_have_voted_$current_userId", true);

		if ($userVoted !== "1") {
			update_post_meta($postId, "user_have_voted_$current_userId", 1);
			update_user_meta($current_userId, "user_submitted_rating_$postId", $postSubmitrating);
			update_post_meta($postId, "user_submitted_rating_$current_userId", $postSubmitrating);

			$submitted_count = get_post_meta($postId, "submitted_rating_count", true);
			$get_total_sum = get_post_meta($postId, 'submitted_rating', true);

			if ($get_total_sum > 0) {
				$get_total_sum = ($get_total_sum + $postSubmitrating);
				$submitted_count = ($submitted_count + 1);
				update_post_meta($postId, "submitted_rating", $get_total_sum);
				update_post_meta($postId, "submitted_rating_count", $submitted_count);
			} else {
				update_post_meta($postId, "submitted_rating", $postSubmitrating);
				update_post_meta($postId, "submitted_rating_count", 1);
			}
			echo "Rating submitted successfully";
		} else {
			echo "You have already rated";
		}
	} else {
		echo "Please login to rate";
	}

	die();
}


function myscript()
{
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Portfolio carousel - Projects Highlights
			if (typeof $.fn.owlCarousel !== 'undefined' && $('#purpose_sub_slider').length) {
				$('#purpose_sub_slider').owlCarousel({
				loop: true,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplayHoverPause: true,
				margin: 10,
				responsiveClass: true,
				nav: true,
				rewind: true,
				navText: ['<i class="ti-arrow-left"></i>', '<i class="ti-arrow-right"></i>'],
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
			}
			// TGIT Stars carousel
			if (typeof $.fn.owlCarousel !== 'undefined' && $('#tgitstar_sub_slider').length) {
				$('#tgitstar_sub_slider').owlCarousel({
				loop: true,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplayHoverPause: true,
				margin: 10,
				responsiveClass: true,
				nav: true,
				rewind: true,
				navText: ['<i class="ti-arrow-left"></i>', '<i class="ti-arrow-right"></i>'],
				responsive: {
					0: {
						items: 1,
						nav: true,
						dots: false,
						loop: true
					},
					600: {
						items: 2,
						nav: true,
						dots: false,
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

			// Star rating (portfolio/rating pages)
			if ($(".svg-star-rating").length) {
				$(".svg-star-rating").starRating({
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
			callback: function(currentRating, $el) {
				var currentPostid = $el.context.dataset.postId;
				var curretTotalrating = $el.context.dataset.rating;
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type: 'post',
					data: {
						'action': 'rate_my_postnow',
						'cpost_id': currentPostid,
						'totalrating': curretTotalrating,
						'inputrating': currentRating
					},
					success: function(response) {
						console.log(response);
						jQuery('#rating_' + currentPostid + '').html(response);
					}
				});
			}
			});
			}

			// Read More / Show More toggle
			if ($('.showmore_contentclass').length && $('.custom_showmore_divarea a').length) {
				$('.custom_showmore_divarea a').toggle(function(e) {
					e.preventDefault();
					e.stopPropagation();
					$('.showmore_contentclass').css('height', 'auto');
					$('.custom_showmore_divarea span.sh-button-text').text("Read Less");
				}, function() {
					$('.showmore_contentclass').animate({ 'height': '360px' });
					$('.custom_showmore_divarea span.sh-button-text').text("Read More");
				});
				$('body').on('click', function() {
					$('.showmore_contentclass').animate({ 'height': '360px' });
					$('.custom_showmore_divarea span.sh-button-text').text("Read More");
				});
			}
		}); // end document.ready
	</script>
<?php
}
add_action('wp_footer', 'myscript');

/*Nav Menu Register*/
function register_newnav_menu()
{
	register_nav_menu('header-menu-other-pages', __('Header Menu Other pages'));
}
add_action('init', 'register_newnav_menu');

add_theme_support('comments');
function enable_comments_custom_post_type()
{
	add_post_type_support('fw-portfolio', 'comments');
}
add_action('init', 'enable_comments_custom_post_type', 11);


add_filter('comment_form_defaults', 'wpsites_comment_form_defaults');
function wpsites_comment_form_defaults($defaults)
{

	//$defaults['title_reply'] = __( 'Add Your Story' );
	$defaults['label_submit'] = __('Post a Feedback', 'jevlin');
	return $defaults;
}

/*Remove Replace howdy*/
/* function replace_howdy( $wp_admin_bar ) {
 $my_account=$wp_admin_bar->get_node('my-account');
 $newtitle = str_replace( 'Howdy,', '', $my_account->title );
 $wp_admin_bar->add_node( array('id' => 'my-account','title' => $newtitle,));
 }
 add_filter( 'admin_bar_menu', 'replace_howdy',25 ); */

if (!is_super_admin()) {
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
function postRequest($url)
{

	$curlObj = curl_init();
	$username = 'tgituser';
	$password = 'Tg!tu$er';
	curl_setopt($curlObj, CURLOPT_URL, $url);
	//curl_setopt($curlObj, CURLOPT_USERPWD, $username . ":" . $password);  
	curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($curlObj, CURLOPT_TIMEOUT,        60);
	curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
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

function postreq_get_content($url)
{
	$responsedata = file_get_contents($url);
	return $responsedata;
}


function check_login_user()
{
    // Only run on allowed site IDs defined in wp-config.php
    $allowed_sites = array_map('intval', explode(',', ALLOWED_SITE_IDS));

    if (!in_array(get_current_blog_id(), $allowed_sites)) {
        return; // Stop: this site is not allowed to run authentication
    }

    // Old ALL_ADMIN not used now (site admins auto-detected)
    // $all_policy_admin = explode(',', ALL_ADMIN);

    if (!is_user_logged_in() && $GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

        if (!empty($_COOKIE['i_s']) && (empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']))) {

            $apiResponse = new apiResponse();
            $api_response = $apiResponse->curlresponse();

            if ($api_response['resp'] == 'true') {

                if ($user_id = email_exists($api_response['email'])) {

                    wp_set_current_user($user_id);
                    $my_user = wp_get_current_user();

                    /* NEW ADMIN LOGIC ADDED */
                    $site_admins = get_users([
                        'blog_id' => get_current_blog_id(),
                        'role'    => 'administrator',
                        'fields'  => 'ID'
                    ]);

                    if ( in_array($user_id, $site_admins) || is_super_admin($user_id) ) {
                        wp_update_user([
                            'ID'   => $user_id,
                            'role' => 'policy-admin'
                        ]);
                    }
                    /* END NEW CODE */

                    wp_update_user(array('ID' => $user_id, 'first_name' => $api_response['first_name'], 'last_name' => $api_response['last_name']));
                    update_user_meta($user_id, 'branch', $api_response['branch']);
                    update_user_meta($user_id, 'location', $api_response['location']);
                    update_user_meta($user_id, 'designation', $api_response['designation']);
                    update_user_meta($user_id, 'department', $api_response['department']);
                    update_user_meta($user_id, 'sap', $api_response['sap']);

                    if (isset($api_response['profile_picture'])) {
                        update_user_meta($user_id, 'profile_picture', $api_response['profile_picture']);
                    }
                    if (isset($api_response['manager_email'])) {
                        update_user_meta($user_id, 'manager_email', $api_response['manager_email']);
                    }

                } else {

                    $user_id = wp_create_user(strtolower($api_response['email']), "", $api_response['email']);

                    if (!is_wp_error($user_id)) {

                        wp_set_current_user($user_id);
                        $my_user = wp_get_current_user();

                        /* NEW ADMIN LOGIC ADDED (for new users) */
                        $site_admins = get_users([
                            'blog_id' => get_current_blog_id(),
                            'role'    => 'administrator',
                            'fields'  => 'ID'
                        ]);

                        if ( in_array($user_id, $site_admins) || is_super_admin($user_id) ) {
                            wp_update_user([
                                'ID'   => $user_id,
                                'role' => 'policy-admin'
                            ]);
                        }
                        /* END NEW CODE */

                        wp_insert_user(array('ID' => $user_id, 'first_name' => $api_response['first_name'], 'last_name' => $api_response['last_name']));

                        add_user_meta($user_id, 'branch', $api_response['branch']);
                        add_user_meta($user_id, 'location', $api_response['location']);
                        add_user_meta($user_id, 'designation', $api_response['designation']);
                        add_user_meta($user_id, 'department', $api_response['department']);
                        add_user_meta($user_id, 'sap', $api_response['sap']);

                        if (isset($user_info['profile_picture'])) {
                            add_user_meta($user_id, 'profile_picture', $api_response['profile_picture']);
                        }
                        if (isset($api_response['manager_email'])) {
                            add_user_meta($user_id, 'manager_email', $api_response['manager_email']);
                        }
                    } else {
                        error_log('function.php: Login based on cookie: Error creating user');
                    }
                }
            } else {
                header('WWW-Authenticate: Basic realm="Authentication Required"');
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }

        } else {

            if (!empty($_SERVER['PHP_AUTH_USER']) || !empty($_SERVER['PHP_AUTH_PW'])) {

                $apiAuthentication = new apiAuthentication();
                $user_info = $apiAuthentication->curlrequest($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

                if ($user_id = email_exists($user_info['email'])) {

                    wp_set_current_user($user_id);
                    $my_user = wp_get_current_user();

                    /* NEW ADMIN LOGIC ADDED */
                    $site_admins = get_users([
                        'blog_id' => get_current_blog_id(),
                        'role'    => 'administrator',
                        'fields'  => 'ID'
                    ]);

                    if ( in_array($user_id, $site_admins) || is_super_admin($user_id) ) {
                        wp_update_user([
                            'ID'   => $user_id,
                            'role' => 'policy-admin'
                        ]);
                    }
                    /* END NEW CODE */

                    wp_update_user(array('ID' => $user_id, 'first_name' => $user_info['first_name'], 'last_name' => $user_info['last_name']));
                    update_user_meta($user_id, 'branch', $user_info['branch']);
                    update_user_meta($user_id, 'location', $user_info['location']);
                    update_user_meta($user_id, 'designation', $user_info['designation']);
                    update_user_meta($user_id, 'department', $user_info['department']);
                    update_user_meta($user_id, 'sap', $user_info['sap']);

                    if (isset($user_info['profile_picture'])) {
                        update_user_meta($user_id, 'profile_picture', $user_info['profile_picture']);
                    }
                    if (isset($user_info['manager_email'])) {
                        update_user_meta($user_id, 'manager_email', $user_info['manager_email']);
                    }

                    setcookie('i_s', $user_info['i_s'],  $user_info['i_s_expiry'], "/", ".timesgroup.com", true, true);

                } else {

                    if (filter_var($user_info['email'], FILTER_VALIDATE_EMAIL)) {

                        $email = $user_info['email'];
                        $user_id = wp_create_user(strtolower($email), "", $email);

                        if (!is_wp_error($user_id)) {

                            wp_set_current_user($user_id);
                            $my_user = wp_get_current_user();

                            /* NEW ADMIN LOGIC ADDED (new user) */
                            $site_admins = get_users([
                                'blog_id' => get_current_blog_id(),
                                'role'    => 'administrator',
                                'fields'  => 'ID'
                            ]);

                            if ( in_array($user_id, $site_admins) || is_super_admin($user_id) ) {
                                wp_update_user([
                                    'ID'   => $user_id,
                                    'role' => 'policy-admin'
                                ]);
                            }
                            /* END NEW CODE */

                            wp_insert_user(array('ID' => $user_id, 'first_name' => $user_info['first_name'], 'last_name' => $user_info['last_name']));

                            add_user_meta($user_id, 'branch', $user_info['branch']);
                            add_user_meta($user_id, 'location', $user_info['location']);
                            add_user_meta($user_id, 'designation', $user_info['designation']);
                            add_user_meta($user_id, 'department', $user_info['department']);
                            add_user_meta($user_id, 'sap', $user_info['sap']);

                            if (isset($user_info['profile_picture'])) {
                                add_user_meta($user_id, 'profile_picture', $user_info['profile_picture']);
                            }
                            if (isset($user_info['manager_email'])) {
                                add_user_meta($user_id, 'manager_email', $user_info['manager_email']);
                            }
                        } else {
                            error_log('function.php: Error creating user -' . $_SERVER['PHP_AUTH_USER']);
                        }

                        setcookie('i_s', $user_info['i_s'],  $user_info['i_s_expiry'], "/", ".timesgroup.com", true, true);

                    } else {
                        error_log('Email not valid for-' . $_SERVER['PHP_AUTH_USER']);
                        header('WWW-Authenticate: Basic realm="Authentication Required"');
                        exit;
                    }
                }

            } else {
                header('WWW-Authenticate: Basic realm="Authentication Required"');
                echo '<script>window.close();</script>';
                exit;
            }
        }
    }
}

add_action('init', 'check_login_user', 10);

/*
 * Redirect non admin user's from wp-admin 
 */
function check_for_subscriber()
{
	if (!current_user_can('administrator') && !is_admin()) :
		wp_redirect(home_url());
		exit();
	endif;
}
add_action('admin_init', 'check_for_subscriber');

/*
 * Change comment form text.
 */
function change_comment_form_logged_in($args_logged_in, $commenter, $user_identity)
{
	$current_user = wp_get_current_user();
	if (isset($current_user) && !empty($current_user)) :
		if (isset($current_user->user_login) && $current_user->user_login != '') :
			$args_logged_in = '<p class="logged-in-as">Logged in as <span>' . $current_user->user_login . '</span></p>';
		else :
			$args_logged_in = '<p class="logged-in-as">Logged in as <span>' . $current_user->user_email . '</span></p>';
		endif;
	endif;

	return $args_logged_in;
}
add_filter('comment_form_logged_in', 'change_comment_form_logged_in', 10, 3);

/*
 *Update auth cookie expiry 
 */

function custom_auth_cookie_expiration_filter($expiration, $user_id, $remember)
{
	if ($remember) {
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
function limitNewRegistrationNotifications($behaviour)
{
	// do nothing
	if ($behaviour == 'default') {
		return;
	}

	// handle user registrations (self registered users)
	remove_action('register_new_user', 'wp_send_new_user_notifications');

	// new users added via wp-admin are created using add_user() -> edit_user() chain, NOT register_new_user()
	// @see https://developer.wordpress.org/reference/functions/add_user/
	remove_action('edit_user_created_user', 'wp_send_new_user_notifications', 10, 2);

	// notifications disabled ?
	if ($behaviour == 'none') {
		return;
	}

	// add custom callback and override the $notify setting with custom behaviour
	add_action('register_new_user', function ($user_id) use ($behaviour) {
		// trigger notification
		wp_new_user_notification($user_id, null, $behaviour);
	});
	add_action('edit_user_created_user', function ($user_id) use ($behaviour) {
		// trigger notification
		wp_new_user_notification($user_id, null, $behaviour);
	});
}

add_action('phpmailer_init', 'wse199274_intercept_registration_email');
function wse199274_intercept_registration_email($phpmailer)
{
	$admin_email = get_option('admin_email');

	# Intercept username and password email by checking subject line
	if (strpos($phpmailer->Subject, 'Your username and password info')) {
		# clear the recipient list
		$phpmailer->ClearAllRecipients();
		# optionally, send the email to the WordPress admin email
		$phpmailer->AddAddress($admin_email);
	} else {
		#not intercepted
	}
}

add_filter('the_content', 'add_post_content');

function add_post_content($content)
{
	if (is_front_page()) {
		$content .= '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title text-center" id="myModalLabel">TGIT Rewards & Recognition</h4>
					  </div>
					  <div class="modal-body">
					   <img src="" alt="">
					  </div>
					  <div class="social-sharing text-center">
				        <a class="facebook" href="" class="btn btn-social-icon btn-lg btn-facebook"><i class="fa fa-facebook"></i></a>
				        <a class="twitter" href="" class="btn btn-social-icon btn-lg btn-twitter"><i class="fa fa-twitter"></i></a>
				        <a class="linkedin" href="" class="btn btn-social-icon btn-lg btn-linkedin"><i class="fa fa-linkedin"></i></a>
				        <a target="_blank" class="whatsapp" href="" class="btn btn-social-icon btn-lg btn-whatsapp"><i class="fa fa-whatsapp"></i></a>
					<a target="_blank" class="download" href="" class="btn btn-social-icon btn-lg btn-download" download><i class="fa fa-download"></i></a>
				    </div>
					</div>
				  </div>
				</div>';
	}
	return $content;
}



function insert_fb_in_head()
{
	global $post;
	if (!is_single()) { //if it is not a post or a page
		echo '<meta property="og:title" content="TGIT Dialogue"/>';
		echo '<meta property="og:type" content="website"/>';
		echo '<meta property="og:url" content="' . get_permalink() . '"/>';
		echo '<meta property="og:site_name" content="TGIT Dialogue"/>'; //change My Website to your thing
		echo '<meta property="og:description" content="TGIT Dialogue"/>'; //change description to your thing
		echo '<meta property="og:image" content="' . get_stylesheet_directory_uri() . '/img/site-logo.jpg">';
		//echo "";
	}
	if (is_single()) {
		echo '<meta property="og:title" content="TGIT Dialogue"/>';
		echo '<meta property="og:type" content="article"/>';
		echo '<meta property="og:url" content="' . get_permalink() . '"/>';
		echo '<meta property="og:site_name" content="TGIT Dialogue"/>'; //change My Website to your thing
		echo '<meta property="og:description" content="' . get_the_excerpt() . '"/>';

		if (!has_post_thumbnail($post->ID)) { //the post does not have featured image, use a default image

			echo '<meta property="og:image" content="' . get_stylesheet_directory_uri() . '/img/site-logo.jpg">';
		} else { // assign the post featured image
			$thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
			echo '<meta property="og:image" content="' . esc_attr($thumbnail_src[0]) . '"/>';
		}
		echo "
    ";
	}
}
add_action('wp_head', 'insert_fb_in_head', 5);

//Adding the Open Graph in the Language Attributes
function add_opengraph_doctype($output)
{
	return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
}
//add_filter('language_attributes', 'add_opengraph_doctype');

function toi_subsite_url($extra = '') {
    $details = get_blog_details();

    // Build the complete URL
    $url = trailingslashit( TOI_BASE_URL . ltrim( $details->path, '/' ) );

    if ( $extra ) {
        $url .= ltrim($extra, '/');
    }

    return $url;
}
