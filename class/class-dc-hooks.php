<?php
/*
This is our main DC_Hooks class
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

class DC_Hooks extends DC_Main {
	
	//Main construct for list of action and filter hooks
	protected function __construct(){
		
		//Load divi child option settings from parent class
		parent::init_dc_options();
		
		/*
		Wordpress Core Actions and Filters
		*/
		//List of action hooks
		add_action('wp_enqueue_scripts', array($this, 'dc_stylesheets'));
		add_action('wp_head', array($this, 'dc_script_overrides'));
		add_action('admin_enqueue_scripts', array($this, 'dc_admin_metabox_global_js_script'), 1);
		add_action('admin_enqueue_scripts', array($this, 'dc_admin_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'dc_init_codemirror'));
		add_action('after_setup_theme', array($this, 'dc_custom_core_options'));
		add_action('admin_head', array($this, 'dc_menu_side_panel_style'), 1);
		add_action('init', array($this, 'dc_term_filters'));

		/**
		 * Script for phone number hyperlink
		 */
		add_action('wp_footer', function(){
			?>
			<script>
			jQuery(document).ready(function(){
				var phone_number = jQuery('#et-info-phone').text();
				jQuery('#et-info-phone').wrap('<a href="tel:' + phone_number + '"></a>');
			});
			</script>
			<?php
		});
		
		//List of filter hooks
		add_filter('upload_mimes', array($this, 'dc_upload_mime_types'), 10, 1);
		
		/*
		Template Actions and Filters
		*/
		//List of action hooks
		add_action('et_header_top', array($this, 'dc_header_text_action'));
		add_action('et_before_main_content', array($this, 'dc_coverpic_render'), 10);
		add_action('et_before_main_content', array($this, 'dc_breadcrumbs_render'), 11);
		add_action('loop_start', array($this, 'dc_loop_start_archive'));
		
		//List of filter hooks
		add_filter('et_html_logo_container', array($this, 'logo_container_filter'), 10, 1);
		add_filter('body_class', array($this, 'body_class_tagline'), 10, 1);
		add_filter('post_class', array($this, 'dc_post_class_archive'), 10, 1);
		
	}
	
	//Method for printing both the Parent and Child theme stylesheets
	final public function dc_stylesheets(){
		
		//For Parent Divi theme stylesheet output
		wp_enqueue_style('divi-style', get_template_directory_uri() . '/style.css', array(), wp_get_theme(get_template())->get('Version'));
		
		//For Child Divi theme stylesheet output
		wp_enqueue_style('divi-child', DC_ABSURL . '/style.css', array('divi-style'), DC_VERSION);
		
	}
	
	//Method for placing script overrides for compatibility on CoverPic and Breadcrumbs with Divi's Fixed Navigation Header menu layout styling
	final public function dc_script_overrides(){
		
		//Also get the body classes (CSS)
		$body_class = apply_filters('body_class', array());
		
		//For fixed position header styling with trasnparent header background color
		if(in_array('et_fixed_nav', $body_class) && in_array('et_transparent_nav', $body_class)){
			
			//Get predefined object data
			$object_meta = $this->get_predefined_metabox_object( get_queried_object() );
			
			//Get the metadata values
			$dc_coverpic_settings = $object_meta->metadata[$object_meta->dc_coverpic_settings][0];
			$dc_breadcrumbs = $object_meta->metadata[$object_meta->dc_breadcrumbs][0];
			$dc_background_height = $object_meta->metadata[$object_meta->dc_background_height][0];
			
			if($dc_coverpic_settings == 'on' || $dc_breadcrumbs == 'on' || $dc_breadcrumbs != 'off'):
				ob_start(); ?>
				<script>
					//Had to do this on resize event
					var dc_resize;
					jQuery(window).resize(function(){
						if(!!dc_resize){
							clearTimeout(dc_resize);
						}
						dc_resize = setTimeout(function(){
							//Remove the style attribute for the first container in the #main-content element for compatibility
							jQuery('#main-content .container:first-child').removeAttr('style');
						}, 201);
					});
					setTimeout(function(){
						
						//Remove the style attribute for the first container in the #main-content element for compatibility
						jQuery('#main-content .container:first-child').removeAttr('style');
						
						//Define variables
						var top_header_height = (jQuery('#top-header').height() != null) ? jQuery('#top-header').height() : 0,
							main_header_height = jQuery('#main-header').attr('data-height-onload'),
							total_height = parseInt(top_header_height) + parseInt(main_header_height);
						
						<?php
						//For CoverPic
						if($dc_coverpic_settings == 'on'): ?>
						jQuery('#dc_coverpic_image, #dc_coverpic_text_wrapper, #dc_coverpic_text_area_flex').each(function(index){
							if(jQuery(this).attr('id') == 'dc_coverpic_text_area_flex'){
								jQuery(this).attr('style', 'height: ' + (parseInt('<?php echo intval($dc_background_height) * 10; ?>') + total_height) + 'px; padding-top: ' + total_height + 'px;');
							} else {
								jQuery(this).attr('style', 'height: ' + (parseInt('<?php echo intval($dc_background_height) * 10; ?>') + total_height) + 'px;');
							}
						});
						<?php endif; ?>
						
						<?php
						//For breadcrumbs
						if($dc_coverpic_settings != 'on' && ($dc_breadcrumbs == 'on' || $dc_breadcrumbs != 'off')): ?>
						jQuery('.dc_breadcrumbs').attr('style', 'margin-top: ' + total_height + 'px;');
						<?php endif; ?>
						
					}, 250); //This 250ms timeout is based from Divi's custom.js (200) timeout declaration. Refer to the codes from there.
				</script>
				<?php
				echo ob_get_clean();
			endif;
		}
		
	}
	
	//Action method to render the global admin scripts
	final public function dc_admin_metabox_global_js_script(){
		
		//Get current screen
		$screen = get_current_screen();
		
		//Get the post types
		$post_types = $this->array_exclude_unset($this->excluded_post_types, $this->get_post_types());
		
		//Get hierarchical taxonomies
		$taxonomies = $this->get_hierarchical_taxonomies();
		$tax_array = array();
		foreach($taxonomies as $tax_obj){
			$tax_array[] = 'edit-' . $tax_obj->name;
		}
		?>
		<script type="text/javascript">
		
			//Define the object global scope
			var dc = {};
			dc.meta = {};
			
			//Define the site url
			dc.site_url = '<?php echo esc_url(get_option('siteurl')); ?>';
			
			//Global ajax url
			dc.ajax_url = '<?php echo esc_url(admin_url("admin-ajax.php")); ?>';
			
			<?php
			//Only do this in singe post type admin page and edit category screen
			if(in_array($screen->id, $post_types) || in_array($screen->id, $tax_array) && $screen->base != 'edit-tags'):
			
				if(in_array($screen->id, $post_types) || !in_array($screen->id, $tax_array)){ //For post type objects
					//Get global post
					global $post;
					
					//Get the predefined metabox object
					$object_meta = $this->get_predefined_metabox_object($post);
					
					//Set the title element
					echo 'dc.wp_title_element = "#poststuff #title";';
				} elseif(in_array($screen->id, $tax_array) && $screen->base != 'edit-tags') { //For taxonomy type objects
					//Get WP_Term instance
					$wp_term = WP_Term::get_instance($_GET['tag_ID']);
					
					//Get the predefined metabox object
					$object_meta = $this->get_predefined_metabox_object($wp_term);
					
					//Set the title element
					echo 'dc.wp_title_element = "#edittag input#name";';
				} else {
					return;
				}
			?>
			//Define metabox ID
			dc.metabox_id = '#<?php echo 'dc_' . $object_meta->type . '_settings'; ?>';
			
			//Global post type metabox data
			dc.coverpic_settings = '<?php echo $object_meta->dc_coverpic_settings; ?>';
			dc.breadcrumbs = '<?php echo $object_meta->dc_breadcrumbs; ?>';
			dc.background_img = '<?php echo $object_meta->dc_background_img; ?>';
			dc.background_img_alt_tag = '<?php echo $object_meta->dc_background_img_alt_tag; ?>';
			dc.background_color = '<?php echo $object_meta->dc_background_color; ?>';
			dc.background_opacity = '<?php echo $object_meta->dc_background_opacity; ?>';
			dc.background_height = '<?php echo $object_meta->dc_background_height; ?>';
			dc.title_name = '<?php echo $object_meta->dc_title_name; ?>';
			dc.title_pos_name = '<?php echo $object_meta->dc_title_pos_name; ?>';
			dc.title_font_weight_name = '<?php echo $object_meta->dc_title_font_weight_name; ?>';
			dc.title_font_size_name = '<?php echo $object_meta->dc_title_font_size_name; ?>';
			dc.title_font_color_name = '<?php echo $object_meta->dc_title_font_color_name; ?>';
			dc.sub_title_name = '<?php echo $object_meta->dc_sub_title_name; ?>';
			dc.sub_title_font_weight_name = '<?php echo $object_meta->dc_sub_title_font_weight_name; ?>';
			dc.sub_title_font_size_name = '<?php echo $object_meta->dc_sub_title_font_size_name; ?>';
			dc.sub_title_font_color_name = '<?php echo $object_meta->dc_sub_title_font_color_name; ?>';
			
			//Define class array
			dc.class_array = [
				'.<?php echo $object_meta->dc_background_img; ?>',
				'.<?php echo $object_meta->dc_background_img_alt_tag; ?>',
				'.<?php echo $object_meta->dc_background_color; ?>',
				'.<?php echo $object_meta->dc_background_opacity; ?>',
				'.<?php echo $object_meta->dc_background_height; ?>',
				'.<?php echo $object_meta->dc_hide_title_name; ?>',
				'.<?php echo $object_meta->dc_title_name; ?>',
				'.<?php echo $object_meta->dc_title_pos_name; ?>',
				'.<?php echo $object_meta->dc_title_font_weight_name; ?>',
				'.<?php echo $object_meta->dc_title_font_size_name; ?>',
				'.<?php echo $object_meta->dc_title_font_color_name; ?>',
				'.<?php echo $object_meta->dc_hide_sub_title_name; ?>',
				'.<?php echo $object_meta->dc_sub_title_name; ?>',
				'.<?php echo $object_meta->dc_sub_title_font_weight_name; ?>',
				'.<?php echo $object_meta->dc_sub_title_font_size_name; ?>',
				'.<?php echo $object_meta->dc_sub_title_font_color_name; ?>'
			];
			
			//Define post type
			dc.post_type = '<?php echo $object_meta->type; ?>';
			
			//Define post meta values
			dc.meta.default_settings = '<?php echo $object_meta->metadata[$object_meta->dc_default_settings][0]; ?>';
			<?php endif; ?>
			
			//Attach to the global window scope
			window.dc = dc;
			
		</script>
		<?php
	}
	
	//Method for printing child theme admin scripts
	final public function dc_admin_scripts(){
		
		//Applies to Divi Child Options page
		wp_enqueue_style('dc-admin', DC_ABSURL . '/admin/css/admin.css', array(), DC_VERSION);
		
		//Get current screen
		$screen = get_current_screen();
		
		//Get the post types
		$post_types = $this->array_exclude_unset($this->excluded_post_types, $this->get_post_types());
		
		//Get hierarchical taxonomies
		$taxonomies = $this->get_hierarchical_taxonomies();
		$tax_array = array();
		foreach($taxonomies as $tax_obj){
			$tax_array[] = 'edit-' . $tax_obj->name;
		}
		
		//Only load this in the post type pages and edit category screen
		//if(in_array($screen->id, $post_types) || $screen->id == 'edit-category' && $screen->base != 'edit-tags'){
		if(in_array($screen->id, $post_types) || in_array($screen->id, $tax_array) && $screen->base != 'edit-tags'){
			
			//Applies to post type metaboxes
			wp_enqueue_style('dc-post-type-metabox', DC_ABSURL . '/admin/css/dc-settings-metabox.css', array(), DC_VERSION);
			
			//HTML Entity Encoder/Decoder JS Lib
			wp_enqueue_script('dc-he-decoder-lib', DC_ABSURL . '/lib/he-master/he.js', array(), DC_VERSION);
			
			//Color picker library
			wp_enqueue_script('dc-jscolor-lib', DC_ABSURL . '/lib/jscolor.2.0.5.js', array('dc-he-decoder-lib'), DC_VERSION);
			
			//Settings metabox JS
			wp_enqueue_script('dc-settings-metabox', DC_ABSURL . '/admin/js/dc-settings-metabox.js', array('dc-jscolor-lib'), DC_VERSION);
		}
		
	}
	
	//Add custom options to Divi core settings
	final public function dc_custom_core_options() {
		if ( ! function_exists( 'et_load_core_options' ) ) {
			function et_load_core_options() {
				$options = require_once(DC_ABSPATH . '/options_dc.php');
			}
		}
	}
	
	//Render CodeMirror instances
	final public function dc_init_codemirror(){
		
		//Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
		$settings['codemirror']['theme'] = 'ambiance'; //Include the ambiance codemirror theme
		
		//Bail if user disabled CodeMirror.
		if(false === $settings){
			return;
		}
		
		//Get current screen
		$screen = get_current_screen();
		
		//Include the ambiance codemirror stylesheet
		wp_enqueue_style('dc-codemirror-ambiance', DC_ABSURL . '/admin/css/ambiance.css', array(), DC_VERSION);
		
		//Wordpress global variable
		global $pagenow;
		
		//Get the post types
		$post_types = $this->array_exclude_unset($this->excluded_post_types, $this->get_post_types());
		
		//Get hierarchical taxonomies
		$taxonomies = $this->get_hierarchical_taxonomies();
		$tax_array = array();
		foreach($taxonomies as $tax_obj){
			$tax_array[] = 'edit-' . $tax_obj->name;
		}
		
		//Only render this in the child theme option settings page
		if($screen->id == 'appearance_page_child-theme-options'){
		
			//Get the child theme html form fields
			global $dc_options;
			if(!empty($dc_options)){
				foreach($dc_options as $id => $values){
					if($values['type'] == 'textarea'){
						wp_add_inline_script(
							'code-editor',
							sprintf(
								'jQuery( function() {
									var test = wp.codeEditor.initialize( "' . $id . '", %s );
									console.log(test);
								} );',
								wp_json_encode( $settings )
							)
						);
					}
				}
			}
			
		}
		
		//This allows codemirror editor to description textarea field when editing a term
		if($pagenow == 'term.php' && in_array($screen->id, $tax_array)) {
			wp_add_inline_script(
				'code-editor',
				sprintf(
					'jQuery( function() { wp.codeEditor.initialize( "description", %s ); } );',
					wp_json_encode( $settings )
				)
			);
		}
		
		//This allows codemirror editor to description textarea field when adding a term
		if($pagenow == 'edit-tags.php' && in_array($screen->id, $tax_array)) {
			wp_add_inline_script(
				'code-editor',
				sprintf(
					'jQuery( function($) {
						instance_custom = wp.codeEditor.initialize( "tag-description", %s );
						instance_custom.codemirror.on("change", function(e){
							e.save();
						});
					} );',
					wp_json_encode( $settings )
				)
			);
		}
		
	}
	
	//Enable uploads for SVG files as well
	final public function dc_upload_mime_types($mimes){
		 $mimes['svg'] = 'image/svg+xml';
		 return $mimes;
	}
	
	//Add some inline styles to admin dashboard side menu panel
	final public function dc_menu_side_panel_style(){
		?>
		<style type="text/css">
			ul#adminmenu li.wp-not-current-submenu div.wp-menu-image.dashicons-before{ filter: grayscale(100) brightness(5); }
			ul#adminmenu li:hover div.wp-menu-image.dashicons-before,
			ul#adminmenu li.current div.wp-menu-image.dashicons-before
			{ filter: none !important; }
			ul#adminmenu li.current div.wp-menu-image.dashicons-before img{
				opacity: 1;
			}
		</style>
		<?php
	}
	
	/*
	Let's override the Term (Aka. Categories) filters
	Note:
	1. pre_term_name and term_name -> The term title.
	2. pre_term_description and term_description -> The term description.
	*/
	final public function dc_term_filters(){
		//Let's remove the filters for the Title and Description of a term
		remove_filter('pre_term_description', 'wp_filter_kses');
		remove_filter('term_description', 'wp_kses_data');
		
		//Now place some custom filters
		add_filter('pre_term_description', 'esc_html');
		add_filter('term_description', function($content){
			return apply_filters('the_content', $content);
		});
	}
	
	//Filter to add a css Class to the <body> element when a header tagline is enabled
	final public function body_class_tagline($classes){
		if(!(empty(get_option('blogdescription'))) && $this->dc_options['show-tagline'] == 'on'){
			return array_merge($classes, array('dc_has_tagline'));
		}
		return $classes;
	}
	
	//Filter method for the logo_container area in the header.php file
	final public function logo_container_filter($logo_container){
		//For header tagline
		if(!(empty(get_option('blogdescription'))) && $this->dc_options['show-tagline'] == 'on'){
			$logo_container .= '<p class="dc_tagline">' . get_option('blogdescription') . '</p>';
		}
		//For header-text content
		if(!empty($this->dc_options['header-text']) && (et_get_option('header_style') != 'split' || is_customize_preview())){
			$logo_container .= do_shortcode(stripcslashes('<div class="header_text">' . $this->dc_options['header-text'] . '</div>') . '<div class="header_text_clear"></div>');
		}
		return $logo_container;
	}
	
	//Action method for the header text placed below before the </div> closing tag of the main main menu navigation area
	final public function dc_header_text_action(){
		//For header tagline
		if(!(empty(get_option('blogdescription'))) && $this->dc_options['show-tagline'] == 'on'){
			echo '<p class="dc_tagline">' . get_option('blogdescription') . '</p>';
		}
		//For header-text content
		if(!empty($this->dc_options['header-text']) && (et_get_option('header_style') == 'split' || is_customize_preview())){
			echo do_shortcode(stripcslashes('<div class="header_text">' . $this->dc_options['header-text'] . '</div>') . '<div class="header_text_clear"></div>');
		}
	}
	
	//Action method for placing the breadcrumbs from Breadcrumbs Navxt Plugin
	final public function dc_breadcrumbs_render(){
		if(function_exists('bcn_display') && !is_home() && !is_front_page()):
		
			//Get predefined object data
			$object_meta = $this->get_predefined_metabox_object( get_queried_object() );
			
			//Define breadcrumbs
			$dc_breadcrumbs = $object_meta->metadata[$object_meta->dc_breadcrumbs][0];
			
			//Check the option settings
			if($dc_breadcrumbs == 'on' || $dc_breadcrumbs != 'off'):
			?>
			<div class="dc_breadcrumbs">
				<div class="container">
					<?php bcn_display(); ?>
				</div>
			</div>
			<?php
			endif;
			
		endif;
	}
	
	//Action method of placing the CoverPic template
	final public function dc_coverpic_render(){
		include_once DC_ABSPATH . '/coverpic.php';
	}
	
	//Action method to display category Title and Description in archive pages
	final public function dc_loop_start_archive(){
		//Only show run this on category archive pages
		if(is_archive()):
			//Gets the query object of the category archive
			$wp_query = get_queried_object();
			$title = $wp_query->name;
			$description = $wp_query->description;
			
			//Get CoverPic metadata
			$coverpic = $this->get_predefined_metabox_object($wp_query);
			$dc_hide_title = $coverpic->metadata[$coverpic->dc_hide_title_name][0];
			?>
			<div class="dc_cat_information">
				<?php if($dc_hide_title == 'on' || $dc_hide_title != 'off'): ?>
				<h1><?php echo sanitize_text_field($wp_query->name); ?></h1>
				<?php endif; ?>
				<div class="dc_cat_description"><?php echo html_entity_decode(esc_html($description)); ?></div>
			</div>
		<?php
		endif;
	}
	
	/*
	Filter method for modifying the post class omitted in each <archive> element in archive pages
	Note: The result is that it only allows standard post format to add the "has-post-thumbnail" class to the <archive> element
	*/
	final public function dc_post_class_archive($classes){
		if(is_archive()){
			$post_format = et_pb_post_format();
			if(
				in_array($post_format, array('link', 'audio', 'quote', 'video', 'gallery')) ||
				('on' !== et_get_option( 'divi_thumbnails_index', 'on' ))
			){
				$index = array_search('has-post-thumbnail', $classes);
				if($index !== false){
					unset($classes[$index]);
				}
				return $classes;
			}
			return $classes;
		}
		return $classes;
	}
	
}
new DC_Hooks;