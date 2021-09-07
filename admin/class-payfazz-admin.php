<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/dijitaq/
 * @since      1.0.0
 *
 * @package    Payfazz
 * @subpackage Payfazz/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Payfazz
 * @subpackage Payfazz/admin
 * @author     Firdaus Riyanto <firdausriyanto@gmail.com>
 */
class Payfazz_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Payfazz_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Payfazz_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/payfazz-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Payfazz_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Payfazz_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/payfazz-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register meta boxes
	 *
	 * @since    1.0.0
	 */
	public function register_meta_boxes() {
		
		global $post;

		$values = get_post_custom( $post->ID );
		$ecommerce_link = isset( $values['_payfazz_ecommerce_link'][0] ) ? $values['_payfazz_ecommerce_link'][0] : '';
		$selected = isset( $values['_payfazz_size'][0] ) ? esc_attr( $values['_payfazz_size'][0] ) : '';

		wp_nonce_field( '_payfazz_meta_box_nonce', 'meta_box_nonce' );

		$config_metabox = array (
			'type'          => 'metabox',
      'id'						=> 'payfazz',
      'post_types'    => array( 'payfazz' ),
      'context'       => 'normal',
      'priority'      => 'low',
      'title'         => 'Payfazz Metabox',
      'capability'    => 'edit_posts',
      'tabbed'        => false,
      'multilang'     => false,
      'options'       => 'simple', 
			'submenu'				=> false
		);

		$fields[] = array(
			'fields' => array(
        array(
	        'id'        => '_payfazz_size',
	        'type'      => 'select',
	        'title'     => 'Size',
	        'options'   => array(
            'small'   => 'Small',
            'medium'  => 'Medium',
            'large'  	=> 'Large',
	        ),
	        'default_option' => 'Select size',
        ),

				array(
          'id'          => '_payfazz_ecommerce_link',
          'type'        => 'text',
          'title'       => 'Ecommerce link',
          'before'      => null,
          'after'       => null,
          'class'       => 'text-class',
          'description' => null,
          'help'        => null,
					'default'	  => $ecommerce_link,
        ),

				array(
					'id'     => '_payfazz_gallery',
					'type'   => 'gallery',
					'title'  => 'Image gallery',
				),
			),
		);

		$options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields );
	
	}

	/*
	 * Disable Gutenberg editor
	 *
	 * @since    1.0.0
	 */
	public function payfazz_disable_gutenberg( $current_status, $post_type ) {
		if ( $post_type === 'payfazz' ) return false;
		return $current_status;
	}
}
