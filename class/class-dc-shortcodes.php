<?php
/*
This is our main DC_Shortcodes class
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

class DC_Shortcodes extends DC_main {
	
	protected function __construct(){
		//List of shortcodes
		add_shortcode('dc_content', array($this, 'dc_default_shortcodes'));
	}
	
	//Define default shortcodes for child theme settings
	public function dc_default_shortcodes($atts){
		
		//Define default shortcode attributes
		$atts = shortcode_atts(
			array(
				'name' => ''
			),
		$atts, 'dc_content');
		
		//Simple check if shortcode attribute is empty
		if(empty($atts['name'])){
			return 'Please provide the "name" attribute to the shortcode. Refer to the Divi Child Theme Options page.';
		}
		
		//Get the option settings
		$dc_settings = get_option('elegant-child-options');
		
		//Get value based on key name
		return do_shortcode($this->get_key_value($atts['name'], $dc_settings));
		
	}
	
}
new DC_Shortcodes;