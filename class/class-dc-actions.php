<?php
/*
This is our main DC_Actions class that handles all the post and get requests.
Note: This also includes the Ajax requests
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

class DC_Actions extends DC_Main {
	
	//Define the requests we want to capture
	protected function __construct(){
		
		//For Child Theme Options request
		add_action('dc_settings_action', array($this, 'dc_settings_clbck'), 10, 1);
		
		//For Child Theme metabox overlay settings request (Ajax)
		add_action('wp_ajax_dc_meta_settings', array($this, 'dc_modal_meta_settings_save'));
		add_action('wp_ajax_dc_postmeta_checkbox_main', array($this, 'dc_postmeta_checkbox_main_save'));
		
	}
	
	//Handle requests from the child theme settings page
	final public function dc_settings_clbck($post){
		if(isset($post['dc_settings_save'])){
			update_option('elegant-child-options', $post['data']);
			echo '<div class="updated"><p>Settings Saved!</p></div>';
		}
	}
	
	//Method for saving the metadata fields from the overlay modal divi child settings shown in post type objects metabox
	final public function dc_modal_meta_settings_save(){
		
		//Define allowed HTML elements and the attributes it can use.
		$dc_allowed = array(
			'a' => array(
				'href' => array(),
				'target' => array(),
				'rel' => array(),
				'style' => array(),
				'class' => array()
			),
			'div' => array(
				'style' => array(),
				'class' => array()
			),
			'sup' => array(
				'style' => array(),
				'class' => array()
			),
			'sub' => array(
				'style' => array(),
				'class' => array()
			),
			'em' => array(
				'style' => array(),
				'class' => array()
			),
			'span' => array(
				'style' => array(),
				'class' => array()
			),
			'br' => array(
				'style' => array(),
				'class' => array()
			)
		);
		
		//Save each meta data in a loop
		$counter = 0;
		foreach( $_POST as $property => $value ) {
			if($_POST['_dc_object_type'] == 'post'){
				update_post_meta( $_POST['_dc_object_id'], $property, wp_kses( $value, $dc_allowed) );
			}
			if($_POST['_dc_object_type'] == 'taxonomy'){
				update_term_meta( $_POST['_dc_object_id'], $property, wp_kses( $value, $dc_allowed) );
			}
			$counter++;
			if( count($_POST) == $counter) {
				echo true; //Send data after loop saving
			}
		}
		
		exit;
		
	}
	
	//Method for saving checboxes in the divi child settings shown in post type objects metabox
	final public function dc_postmeta_checkbox_main_save(){
		
		//Save each meta data in a loop
		foreach( $_POST as $property => $value ) {
			if($_POST['_dc_object_type'] == 'post'){
				update_post_meta( $_POST['_dc_object_id'], $property, $value );
			}
			if($_POST['_dc_object_type'] == 'taxonomy'){
				update_term_meta( $_POST['_dc_object_id'], $property, $value );
			}
			$counter++;
			if( count($_POST) == $counter) {
				print_r(json_encode($_POST)); //Send data after loop saving
			}
		}
		
		exit;
		
	}
	
}
new DC_Actions;