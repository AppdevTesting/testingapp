<?php

/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       2.0.6
 *
 * @var Payment $payment
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Resources\Options;

?>
<div class="pricing-info">
	<h2><?php esc_html_e( 'Details', 'classified-listing' ); ?></h2>
	<table class="rtcl-bs-table rtcl-bs-table-bordered rtcl-bs-table-striped">
		<tr>
			<th colspan="2">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo get_the_title( $payment->get_listing_id() );
				?>
				(<span class="listing-id">
					<?php
					esc_html_e( 'ID#', 'classified-listing' );
					echo absint( $payment->get_listing_id() )
					?>
				</span>)
			</th>
		</tr>
		<tr>
			<td class="text-right rtcl-vertical-middle"><?php esc_html_e( 'Pricing Option ', 'classified-listing' ); ?></td>
			<td><?php echo esc_html( $payment->pricing->getTitle() ); ?></td>
		</tr>
		<tr>
			<td class="text-right rtcl-vertical-middle"><?php esc_html_e( 'Features ', 'classified-listing' ); ?></td>
			<td class="rtcl-pricing-details-dp">
				<div class="rtcl-pricing-features">
					<?php
					printf(
						'<span>%d %s</span>',
						absint( $payment->pricing->getVisible() ),
						esc_html__( 'Days', 'classified-listing' )
					);
					$promotions = Options::get_listing_promotions();
					foreach ( $promotions as $promo_id => $promotion ) {
						if ( $payment->pricing->hasPromotion( $promo_id ) ) {
							echo '<span class="badge rtcl-badge-' . esc_attr( $promo_id ) . '">' . esc_html( $promotion ) . '</span>';
						}
					} ?>
				</div>
				<?php
				$description = $payment->pricing->getDescription();
				if ( ! empty( $description ) ) {
					?>
					<div class="pricing-description"><?php Functions::print_html( nl2br( $description ) ); ?></div>
				<?php } ?>
			</td>
		</tr>
		<?php do_action( 'rtcl_payment_receipt_details_before_total_amount', $payment ); ?>
		<tr>
			<td class="text-right rtcl-vertical-middle"><?php esc_html_e( 'Amount ', 'classified-listing' ); ?></td>
			<!-- $order->get_total() -->
			<td>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Functions::get_payment_formatted_price_html( $payment->get_total() );
				?>
			</td>
		</tr>

	</table>
</div>
