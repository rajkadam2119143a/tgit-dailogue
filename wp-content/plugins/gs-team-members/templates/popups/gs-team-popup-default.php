<?php
namespace GSTEAM;
if ( $gs_teammembers_pop_clm == 'one' ) : ?>

    <div class="gs_team_popup_details gs-tm-sicons popup-one-column">
        
        <!-- Team Image -->
        <div class="clearfix">
            <?php member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
        </div>
        <?php do_action( 'gs_team_after_member_thumbnail_popup' ); ?>

        <!-- Member Name -->
        <?php member_name( $id, true, false, 'single_page', 'h2' ); ?>
        <?php do_action( 'gs_team_after_member_name' ); ?>

        <!-- Member Designation -->
        <?php if ( !empty( $designation ) ): ?>
            <div class="gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></div>
            <?php do_action( 'gs_team_after_member_designation' ); ?>
        <?php endif; ?>

        <!-- Social Links -->
        <?php include Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>

        <!-- Description -->
        <div class="gs-member-desc <?php echo $gs_desc_scroll_contrl == 'on' ? 'gs-team--scrollbar' : ''; ?>" itemprop="description"><?php echo wpautop( do_shortcode( get_the_content() ) ); ?></div>
        <?php do_action( 'gs_team_after_member_details' ); ?>
        
        <!-- Meta Details -->
        <?php include Template_Loader::locate_template( 'partials/gs-team-layout-meta-details.php' ); ?>

        <!-- Skills -->
        <?php include Template_Loader::locate_template( 'partials/gs-team-layout-skills.php' ); ?>

    </div>

<?php else: ?>
    <?php

$post_id   = get_the_ID();
$post_url  = get_permalink($post_id); // ✅ Share THIS
$image     = get_field('certificate_image', $post_id); // ACF image field (Array)

if ($image):
?>
    <div class="certificate-container" style="text-align: center;">
        <img src="<?php echo esc_url($image['url']); ?>" alt="Certificate" style="max-width:100%; height:auto;"/>
        
        <div class="social-icons" style="margin-top: 0px;font-size: 24px;padding: 20px;">

            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($post_url); ?>" target="_blank" style="margin: 0 10px;">
                <i class="fab fa-facebook-square" style="color: #CA171D;"></i>
            </a>

            <!-- Twitter (X) -->
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($post_url); ?>" target="_blank" style="margin: 0 10px;">
                <i class="fab fa-x-twitter" style="color: #CA171D;"></i>
            </a>

            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($post_url); ?>" target="_blank" style="margin: 0 10px;">
                <i class="fab fa-linkedin" style="color: #CA171D;"></i>
            </a>

            <!-- WhatsApp -->
            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . $post_url); ?>" 
            target="_blank" 
            style="margin: 0 10px;">
                <i class="fab fa-whatsapp" style="color: #CA171D;"></i>
            </a>


            <!-- Download (still image URL) -->
            <a href="<?php echo esc_url($image['url']); ?>" download style="margin: 0 10px;">
                <i class="fas fa-download" style="color: #CA171D;"></i>
            </a>

        </div>
    </div>
<?php
endif;
?>


<?php endif; ?>