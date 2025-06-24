<?php
/**
 * Listing Currency Selection Field
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var int $post_id
 */

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( Functions::get_option_item( 'rtcl_general_settings', 'enable_multiple_currencies', 'no' ) !== 'yes' ) {
	return;
}

$available_currencies = Functions::get_available_currencies();
// If only one or no currency is available (e.g. main currency only), don't show the dropdown.
if ( count( $available_currencies ) <= 1 ) {
	return;
}

$current_currency = $post_id ? Functions::get_listing_currency( $post_id ) : Functions::get_currency();
?>
<div class="rtcl-form-group rtcl-listing-currency-wrap">
	<label for="rtcl-listing-currency"><?php esc_html_e( 'Currency', 'classified-listing' ); ?></label>
	<select class="rtcl-form-control rtcl-select2" id="rtcl-listing-currency" name="_rtcl_listing_currency">
		<?php
		foreach ( $available_currencies as $currency_code => $currency_name ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $currency_code ),
				selected( $current_currency, $currency_code, false ),
				esc_html( $currency_name )
			);
		}
		?>
	</select>
</div>
