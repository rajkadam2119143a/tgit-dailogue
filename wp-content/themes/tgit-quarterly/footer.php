<?php
/**
 * Theme footer.
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_404() ) {
	get_template_part( 'template-parts/footer', 'editorial' );
}
wp_footer();
?>
</body>
</html>
