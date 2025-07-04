<?php
/**
 * Listing meta
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 * @var Listing $listing 
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;

if ( ! $listing ) {
	global $listing;
}

if ( empty( $listing ) ) {
	return;
}

if ( ! $listing->can_show_date() && ! $listing->can_show_user() && ! $listing->can_show_category() && ! $listing->can_show_location() && ! $listing->can_show_views() ) {
	return;
}
?>

<ul class="rtcl-listing-meta-data">
	<?php
	if ( $listing->can_show_ad_type() ) :
		$listing_types = Functions::get_listing_types();
		$types         = ! empty( $listing_types ) && isset( $listing_types[ $listing->get_ad_type() ] ) ? $listing_types[ $listing->get_ad_type() ] : '';
		if ( $types ) {
			?>
			<li class="ad-type"><i class="rtcl-icon rtcl-icon-tags"></i>&nbsp;<?php echo esc_html( $types ); ?></li>
		<?php } ?>
	<?php endif; ?>
	<?php if ( $listing->can_show_date() ) : ?>
		<li class="updated"><i class="rtcl-icon rtcl-icon-clock"></i>&nbsp;<?php $listing->the_time(); ?></li>
	<?php endif; ?>
	<?php if ( $listing->can_show_user() ) : ?>
		<li class="author">
			<i class="rtcl-icon rtcl-icon-user"></i>
			<?php esc_html_e( 'by ', 'classified-listing' ); ?>
			<?php if ( $listing->can_add_user_link() && ! is_author() ) : ?>
				<a href="<?php echo esc_url( $listing->get_the_author_url() ); ?>"><?php $listing->the_author(); ?></a>
			<?php else : ?>
				<?php $listing->the_author(); ?>
			<?php endif; ?>
			<?php do_action( 'rtcl_after_author_meta', $listing->get_owner_id() ); ?>
		</li>
	<?php endif; ?>
	<?php
	if ( $listing->has_category() && $listing->can_show_category() ) :
		$categories = $listing->get_categories();
		if ( ! empty( $categories ) ) {
			?>
		<li class="rt-categories">
			<i class="rtcl-icon rtcl-icon-tags"></i>
			<?php
			foreach ( $categories as $category ) {
				echo $glue ?? '';
				?>
				<a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
				<?php
				$glue = '<span class="rtcl-delimiter">,</span>';
			}
			?>
		</li>
		<?php } endif; ?>
	<?php
	if ( $listing->has_location() && $listing->can_show_location() ) :
		?>
		<li class="rt-location">
			<i class="rtcl-icon rtcl-icon-location"></i> <?php $listing->the_locations( true, true ); ?>
		</li>
	<?php endif; ?>
	<?php if ( $listing->can_show_views() ) : ?>
		<li class="rt-views">
			<i class="rtcl-icon rtcl-icon-eye"> </i>
			<?php echo esc_html( sprintf( /* translators: View count */ _n( '%s view', '%s views', $listing->get_view_counts(), 'classified-listing' ), esc_html( number_format_i18n( $listing->get_view_counts() ) ) ) ); ?>
		</li>
	<?php endif; ?>
</ul>
