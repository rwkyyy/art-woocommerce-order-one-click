<?php // @codingStandardsIgnoreLine
/**
 * Class ArtWoo_Order_One_Click
 *
 * Main AWOOC class, initialized the plugin
 *
 * @class       ArtWoo_Order_One_Click
 * @version     1.8.0
 * @author      Artem Abramovich
 */
class ArtWoo_Order_One_Click {

	/**
	 * Instance of ArtWoo_Order_One_Click.
	 *
	 * @since  1.8.0
	 * @access private
	 * @var object $instance The instance of AWOOS_Custom_Sale.
	 */
	private static $instance;

	/**
	 * Plugin version.
	 *
	 * @since 1.8.0
	 * @var string $version Plugin version number.
	 */
	public $version;

	/**
	 * Plugin name.
	 *
	 * @since 1.8.0
	 * @var string $name Plugin name.
	 */
	public $name;

	/**
	 * @since 1.9.0
	 * @var array Required plugins.
	 */
	protected $required_plugins = array();


	/**
	 * Construct.
	 *
	 * @since 1.8.0
	 *
	 * @see   https://github.com/kagg-design/woof-by-category
	 *
	 */
	public function __construct() {

		$this->required_plugins = array(
			array(
				'plugin'  => 'woocommerce/woocommerce.php',
				'name'    => 'WooCommerce',
				'slug'    => 'woocommerce',
				'class'   => 'WooCommerce',
				'version' => '3.0',
				'active'  => false,
			),
			array(
				'plugin'  => 'contact-form-7/wp-contact-form-7.php',
				'name'    => 'Contact Form 7',
				'slug'    => 'contact-form-7',
				'class'   => 'WPCF7',
				'version' => '5.0',
				'active'  => false,
			),
		);

		$this->init();

		$this->hooks();

		$this->load_textdomain();
	}


	/**
	 * Init.
	 *
	 * Initialize plugin parts.
	 *
	 *
	 * @since 1.8.0
	 */
	public function init() {

		add_action( 'admin_init', array( $this, 'check_requirements' ) );
		add_action( 'admin_init', array( $this, 'check_php_version' ) );

		foreach ( $this->required_plugins as $required_plugin ) {
			if ( ! class_exists( $required_plugin['class'] ) ) {
				return;
			}
		}

		/**
		 * Hiding field to CF7
		 */
		include __DIR__ . '/admin/added-cf7-field.php';

		/**
		 * Front end
		 */
		include __DIR__ . '/class-awooc-frontend.php';
		$this->front_end = new AWOOC_Front_End();

		/**
		 * Ajax
		 */
		include __DIR__ . '/class-awooc-ajax.php';
		$this->ajax = new AWOOC_Ajax();

		/**
		 * Создание заказов
		 */
		include __DIR__ . '/class-awooc-orders.php';
		$this->orders = new AWOOC_Orders();

		/**
		 * Template functions
		 */
		include __DIR__ . '/awooc-template-functions.php';



		global $pagenow;
		if ( 'plugins.php' === $pagenow ) {
			// Plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ), 10, 2 );
		}

	}


	/**
	 * Hooks.
	 *
	 * Initialize all class hooks.
	 *
	 * @since 1.8.0
	 */
	public function hooks() {

		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_awooc_admin_settings' ), 15 );

	}


	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.9.0
	 */
	public function load_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'art-decoration-shortcode' );

		load_textdomain(
			'art-woocommerce-order-one-click',
			WP_LANG_DIR . '/art-decoration-shortcode/art-decoration-shortcode-' . $locale . '.mo'
		);
		load_plugin_textdomain(
			'art-woocommerce-order-one-click',
			false,
			basename( dirname( __FILE__ ) ) . '/languages'
		);

	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.8.0
	 * @return object Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


	/**
	 * Settings.
	 *
	 * Include the WooCommerce settings class.
	 *
	 * @param WC_Admin_Settings $settings
	 *
	 * @return array
	 *
	 * @since 1.8.0
	 * @since 1.8.5
	 */
	public function add_awooc_admin_settings( $settings ) {

		$settings[] = include __DIR__ . '/admin/class-awooc-admin-settings.php';

		return $settings;
	}


	/**
	 * Plugin action links.
	 *
	 * Add links to the plugins.php page below the plugin name
	 * and besides the 'activate', 'edit', 'delete' action links.
	 *
	 * @since 1.8.0
	 *
	 * @param    array  $links List of existing links.
	 * @param    string $file  Name of the current plugin being looped.
	 *
	 * @return    array            List of modified links.
	 */
	public function add_plugin_action_links( $links, $file ) {

		if ( plugin_basename( __FILE__ ) === $file ) {
			$links = array_merge(
				array(
					'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=awooc_settings' ) ) . '">Настройки</a>',
				),
				$links
			);
		}

		return $links;

	}


	/**
	 * Display PHP 5.6 required notice.
	 *
	 * Display a notice when the required PHP version is not met.
	 *
	 * @since 1.8.0
	 */
	public function php_version_notice() {

		$message = sprintf(
			/* translators: 1: Name plugins, 2:PHP version */
			esc_html__(
				'%1$s requires PHP version 5.6 or higher. Your current PHP version is %2$s. Please upgrade PHP version to run this plugin.',
				'art-woocommerce-order-one-click'
			),
			esc_html( AWOOC_PLUGIN_NAME ),
			PHP_VERSION
		);

		$this->admin_notice( $message, 'notice notice-error is-dismissible' );

	}


	public function check_php_version() {

		if ( version_compare( PHP_VERSION, '5.6', 'lt' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			//$this->php_version_notice();
			add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
			add_action( 'admin_notices', array( $this, 'show_deactivate_notice' ) );
		}

	}

	/**
	 * Check plugin requirements. If not met, show message and deactivate plugin.
	 *
	 * @since 1.9.0
	 */
	public function check_requirements() {

		if ( false === $this->requirements() ) {
			add_action( 'admin_notices', array( $this, 'show_plugin_not_found_notice' ) );
			if ( is_plugin_active( plugin_basename( AWOOC_PLUGIN_URI ) ) ) {
				deactivate_plugins( plugin_basename( AWOOC_PLUGIN_URI ) );
				// @codingStandardsIgnoreStart
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
				// @codingStandardsIgnoreEnd
				add_action( 'admin_notices', array( $this, 'show_deactivate_notice' ) );
			}
		}
	}


	private function requirements() {

		$all_active = true;
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		foreach ( $this->required_plugins as $key => $required_plugin ) {
			if ( is_plugin_active( $required_plugin['plugin'] ) ) {
				$this->required_plugins[ $key ]['active'] = true;
			} else {
				$all_active = false;
			}
		}

		return $all_active;
	}


	public function show_plugin_not_found_notice() {

		$message = sprintf(
			/* translators: 1: Name author plugin */
			__( 'The %s requires installed and activated plugins: ', 'art-woocommerce-order-one-click' ),
			esc_attr( AWOOC_PLUGIN_NAME )
		);

		$message_parts = array();

		foreach ( $this->required_plugins as $key => $required_plugin ) {
			if ( ! $required_plugin['active'] ) {
				$href = '/wp-admin/plugin-install.php?tab=plugin-information&plugin=';

				$href .= $required_plugin['slug'] . '&TB_iframe=true&width=640&height=500';

				$message_parts[] = '<strong><em><a href="' . $href . '" class="thickbox">' . $required_plugin['name'] . ' version ' . $required_plugin['version'] . '</a> or higher</em></strong>';
			}
		}

		$count = count( $message_parts );
		foreach ( $message_parts as $key => $message_part ) {
			if ( 0 !== $key ) {
				if ( ( ( $count - 1 ) === $key ) ) {
					$message .= ' and ';
				} else {
					$message .= ', ';
				}
			}

			$message .= $message_part;
		}

		$message .= '.';

		$this->admin_notice( $message, 'notice notice-error is-dismissible' );
	}


	/**
	 * Show admin notice.
	 *
	 * @param string $message Message to show.
	 * @param string $class   Message class: notice notice-success notice-error notice-warning notice-info is-dismissible
	 *
	 * @since 1.9.0
	 */
	private function admin_notice( $message, $class ) {

		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<p>
				<span>
				<?php echo wp_kses_post( $message ); ?>
				</span>
			</p>
		</div>
		<?php

	}


	/**
	 * Show a notice to inform the user that the plugin has been deactivated.
	 *
	 * @since 1.9.0
	 */
	public function show_deactivate_notice() {

		$message = sprintf(
			/* translators: 1: Name author plugin */
			__( '%s plugin has been deactivated.', 'art-woocommerce-order-one-click' ),
			esc_attr( AWOOC_PLUGIN_NAME )
		);

		$this->admin_notice( $message, 'notice notice-info is-dismissible' );
	}


	/**
	 * Deleting settings when uninstalling the plugin
	 *
	 * @since 1.8.0
	 */
	public function uninstall() {

		delete_option( 'woocommerce_awooc_padding' );
		delete_option( 'woocommerce_awooc_margin' );
		delete_option( 'woocommerce_awooc_mode_catalog' );
		delete_option( 'woocommerce_awooc_select_form' );
		delete_option( 'woocommerce_awooc_title_button' );
		delete_option( 'woocommerce_awooc_select_item' );
		delete_option( 'woocommerce_awooc_created_order' );
	}
}
