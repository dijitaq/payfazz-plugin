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

		//wp_nonce_field( '_payfazz_meta_box_nonce', 'meta_box_nonce' );

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
	 * Register custom fields to REST API
	 *
	 * @since    1.0.0
	 */
	public function register_payfazz_custom_fields_api() {
		register_rest_field(
			'payfazz',
			'featured_image_src',
			array(
				'get_callback' => array( $this, 'get_featured_image_src' ),
				'update_callback' => null,
				'schema' => null,
			)
		);

		register_rest_field(
			'payfazz',
			'_payfazz_size',
			array(
				'get_callback' => array( $this, 'get_payfazz_custom_field' ),
				'update_callback' => null,
				'schema' => null,
			)
		);

		register_rest_field(
			'payfazz',
			'_payfazz_ecommerce_link',
			array(
				'get_callback' => array( $this, 'get_payfazz_custom_field' ),
				'update_callback' => null,
				'schema' => null,
			)
		);

		register_rest_field(
			'payfazz',
			'payfazz_categories',
			array(
				'get_callback' => array( $this, 'get_payfazz_taxonomies' ),
				'update_callback' => null,
				'schema' => null,
			)
		);

		register_rest_field(
			'payfazz',
			'_payfazz_gallery',
			array(
				'single' => false,
				'type' => 'array',
				'get_callback' => array( $this, 'get_gallery_images_src' ),
				'update_callback' => null,
				'show_in_rest' => array(
					'schema' => array(
						'single' => false,
						'type' => 'array',
						'items' => array(
							'type' => 'string'
						)
					)
				)
			)
		);
	}

	/*
	 * Display custom fields in REST API
	 *
	 * text, email, select, etc.
	 *
	 * @since    1.0.0
	 */
	public function get_payfazz_custom_field( $object, $field_name, $request ) {
		return get_post_meta( $object['id'], $field_name, true );
	}

	/*
	 * Display Feature image in REST API
	 *
	 * @since    1.0.0
	 */
	public function get_featured_image_src( $object, $field_name, $request ) {
		$image = wp_get_attachment_image_src( $object['featured_media'], 'medium' );
		
		return $image[0];
	}

	/*
	 * Display Feature image in REST API
	 *
	 * @since    1.0.0
	 */
	public function get_payfazz_taxonomies( $object, $field_name, $request ) {
		$ids = explode( ",", $object['payfazz_categories'] );

		$array = array();

		foreach ( $object['payfazz_categories'] as $id ) {
			$taxonomy = get_term( $id );

			array_push( $array, array( 'name' => $taxonomy->name, 'slug' => $taxonomy->slug ) );
		}

		return $array;
	}

	/*
	 * Display gallery images in REST API
	 *
	 * @since    1.0.0
	 */
	public function get_gallery_images_src( $object, $field_name, $request ) {
		$attachments = get_post_meta( $object['id'], $field_name, false );
		$ids = explode( ",", $attachments[0] );

		$array = array();

		foreach ( $ids as $id ) {
			$image = wp_get_attachment_image_src( $id, 'full' );

			array_push( $array, array( 'full' => $image[0] ) );
		}

		return $array;
	}

	/*
	 * Modify columns in Payfazz list in admin area
	 *
	 * @since    1.0.0
	 */
	public function manage_payfazz_posts_columns( $columns ) {

    // Remove unnecessary columns
    unset(
      $columns['author'],
      $columns['comments']
    );

    // Rename title and add ID and Address
    $columns['thumbnail'] = '';
    $columns['_payfazz_size'] = esc_attr__( 'Size', 'plugin-name' );
    $columns['_payfazz_ecommerce_link'] = esc_attr__( 'Link', 'plugin-name' );


    /**
     * Rearrange column order
     *
     * Now define a new order. you need to look up the column
     * names in the HTML of the admin interface HTML of the table header.
     *
     *     "cb" is the "select all" checkbox.
     *     "title" is the title column.
     *     "date" is the date column.
     *     "icl_translations" comes from a plugin (eg.: WPML).
     *
     * change the order of the names to change the order of the columns.
     *
     * @link http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels
     */
    $customOrder = array('cb', 'thumbnail', 'title', '_payfazz_size', '_payfazz_ecommerce_link', 'date');

    /**
     * return a new column array to wordpress.
     * order is the exactly like you set in $customOrder.
     */
    foreach ($customOrder as $column_name)
        $rearranged[$column_name] = $columns[$column_name];

    return $rearranged;

  }

  // Populate new columns in customers list in admin area
  public function manage_posts_custom_column( $column, $post_id ) {

	  // For array, not simple options
	  // global $post;
	  // $custom = get_post_custom();
	  // $meta = maybe_unserialize( $custom[$this->plugin_name][0] );

	  // Populate column form meta
	  switch ($column) {

      case "thumbnail":
        echo '<a href="' . get_edit_post_link() . '">';
        echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
        echo '</a>';
        break;
      case "_payfazz_size":
        echo get_post_meta( $post_id, $column, true );
        break;
      case "_payfazz_ecommerce_link":
        echo get_post_meta( $post_id, $column, true );
        break;

	  }

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
