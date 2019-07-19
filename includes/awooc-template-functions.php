<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Модальное окно
 *
 * @see   awooc_popup_window_title()
 * @see   awooc_popup_window_image()
 * @see   awooc_popup_window_price()
 * @see   awooc_popup_window_sku()
 * @see   awooc_popup_window_sku()
 * @see   awooc_popup_window_attr()
 * @see   awooc_popup_window_qty()
 *
 * @since 1.8.0
 * @since 2.1.0
 */
add_action( 'awooc_popup_before_column', 'awooc_popup_window_title', 10, 2 );

add_action( 'awooc_popup_column_left', 'awooc_popup_window_image', 10, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_price', 20, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_sku', 30, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_attr', 40, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_qty', 50, 2 );

if ( ! function_exists( 'awooc_mode_classes' ) ) {
	/**
	 * Вспомогательная функция вывода класса в зависимости от режима работы
	 *
	 * @return string
	 *
	 * @since 2.1.4
	 *
	 */
	function awooc_mode_classes() {

		$mode_classes     = '';
		$show_add_to_card = get_option( 'woocommerce_awooc_mode_catalog' );

		switch ( $show_add_to_card ) {
			case 'dont_show_add_to_card':
				$mode_classes = 'dont-show-add-to-card';
				break;
			case 'show_add_to_card':
				$mode_classes = 'show-add-to-card';
				break;
			case 'in_stock_add_to_card':
				$mode_classes = 'in-stock-add-to-card';
				break;
		}

		return $mode_classes;
	}
}
if ( ! function_exists( 'awooc_html_custom_add_to_cart' ) ) {

	/**
	 * Displaying the button add to card in product page
	 *
	 * @since 1.5.0
	 * @since 2.1.4
	 *
	 * @param array $args
	 * @param null  $product
	 *
	 */
	function awooc_html_custom_add_to_cart( $args = array(), $product = null ) {

		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		$defaults = array(
			'href'       => '#awooc',
			'product_id' => $product->get_id(),
			'class'      => apply_filters( 'awooc_classes_button', 'awooc-custom-order button alt ' . awooc_mode_classes() ),
			'id'         => apply_filters( 'awooc_id_button', 'awooc-custom-order-button' ),
			'label'      => apply_filters( 'awooc_button_label', get_option( 'woocommerce_awooc_title_button' ) ),
		);

		$args = apply_filters( 'awooc_button_args', wp_parse_args( $args, $defaults ), $product );

		ob_start();

		do_action( 'awooc_before_button' );

		?>
		<a
			href="<?php echo esc_url( $args['href'] ); ?>"
			data-value-product-id="<?php echo esc_attr( $args['product_id'] ); ?>"
			class="<?php echo esc_attr( $args['class'] ); ?>"
			id="<?php echo esc_attr( $args['id'] ); ?>"
			<?php do_action( 'awooc_attributes_button' ); ?>>
			<?php echo esc_html( trim( $args['label'] ) ); ?>
		</a>

		<?php

		do_action( 'awooc_after_button' );

		echo ob_get_clean();// WPCS: XSS ok.
	}
}

if ( ! function_exists( 'awooc_popup_window_title' ) ) {
	/**
	 * Displaying the product header in a popup window
	 *
	 * @param $elements
	 * @param $product
	 *
	 * @since 1.5.0
	 * @since 1.8.10
	 */
	function awooc_popup_window_title( $elements, $product ) {

		if ( in_array( 'title', $elements, true ) ) {
			echo wp_kses_post(
				apply_filters(
					'awooc_popup_title_html',
					sprintf(
						'<h2 class="%s"></h2>',
						esc_attr( 'awooc-form-custom-order-title' )
					),
					$product
				)
			);
		}
	}
}

if ( ! function_exists( 'awooc_popup_window_image' ) ) {
	/**
	 * Output of a product thumbnail in a popup window
	 *
	 * @param $elements
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 */
	function awooc_popup_window_image( $elements ) {

		if ( in_array( 'image', $elements, true ) ) {

			echo '<div class="awooc-form-custom-order-img"></div>';

		}
	}
}

if ( ! function_exists( 'awooc_popup_window_price' ) ) {
	/**
	 * Output of a product price in a popup window
	 *
	 * @param $elements
	 * @param $product
	 *
	 * @since 1.5.0
	 * @since 1.8.10
	 */
	function awooc_popup_window_price( $elements, $product ) {

		if ( in_array( 'price', $elements, true ) ) {

			echo wp_kses_post(
				apply_filters(
					'awooc_popup_price_html',
					'<div class="awooc-form-custom-order-price"></div>',
					$product
				)
			);

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_sku' ) ) {
	/**
	 * Output of a product sku in a popup window
	 *
	 * @param $elements
	 *
	 * @since 1.5.0
	 * @since 1.5.0
	 */
	function awooc_popup_window_sku( $elements ) {

		if ( in_array( 'sku', $elements, true ) ) {

			echo '<div class="awooc-form-custom-order-sku"></div>';

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_qty' ) ) {
	/**
	 * Output of a product quantity in a popup window
	 *
	 * @param $elements
	 *
	 * @since 2.1.0
     * @todo - need to rework this to be translation compatible, atm doesn't work - modified label
	 */
	function awooc_popup_window_qty( $elements ) {

		if ( in_array( 'qty', $elements, true ) ) {

			echo '<div class="awooc-form-custom-order-qty"></div><br><p>*19% VAT for EU locations</p>';

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_attr' ) ) {
	/**
	 * Output of a product attributes in a popup window
	 *
	 * @param $elements
	 *
	 *
	 * @since 1.5.0
	 * @since 1.8.9
     * @todo - need to make this an option (removed label)
	 */
	function awooc_popup_window_attr( $elements ) {

		if ( in_array( 'attr', $elements, true ) ) {
			echo '<div class="awooc-form-custom-order-attr"></div>';
		}

	}
}

if ( ! function_exists( 'awooc_popup_window_select_form' ) ) {
	/**
	 * Output form in a popup window
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 *
	 */
	function awooc_popup_window_select_form() {

		$select_form = get_option( 'woocommerce_awooc_select_form' );
		if ( $select_form ) {
			do_action( 'awooc_popup_before_form' );

			if ( apply_filters( 'awooc_using_cf7', true ) ) {
				echo do_shortcode( '[contact-form-7 id="' . esc_attr( $select_form ) . '"]' );
			}

			do_action( 'awooc_popup_after_form' );
		}

	}
}
