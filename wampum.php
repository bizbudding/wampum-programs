<?php
/**
 * @package   Wampum
 * @author    BizBudding, INC <mike@bizbudding.com>
 * @license   GPL-2.0+
 * @link      http://bizbudding.com.com
 * @copyright 2016 BizBudding, INC
 *
 * @wordpress-plugin
 * Plugin Name:        Wampum
 * Description: 	   The core Wampum plugin
 * Plugin URI:         https://github.com/JiveDig/wampum
 * Author:             Mike Hemberger
 * Author URI:         http://bizbudding.com
 * Text Domain:        wampum
 * License:            GPL-2.0+
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:            0.0.5
 * GitHub Plugin URI:  https://github.com/JiveDig/wampum
 * GitHub Branch:	   master
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Wampum_Setup' ) ) :

/**
 * Main Wampum_Setup Class.
 *
 * @since 1.4
 */
final class Wampum_Setup {
	/** Singleton *************************************************************/

	/**
	 * @var Wampum_Setup The one true Wampum_Setup
	 * @since 1.4
	 */
	private static $instance;

	/**
	 * Wampum Account Page Object
	 *
	 * @since 1.0.0
	 *
	 * @var object | Wampum_Account_Page
	 */
	public $account;

	/**
	 * Wampum Content Types Object
	 *
	 * @since 1.0.0
	 *
	 * @var object | Wampum_Content_Types
	 */
	public $content_types;

	/**
	 * Wampum Connections Object
	 *
	 * @since 1.0.0
	 *
	 * @var object | Wampum_Connections
	 */
	public $connections;

	/**
	 * Wampum Membership Object
	 *
	 * @since 1.0.0
	 *
	 * @var object | Wampum_Membership
	 */
	public $membership;

	/**
	 * Wampum Template Loader Object
	 *
	 * @since 1.0.0
	 *
	 * @var object | Wampum_Template_Loader
	 */
	public $templates;

	/**
	 * Wampum Widgets Object
	 *
	 * @since 1.0.0
	 *
	 * @var object | Wampum_Widgets
	 */
	public $widgets;

	/**
	 * Main Wampum_Setup Instance.
	 *
	 * Insures that only one instance of Wampum_Setup exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   1.0.0
	 * @static  var array $instance
	 * @uses    Wampum_Setup::setup_constants() Setup the constants needed.
	 * @uses    Wampum_Setup::includes() Include the required files.
	 * @uses    Wampum_Setup::load_textdomain() load the language files.
	 * @see     Wampum()
	 * @return  object | Wampum_Setup The one true Wampum_Setup
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Wampum_Setup;
			// Methods
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->setup();
			// Instantiate Classes
			self::$instance->account		= Wampum_Account_Page::instance();
			self::$instance->content_types	= Wampum_Content_Types::instance();
			self::$instance->connections	= Wampum_Connections::instance();
			self::$instance->membership		= Wampum_Membership::instance();
			self::$instance->settings 		= Wampum_Settings::instance();
			self::$instance->step_progress 	= Wampum_User_Step_Progress::instance();
			self::$instance->templates		= Wampum_Template_Loader::instance();
			self::$instance->widgets		= Wampum_Widgets::instance();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wampum' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wampum' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'WAMPUM_VERSION' ) ) {
			define( 'WAMPUM_VERSION', '0.0.5' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'WAMPUM_PLUGIN_DIR' ) ) {
			define( 'WAMPUM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Includes Path
		if ( ! defined( 'WAMPUM_INCLUDES_DIR' ) ) {
			define( 'WAMPUM_INCLUDES_DIR', WAMPUM_PLUGIN_DIR . 'includes/' );
		}

		// Plugin Folder URL.
		if ( ! defined( 'WAMPUM_PLUGIN_URL' ) ) {
			define( 'WAMPUM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'WAMPUM_PLUGIN_FILE' ) ) {
			define( 'WAMPUM_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name
		if ( ! defined( 'WAMPUM_BASENAME' ) ) {
			define( 'WAMPUM_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}

	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.4
	 * @return void
	 */
	private function includes() {
		// Vendor
		require_once WAMPUM_INCLUDES_DIR . 'lib/class-tgm-plugin-activation.php';
		require_once WAMPUM_INCLUDES_DIR . 'lib/class-gamajo-template-loader.php';
		require_once WAMPUM_INCLUDES_DIR . 'lib/class-jivedig-content-swap.php';
		require_once WAMPUM_INCLUDES_DIR . 'lib/class-p2p-restful-connection.php';
		require_once WAMPUM_INCLUDES_DIR . 'lib/extended-cpts.php';
		require_once WAMPUM_INCLUDES_DIR . 'lib/extended-taxos.php';
		// Classes
		require_once WAMPUM_INCLUDES_DIR . 'class-wampum-account-page.php';
		require_once WAMPUM_INCLUDES_DIR . 'class-wampum-content-types.php';
		require_once WAMPUM_INCLUDES_DIR . 'class-wampum-connections.php';
		require_once WAMPUM_INCLUDES_DIR . 'class-wampum-membership.php';
		require_once WAMPUM_INCLUDES_DIR . 'class-wampum-settings.php';
		require_once WAMPUM_INCLUDES_DIR . 'class-wampum-user-step-progress.php';
		require_once WAMPUM_INCLUDES_DIR . 'class-wampum-template-loader.php';
		require_once WAMPUM_INCLUDES_DIR . 'class-wampum-widgets.php';
		// Widgets
		require_once WAMPUM_INCLUDES_DIR . 'widgets/class-wampum-widget-program-steps.php';
		// Functions
		require_once WAMPUM_INCLUDES_DIR . 'functions-display.php';
		require_once WAMPUM_INCLUDES_DIR . 'functions-helpers.php';
	}

	public function setup() {
		add_action( 'tgmpa_register', array( $this, 'dependencies' ) );
		// Bail if Posts to Posts or Piklist are not active
		if ( ! ( function_exists( 'p2p_register_connection_type' ) || class_exists('Piklist') ) ) {
			add_action( 'admin_init', array( $this, 'deactivate' ) );
			return;
		}
		// Genesis & WooCommerce Connect
		add_theme_support( 'genesis-connect-woocommerce' );
		// If front end
		if ( ! is_admin() ) {
			// Register stylesheet
			add_action( 'wp_enqueue_scripts', array( $this, 'register_stylesheets' ) );
			// Setup front end hooks
			add_filter( 'the_content', array( $this, 'before_content' ) );
			add_filter( 'the_content', array( $this, 'after_content' ) );
		}
	}

	/**
	 * Dependent plugin check
	 * @link http://tgmpluginactivation.com/
	 *
	 * @since 1.0.0
	 *
	 * @return  mixed  admin notice if dependent plugins aren't active
	 */
	public function dependencies() {
		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(
	 		// Dependent plugins from the WordPress Plugin Repository.
	 		array(
				'name'				=> 'Piklist',
				'slug'				=> 'piklist',
				'required'			=> true,
				'version'			=> '0.9.9.7',
				'force_activation'	=> true,
			),
	 		array(
				'name'				=> 'Posts to Posts',
				'slug'				=> 'posts-to-posts',
				'required'			=> true,
				'version'			=> '1.6.5',
				'force_activation'	=> true,
			),
		);

		// TGM configuration array
	 	$config = array(
	 		'id'           => 'wampum',                 // Unique ID for hashing notices for multiple instances of TGMPA.
	 		'default_path' => '',                       // Default absolute path to bundled plugins.
	 		'menu'         => 'wampum-install-plugins', // Menu slug.
	 		'parent_slug'  => 'themes.php',             // Parent menu slug.
	 		'capability'   => 'edit_theme_options',     // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
	 		'has_notices'  => true,                     // Show admin notices or not.
	 		'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
	 		'dismiss_msg'  => '',                       // If 'dismissable' is false, this message will be output at top of nag.
	 		'is_automatic' => false,                    // Automatically activate plugins after installation or not.
	 		'message'      => '',                       // Message to output right before the plugins table.
	 	);

	 	tgmpa( $plugins, $config );
	}

	/**
	 * Deactivates the plugin if Genesis isn't running
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		deactivate_plugins( WAMPUM_BASENAME );
		add_action( 'admin_notices', array( $this, 'error_message' ) );
	}

	/**
	 * Error message if we're not using the Genesis Framework.
	 *
	 * @since 1.0.0
	 */
	public function error_message() {

		$error = sprintf( __( 'Wampum dependent plugins are not installed. Wampum has been deactivated.', 'wampum' ) );

		echo '<div class="error"><p>' . esc_attr( $error ) . '</p></div>';

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

	}

	/**
	 * Register stylesheets for later use
	 * Use via wp_enqueue_style('wampum'); in a template
	 *
	 * @since  1.0.0
	 *
	 * @return null
	 */
	public function register_stylesheets() {
	    wp_register_style( 'wampum', WAMPUM_PLUGIN_URL . 'css/wampum.css' );
	}

	/**
	 * Before Download Content
	 *
	 * Adds an action to the beginning of download post content that can be hooked to
	 * by other functions.
	 *
	 * @since 1.0.8
	 * @global $post
	 *
	 * @param $content The the_content field of the download object
	 * @return string the content with any additional data attached
	 */
	function before_content( $content ) {
		ob_start();
		do_action( 'wampum_before_content' );
		$content = ob_get_clean() . $content;
		return $content;
	}

	/**
	 * After Download Content
	 *
	 * Adds an action to the end of download post content that can be hooked to by
	 * other functions.
	 *
	 * @since 1.0.8
	 * @global $post
	 *
	 * @param $content The the_content field of the download object
	 * @return string the content with any additional data attached
	 */
	function after_content( $content ) {
		ob_start();
		do_action( 'wampum_after_content' );
		$content .= ob_get_clean();
		return $content;
	}

}
endif; // End if class_exists check.

/**
 * The main function for that returns Wampum_Setup
 *
 * The main function responsible for returning the one true Wampum_Setup
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wampum = Wampum(); ?>
 *
 * @since 1.0.0
 *
 * @return object|Wampum_Setup The one true Wampum_Setup Instance.
 */
function Wampum() {
	return Wampum_Setup::instance();
}

// Get Wampum Running.
Wampum();
