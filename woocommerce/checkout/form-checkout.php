<?php
/**
 * Checkout Form — Woodmart Fashion-2 Architecture
 *
 * Overrides default WooCommerce checkout template with 1:1 Woodmart structure:
 * - Clean two-column layout with 40px gap
 * - Top login & coupon notices
 * - Clean billing fields with labels above inputs
 * - Dedicated "YOUR ORDER" card with table headers, thumbnails & inline quantity stepper
 *
 * @package Neebites
 * @version 2.5.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="woodmart-checkout-wrapper">

	<?php
	// Hook for login form and coupon form toggles
	do_action( 'woocommerce_before_checkout_form', $checkout );

	// If checkout registration is disabled and not logged in, the user cannot checkout.
	if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
		echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
		return;
	}
	?>

	<!-- Free Shipping Goal Indicator (from reference photo) -->
	<?php if ( function_exists('neebites_render_free_shipping_bar') ) : ?>
		<div class="woodmart-free-shipping-box">
			<?php neebites_render_free_shipping_bar(); ?>
		</div>
	<?php endif; ?>

	<form name="checkout" method="post" class="checkout woocommerce-checkout woodmart-checkout-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="woodmart-checkout-left col2-set" id="customer_details">
				<div class="woodmart-billing-column">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>

				<div class="woodmart-shipping-column">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>

		<!-- Right Column: YOUR ORDER Card -->
		<div class="woodmart-checkout-right">
			<div class="woodmart-order-review-card">
				
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
				
				<h3 id="order_review_heading" class="woodmart-order-heading"><?php esc_html_e( 'YOUR ORDER', 'neebites' ); ?></h3>
				
				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

			</div>
		</div>

	</form>

	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div>
