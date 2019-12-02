<?php
/*
This is our main DC_Metaboxes class
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

class DC_Metaboxes extends DC_Main {
	
	//Main construct for list of action and filter hooks for Metaboxes
	protected function __construct(){
		
		//Applies to post type objects
		add_action('add_meta_boxes', array($this, 'dc_post_type_metaboxes'));
		
		/*
		Iterate through each of the hierarchical taxonomies
		*/
		add_action('admin_init', function(){
			$taxonomies = $this->get_hierarchical_taxonomies();
			if(!empty($taxonomies)){
				foreach($taxonomies as $tax_obj){
					add_action($tax_obj->name . '_term_edit_form_top', array($this, 'dc_taxonomy_metaboxes_clbck'), 10, 1);
				}
			}
		});
		
	}
	
	//Action method for initializing metabox on every post type
	final public function dc_post_type_metaboxes(){
		
		//Get the post types
		$post_types = $this->array_exclude_unset($this->excluded_post_types, $this->get_post_types());
		//add_meta_box();
		
		//Begin looping through each post types
		foreach($post_types as $post_type){
			add_meta_box(
				'dc_' . $post_type . '_settings',
				'Divi Child ' . ucfirst($post_type) . ' Settings',
				array($this, 'dc_post_type_metabox_clbck'),
				$post_type,
				'side',
				'high'
			);
		}
		
	}
	
	//Callback method for Divi Child post type metabox
	final public function dc_post_type_metabox_clbck($post){
		
		//Get the predefined metabox object
		$object_meta = $this->get_predefined_metabox_object($post);
		
		//Add an nonce field so we can check for it later.
		wp_nonce_field('dc_' . $object_meta->type . '_action_nounce', 'dc_fields_' . $object_meta->type . '_name');
		
		//Begin output buffering
		ob_start();
		
		//Include the Metaboxes settings template
		include_once DC_ABSPATH . '/admin/dc-settings-metabox.php';
		
		echo ob_get_clean();
		
	}
	
	//Method for adding 
	final public function dc_taxonomy_metaboxes_clbck($tax_object){
		
		//Disable in edit tags screen
		if(get_current_screen()->base == 'edit-tags'){
			return;
		}
		
		//This function enqueues all scripts, styles, settings, and templates necessary to use the Media Modal API
		wp_enqueue_media();
		
		//Get the predefined metabox object
		$object_meta = $this->get_predefined_metabox_object($tax_object);
		
		//Add an nonce field so we can check for it later.
		wp_nonce_field('dc_' . $object_meta->type . '_action_nounce', 'dc_fields_' . $object_meta->type . '_name');
		
		//Begin output buffering
		ob_start();
		echo '<hr>';
		echo '<h3>Advanced Settings</h3>';
		echo '<div id="dc_' . $object_meta->type . '_settings" class="taxonomy_settings_main_wrapper">';
		
			//Include the Metaboxes settings template
			include_once DC_ABSPATH . '/admin/dc-settings-metabox.php';
		
		echo '</div>';
		echo '<hr>';
		
		echo ob_get_clean();
		
	}
	
}
new DC_Metaboxes;