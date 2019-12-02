<?php
/*
This is our main DC_Main class
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

//Our main class that can be extended
class DC_Main {
	
	/*
	Define excluded post types
	Note 1: Add any post type you wish to exclude here
	Note 2: This is used for post type metaboxes
	*/
	protected $excluded_post_types = array(
		'attachment'
	);
	
	//A single instance of the class DC_Main
	protected static $dc_instance = null;
	
	/*
	DC_Main Instance ensuring that only 1 instance of the class is loaded
	*/
	final public static function instance($version){
		if(is_null(self::$dc_instance)){
			self::$dc_instance = new self($version);
		}
		return self::$dc_instance;
	}
	
	/*
	Cloning is forbidden.
	*/
	public function __clone() {
		$error = new WP_Error('forbidden', 'Cloning is forbidden.');
		return $error->get_error_message();
	}
	
	/*
	Unserializing instances of this class is forbidden.
	*/
	public function __wakeup() {
		$error = new WP_Error('forbidden', 'Unserializing instances of this class is forbidden.');
		return $error->get_error_message();
	}
	
	/*
	Main construct
	*/
	protected function __construct($version){
		$this->init_dc_options();
		$this->version = $version;
		$this->dc_constants();
		$this->dc_includes();
	}
	
	//Define object properties for later use on child classes or for the override.php file
	final protected function init_dc_options(){
		//List of defined properties
		$this->dc_options = get_option('elegant-child-options');
	}
	
	/*
	Define constants used within the class
	*/
	final protected function dc_constants(){
		$this->define('DC_ABSURL', get_stylesheet_directory_uri());
		$this->define('DC_ABSPATH', get_stylesheet_directory());
		$this->define('DC_VERSION', $this->version);
	}
	
	/*
	Define constant if not already set.
	*/
	final protected function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
	
	/*
	Include the files to be used
	*/
	final protected function dc_includes(){
		
		//Load Wordpress core get_plugins function if it was not loaded
		//This is used to check is_plugin_active() Wordpress method
		if(!function_exists('get_plugins')){
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		/*
		Include the Actions Class
		Note: This handles post or get requests to the server
		*/
		include_once DC_ABSPATH . '/class/class-dc-actions.php';
		
		//Include Core theme functions
		include_once DC_ABSPATH . '/includes/theme-functions.php';
		
		//Include some inline functions and other misc codes in a "Procedural" way of coding.
		include_once DC_ABSPATH . '/overrides.php';
		
		//Include the hooks class
		include_once DC_ABSPATH . '/class/class-dc-hooks.php';
		
		//Include the Admin Menus class
		include_once DC_ABSPATH . '/class/class-dc-admin-menus.php';
		
		//Include the Shortcodes class
		include_once DC_ABSPATH . '/class/class-dc-shortcodes.php';
		
		//Include the Metaboxes class
		include_once DC_ABSPATH . '/class/class-dc-metaboxes.php';
		
	}
	
	/*
	Begin defining main methods
	*/
	
	//Gets all the published pages
	final public function get_pages(){
		return get_posts(
			array(
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'post_type' => 'page'
			)
		);
	}
	
	/*
	Gets the value of an associative array by an index key
	Note: Stripslashes are enabled by default
	*/
	final public function get_key_value($key, $array_data = array(), $stripslashes = true){
		if(empty($array_data) || empty($key)){
			return;
		}
		if($stripslashes){
			return stripcslashes($array_data[$key]);
		}
		return $array_data[$key];
	}
	
	//Get all the public post types registered in Wordpress
	final public function get_post_types($args = array( 'public' => true )){
		return get_post_types($args);
	}
	
	//Method to unset an item from an array based from the exclude array
	final public function array_exclude_unset($exclude_array = array(), $array_data = array()){
		//Perform quick checks
		if(
			!is_array($exclude_array) ||
			!is_array($array_data) ||
			empty($array_data)
		){
			return null;
		}
		//Begin iterate
		foreach($array_data as $key => $item){
			if(in_array($item, $exclude_array)){
				unset($array_data[$key]);
			}
		}
		return $array_data;
	}
	
	/*
	Define method to get predefined metabox object values
	Note: Accepts the WP_Post or WP_Term object
	*/
	final public function get_predefined_metabox_object($object){
		
		//Quick check
		if(!is_object($object)){
			return null;
		}
		
		//Define new object
		$new_obj = new stdClass();
		
		//Get object class name
		if(is_a($object, 'WP_Post')){
			$new_obj->id = $object->ID;
			$new_obj->name = get_post_type_object($object->post_type)->labels->singular_name;
			$new_obj->type = $object->post_type;
			$new_obj->object_type = 'post';
			$new_obj->metadata = get_post_meta($object->ID);
		} elseif(is_a($object, 'WP_Term')){
			$new_obj->id = $object->term_id;
			$new_obj->name = get_taxonomy($object->taxonomy)->labels->singular_name;
			$new_obj->type = $object->taxonomy;
			$new_obj->object_type = 'taxonomy';
			$new_obj->metadata = get_term_meta($object->term_id);
		} else {
			return null;
		}
		
		//Define input field name variables
		$new_obj->dc_coverpic_settings = '_dc_' . $new_obj->type . '_coverpic_settings';
		$new_obj->dc_breadcrumbs = '_dc_' . $new_obj->type . '_breadcrumbs';
		$new_obj->dc_background_img = '_dc_' . $new_obj->type . '_background_img';
		$new_obj->dc_background_img_alt_tag = '_dc_' . $new_obj->type . '_background_img_alt_tag';
		$new_obj->dc_background_color = '_dc_' . $new_obj->type . '_background_color';
		$new_obj->dc_background_opacity = '_dc_' . $new_obj->type . '_background_opacity';
		$new_obj->dc_background_height = '_dc_' . $new_obj->type . '_background_height';
		$new_obj->dc_default_settings = '_dc_' . $new_obj->type . '_default_settings';
		
		//For Main Title
		$new_obj->dc_hide_title_name = '_dc_' . $new_obj->type . '_hide_title_name';
		$new_obj->dc_title_name = '_dc_' . $new_obj->type . '_title_name';
		$new_obj->dc_title_pos_name = '_dc_' . $new_obj->type . '_title_pos_name';
		$new_obj->dc_title_font_weight_name = '_dc_' . $new_obj->type . '_title_font_weight_name';
		$new_obj->dc_title_font_size_name = '_dc_' . $new_obj->type . '_title_font_size_name';
		$new_obj->dc_title_font_color_name = '_dc_' . $new_obj->type . '_title_font_color_name';
		
		//For Sub-Title
		$new_obj->dc_hide_sub_title_name = '_dc_' . $new_obj->type . '_hide_sub_title_name';
		$new_obj->dc_sub_title_name = '_dc_' . $new_obj->type . '_sub_title_name';
		$new_obj->dc_sub_title_font_weight_name = '_dc_' . $new_obj->type . '_sub_title_font_weight_name';
		$new_obj->dc_sub_title_font_size_name = '_dc_' . $new_obj->type . '_sub_title_font_size_name';
		$new_obj->dc_sub_title_font_color_name = '_dc_' . $new_obj->type . '_sub_title_font_color_name';
		
		return $new_obj;
		
	}
	
	/*
	Method for Hex to RGB/RGBA color convertion
	Note: First parameter is a hex color code. The second parameter is the opacity level.
	*/
	function hexToRgb($hex, $alpha = false) {
		$hex      = str_replace('#', '', $hex);
		$length   = strlen($hex);
		$rgba['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
		$rgba['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
		$rgba['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
		if ( $alpha ) {
			$rgba['a'] = ( ((intval($alpha)/100) == 1) ? '' : (intval($alpha)/100) );
			return 'rgba(' . $rgba['r'] . ', ' . $rgba['g'] . ', ' . $rgba['b'] . ', ' . $rgba['a'] . ')';
		} else {
			return 'rgb(' . $rgba['r'] . ', ' . $rgba['g'] . ', ' . $rgba['b'] . ')';
		}
	}
	
	/*
	Get all registered taxonomies
	Note: Returns an array of taxonomy object
	*/
	final public function get_hierarchical_taxonomies($args = array(), $output = 'objects'){
		
		//Define defaults
		$defaults = array(
			'public' => true
		);
		
		//Get all the taxonomies
		$tax_array = get_taxonomies(array_merge($defaults, $args), $output);
		
		//Quick check
		if(empty($tax_array)){
			return null;
		}
		
		//Begin iterate
		$new_tax_array = array();
		foreach($tax_array as $tax_obj){
			if($tax_obj->hierarchical === true){
				$new_tax_array[] = $tax_obj;
			}
		}
		
		return $new_tax_array;
		
	}
	
}