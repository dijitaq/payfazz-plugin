<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/dijitaq/
 * @since      1.0.0
 *
 * @package    Payfazz
 * @subpackage Payfazz/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Payfazz
 * @subpackage Payfazz/includes
 * @author     Firdaus Riyanto <firdausriyanto@gmail.com>
 */
class Payfazz {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Payfazz_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Store plugin admin class to allow public access.
	 *
	 * @since    1.0.0
	 * @var object      The admin class.
	 */
	public $admin;

	/**
	 * Store plugin public class to allow public access.
	 *
	 * @since    1.0.0
	 * @var object      The admin class.
	 */
	public $public;

	/**
	 * Store plugin main class to allow public access.
	 *
	 * @since    20180622
	 * @var object      The main class.
	 */
	public $main;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PAYFAZZ_VERSION' ) ) {
			$this->version = PAYFAZZ_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'payfazz';

		$this->main = $this;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Payfazz_Loader. Orchestrates the hooks of the plugin.
	 * - Payfazz_i18n. Defines internationalization functionality.
	 * - Payfazz_Admin. Defines all hooks for the admin area.
	 * - Payfazz_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-payfazz-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-payfazz-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-payfazz-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-payfazz-public.php';

		/**
     * Custom Post Types
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-payfazz-post_types.php';

		/*
		 * Exopite simple options framework class
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/exopite-simple-options/exopite-simple-options-framework-class.php';

		$this->loader = new Payfazz_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Payfazz_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Payfazz_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$this->admin = new Payfazz_Admin( $this->get_plugin_name(), $this->get_version(), $this->main );

		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );

		// Register post types
		$plugin_post_types = new Payfazz_Post_Types();
    $this->loader->add_action( 'init', $plugin_post_types, 'create_custom_post_type', 999 );

    // Add meta boxes
    $this->loader->add_action( 'admin_menu', $this->admin, 'register_meta_boxes', 0 );

    // Register custom fields to REST API
    $this->loader->add_action( 'rest_api_init', $this->admin, 'register_payfazz_custom_fields_api', 0 );

    // Register get post by terms end point to REST API
    $this->loader->add_action( 'rest_api_init', $this->admin, 'register_payfazz_post_by_terms_end_point');

    // Modify columns in Payfazz list in admin area
    $this->loader->add_filter( 'manage_payfazz_posts_columns', $this->admin, 'manage_payfazz_posts_columns' );
		$this->loader->add_action( 'manage_posts_custom_column', $this->admin, 'manage_posts_custom_column', 10, 2 );

    // Disable Gutenberg editor for Payfazz
		$this->loader->add_filter( 'use_block_editor_for_post_type', $this->admin, 'payfazz_disable_gutenberg', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->public = new Payfazz_Public( $this->get_plugin_name(), $this->get_version(), $this->main );

		$this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Payfazz_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
