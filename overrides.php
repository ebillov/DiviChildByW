<?php
/*
Codes below are intended for inline codes and functions that are not necessarily needed to be placed in a class
Note: Only place codes in here if you intend the "Procedural" way of coding.
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

//Format the pages to be used later for the select field
$privacy_policy_data_array = array();
if(!empty(DC()->get_pages())){
	foreach(DC()->get_pages() as $item){
		$privacy_policy_data_array[] = array(
			'option' => $item->ID,
			'value' => $item->post_title,
		);
	}
}

/*
These associative arrays are what the child theme field settings renders.
Note 1: Add anything you want in here to add additional custom fields following the formatting as shown below
Note 2: The "data_array" key is only used for the "select" field type
Usage:
$dc_options = array(
	'key_name' => array(
		'type' => 'select',
		'data_array' => array(
			array(
				'option' => 'option_key',
				'value' => 'This is the value'
			)
		),
		'label' => 'My Custom Option',
		'description' => 'This is the description of my custom option.'
		'has_shortcode' => true //Bool optional (default: false)
	)
);
*/
global $dc_options;
$dc_options = array(
	'show-tagline' => array(
		'type' => 'checkbox',
		'label' => 'Show Header Tagline',
		'description' => 'When enabled, this shows the tagline shown below the Logo in the header area at the top. Configure the text shown by going over to <b>Settings -> General</b> page.'
	),
	'show-archive-readmore' => array(
		'type' => 'checkbox',
		'label' => 'Show Read More On Archive Pages',
		'description' => 'When enabled, this shows the <b>Read More</b> button in archive pages.'
	),
	'header-text' => array(
		'type' => 'textarea',
		'label' => 'Header Text',
		'description' => 'This content will be placed above the main navigation menu of the header area at the top.',
		'has_shortcode' => true
	),
	'copyright' => array(
		'type' => 'textarea',
		'label' => 'Copyright',
		'description' => 'This is shown in the footer area of the website. <b>Format:</b> <span class="select_all_elem">&lt;a href="/"&gt;Business Name, City, State&lt;/a&gt;</span>',
		'has_shortcode' => true
	),
	'privacy-policy' => array(
		'type' => 'select',
		'data_array' => $privacy_policy_data_array,
		'label' => 'Privacy Policy',
		'description' => 'Select a page and choose it as Privacy Policy page.'
	),
);

//Replaces the class for Contact Form 7 submit buttons
ob_start();
add_action('shutdown', function() {
	$final = '';
	
	// We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
	// that buffer's output into the final output.
	$levels = ob_get_level();
	
	for ($i = 0; $i < $levels; $i++) {
		$final .= ob_get_clean();
	}
	
	//Get CoverPic metadata
	$coverpic = DC()->get_predefined_metabox_object( get_queried_object() );
	if(!empty($coverpic)){
		
		//Get the metadata values
		$dc_coverpic_settings = $coverpic->metadata[$coverpic->dc_coverpic_settings][0];
		$dc_hide_title = $coverpic->metadata[$coverpic->dc_hide_title_name][0];
		
		//Begin quick check
		if($dc_coverpic_settings == 'on'){
			//For post type objects
			if($coverpic->object_type == 'post' && $dc_hide_title != 'on'){
				//For post title
				$final = preg_replace("'<h1 class=\"entry-title\">(.*?)</h1>'si", '<span class="entry-title" style="display: none;">' . get_post($coverpic->id)->post_title . '</span>', $final);
				//For postmeta
				$final = str_replace('class="post-meta"', 'class="post-meta" style="display: none;"', $final);
			}
		}
		
	}
	
	$final = str_replace('class="wpcf7-form-control wpcf7-submit"', 'class="et_pb_button"', $final);
	// Apply any filters to the final output
	echo $final;
	
}, 0);