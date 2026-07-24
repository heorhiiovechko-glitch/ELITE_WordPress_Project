<?php
/**
 * Fallback template.
 *
 * @package Elite_Shipping
 */

if ( is_front_page() ) {
	include get_template_directory() . '/front-page.php';
	return;
}

get_header();
?>
<main class="elite-wc-main">
	<div class="elite-container">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_content();
			}
		}
		?>
	</div>
</main>
<?php
get_footer();
