<?php // @codingStandardsIgnoreLine

/**
 * Plugin Name: Art WooCommerce Order One Click
 * Plugin URI: wpruse.ru/my-plugins/order-one-click/
 * Text Domain: art-woocommerce-order-one-click
 * Domain Path: /languages
 * Description: Плагин для WooCommerce.  Включает режим каталога. Скрываются кнопки купить, появляется кнопка Заказать. Для правильной работы требуются WooCommerce и Contact Form 7
 * Version: 1.9.0
 * Author: Artem Abramovich
 * Author URI: https://wpruse.ru/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.5.2
 *
 * Copyright Artem Abramovich
 *
 *     This file is part of Art WooCommerce Order One Click,
 *     a plugin for WordPress.
 *
 *     Art WooCommerce Order One Click is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 3 of the License, or (at your option)
 *     any later version.
 *
 *     Art WooCommerce Order One Click is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$awooc_data = get_file_data(
	__FILE__,
	array(
		'ver'         => 'Version',
		'name'        => 'Plugin Name',
		'text_domain' => 'Text Domain',
	)
);

define( 'AWOOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWOOC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'AWOOC_PLUGIN_VER', $awooc_data['ver'] );
define( 'AWOOC_PLUGIN_NAME', $awooc_data['name'] );

require __DIR__ . '/includes/class-art-woo-order-one-click.php';

register_uninstall_hook( __FILE__, array( 'ArtWoo_Order_One_Click', 'uninstall' ) );

/**
 * The main function responsible for returning the ArtWoo_Order_One_Click object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php awooc_order_one_click()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object ArtWoo_Order_One_Click class object.
 */
if ( ! function_exists( 'awooc_order_one_click' ) ) {

	function awooc_order_one_click() {

		return ArtWoo_Order_One_Click::instance();
	}
}

$GLOBALS['awooc'] = awooc_order_one_click();
