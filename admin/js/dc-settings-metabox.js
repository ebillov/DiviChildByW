jQuery(document).ready(function(){
	
	//Disable submit on pressing enter key
	jQuery('#dc_overlay_wrapper').find('input, select').keydown(function(e){
		if(e.keyCode == 13) {
			e.preventDefault();
			return false;
		}
	});
	
	//For Ajax saving click event on checkboxes
	jQuery('#dc_main_options input').off('change').on('change', function(e){
		dc_ajax_postmeta_checkbox(this);
	});
	
	//Define Ajax saving for main CoverPic checkboxes (basically the checkboxes found in the right-hand side of the post/page editor)
	function dc_ajax_postmeta_checkbox(element){
		
		//For saving info message
		jQuery('#dc_main_options').append('<div class="dc_postmeta_overlay"><div>Saving...<span class="spinner is-active"></span></div></div>');
		
		//For handling unset checkboxes (unchecked) because serialize() doesn't allow unchecked checboxes
		//More info: https://stackoverflow.com/questions/3029870/jquery-serialize-does-not-register-checkboxes
		if( jQuery('input[name=' + element.name + ']:not(:checked)').map(function(){ return this.name + '=off' }).get()[0] ){
			var form = jQuery('#dc_main_options').find('input').serialize(), //Get the main checkboxes and serialize it for POST method
				form = form + '&' + jQuery('input[name=' + element.name + ']:not(:checked)').map(function(){ return this.name + '=off' }).get()[0]; //Add the unchecked checkbox because serialize() doesn't include unset checkboxes
		} else {
			var form = jQuery('#dc_main_options').find('input').serialize(); //Get the main checkboxes and serialize it for POST method
		}
		
		jQuery.ajax({
			url: dc.ajax_url,
			type: 'POST',
			dataType: 'json',
			data: 'action=dc_postmeta_checkbox_main&'+ form,
			error: function(error){
				if(error.statusText == 'error') {
					jQuery('#dc_main_options .dc_postmeta_overlay').html('<div style="color: #ff0000;">There was an error when saving. Please reload the page and try again.</div>');
				}
			},
			success: function(data){
				if(data){
					
					//For CoverPic Settings checkbox
					if( element.name == dc.coverpic_settings ){
						if( data[dc.coverpic_settings] == 'on' ){
							jQuery(jQuery(element)[0].nextSibling).removeClass('disabled').addClass('enabled').html('Enabled');
						} else {
							jQuery(jQuery(element)[0].nextSibling).removeClass('enabled').addClass('disabled').html('Disabled');
						}
					}
					
					//For Breadcrumbs (NavXT) checkbox
					if( element.name == dc.breadcrumbs ){
						if( data[dc.breadcrumbs] == 'on' ){
							jQuery(jQuery(element)[0].nextSibling).removeClass('disabled').addClass('enabled').html('Enabled');
						} else {
							jQuery(jQuery(element)[0].nextSibling).removeClass('enabled').addClass('disabled').html('Disabled');
						}
					}
					
					jQuery('.dc_postmeta_overlay div').html('<i style="color: #009688">Settings Saved!</i>');
					setTimeout(function(){
						jQuery('.dc_postmeta_overlay').fadeOut(function(){
							jQuery('.dc_postmeta_overlay').remove();
						});
					}, 2000);
					
				}
			}
		});
	}
	
	//Define Ajax saving for CoverPic settings
	function dc_ajax_saving(){
		
		//New way of sending off POST data request
		var form_fields = jQuery('#dc_overlay_wrapper').find('input, select'),
			serialized_data = '';
			
		//Iterate through each of the form fields and encode to HTML entities with each of the values
		jQuery.each(form_fields, function(e){
			if(form_fields.length == (e + 1)){
				if(this.type == 'checkbox'){
					if(this.checked){
						serialized_data += this.name + '=' + this.value;
					} else {
						serialized_data += this.name + '=off';
					}
				} else {
					serialized_data += this.name + '=' + encodeURIComponent(he.encode(this.value));
				}
			} else {
				if(this.type == 'checkbox'){
					if(this.checked){
						serialized_data += this.name + '=' + this.value + '&';
					} else {
						serialized_data += this.name + '=off&';
					}
				} else {
					serialized_data += this.name + '=' + encodeURIComponent(he.encode(this.value)) + '&';
				}
			}
		});
		
		jQuery.ajax({
			url: dc.ajax_url,
			type: 'POST',
			//dataType: 'json',
			data: 'action=dc_meta_settings&' + serialized_data,
			error: function(error){
				if(error.statusText == 'error') {
					jQuery('#dc_save_info').html('An error occured while saving. Please reload the page and try again.').attr('style', 'color: #ff0000;');
				}
			},
			success: function(data){
				if(data){
					jQuery('#dc_save_info').removeAttr('style');
					jQuery('#dc_save_info').html('CoverPic settings are saved!');
					jQuery('#dc_overlay_wrapper').data('dc_default_state', false); //Set default state to false
					setTimeout(function(){
						jQuery('#dc_save_info').fadeOut(function(e){
							jQuery('#dc_save').fadeIn('slow');
							jQuery('#dc_cancel').fadeIn('slow');
							jQuery('#dc_overlay_wrapper').fadeOut('fast', function(){
								jQuery('#adminmenumain').removeAttr('style'); //Remove defined overlay perspective
							});
							jQuery('#dc_save_info').remove();
							dc_init_data_save(); //Place the new data values
						});
					},1000);
				}
			}
		});
	}
	
	jQuery('#dc_advance_settings').removeAttr('disabled'); //Enable button when page is fully loaded.
	
	dc_class_sequence = dc.class_array.join(); //Join arrays like implode() in PHP
		
	//Store data value if the settings are in default state
	if(dc.meta.default_settings == ''){
		jQuery('#dc_overlay_wrapper').data('dc_default_state', true);
	} else {
		jQuery('#dc_overlay_wrapper').data('dc_default_state', false);
	}
	
	//Click event for showing the overlay wrapper
	jQuery(dc.metabox_id + ' button').off('click').on('click', function(e){
		e.preventDefault();
		if( jQuery(e.target).attr('id') == 'dc_cancel' ) {
			jQuery('#dc_overlay_wrapper').fadeOut('fast', function(){
				jQuery('#dc_overlay_container').removeAttr('style');
				jQuery('body').removeClass('dc_disabled_overflow'); //Enable body document scrolling again
				jQuery('#adminmenumain').removeAttr('style'); //Remove defined overlay perspective
			});
			dc_init_data(dc.class_array); //For loading intitial data
		} else if( jQuery(e.target).attr('id') == 'dc_save' ) {
			jQuery(e.target).after('<div id="dc_save_info" style="color: #333333;">Saving banner settings... <span class="spinner is-active"></span></div>');
			jQuery(e.target).hide();
			jQuery('#dc_cancel').hide();
			dc_ajax_saving(); //Do ajax saving
			jQuery('body').removeClass('dc_disabled_overflow'); //Enable body document scrolling again
		} else {
			jQuery('#adminmenumain').attr('style', 'position: relative; z-index: -1;'); //Change overlay perspective of when modal overlay is shown
			jQuery(window).scrollTop('top'); //Scroll to top when showing the overlay wrapper
			jQuery('body').addClass('dc_disabled_overflow'); //Disable body document scrolling
			jQuery('#dc_overlay_wrapper').fadeIn('fast');
			dc_dynamic_popup_style();
			
			//Begin logic for whether the settings are default or not
			if( jQuery('#dc_overlay_wrapper').data('dc_default_state') == true ){
				dc_load_default_values(dc_class_sequence);
			} else {
				dc_print_inline_style(dc_class_sequence);
			}
			
		}
	});
	
	//Define dynamic styling for the dc popup settings
	function dc_dynamic_popup_style(dc_interval){
		/*
		var dc_wrapper_height = jQuery('#dc_overlay_container').outerHeight(),
			dc_top_const = (jQuery(window).height() - dc_wrapper_height)/2;
		jQuery('#dc_overlay_container').attr('style', 'top: ' + dc_top_const + 'px;');
		*/
		if(dc_interval) {
			clearInterval(dc_interval); //From the media modal
		}
		
		//Disable the left panel settings when a very old safari is detected
		var safari_old_ver = new RegExp("5.1.7");
		if( safari_old_ver.test(navigator.userAgent) ){
			jQuery('.dc_left_panel').hide();
			jQuery('.safari_error_message').remove();
			jQuery('#dc_import_export_wrapper').after('<span class="safari_error_message" style="color: #ff0000; font-weight: 600;">Preview is not available for Safari 5.1.7</span>');
		}
	}
	
	//For on change event
	jQuery('.dc_options_config select').off('change').on('change', function(e){
		
		if( jQuery(this).attr('data-key') == 'dc_mobile_settings_1' ) {
			if( jQuery(this).val() == 'yes') {
				jQuery(this).addClass('dc_enabled_selection');
				jQuery('.dc_toggle_1').addClass('dc_enabled_option');
			} else {
				jQuery('.dc_toggle_1').removeClass('dc_enabled_option');
				jQuery(this).removeClass('dc_enabled_selection');
			}
		}
		
		if(jQuery(this).attr('data-key') == 'dc_mobile_settings_2') {
			if(jQuery(this).val() == 'yes') {
				jQuery(this).addClass('dc_enabled_selection');
				jQuery('.dc_toggle_2').addClass('dc_enabled_option');
			} else {
				jQuery('.dc_toggle_2').removeClass('dc_enabled_option');
				jQuery(this).removeClass('dc_enabled_selection');
			}
		}
		
	});
	
	//For storing up initial values on page load
	//Had to do it in timeout method because of the jscolor inline style attribute
	setTimeout(function(){
		dc_init_data_save();
	}, 10);
	
	//Had to separate it as a function to be used later when done saving via ajax
	function dc_init_data_save(){
		
		jQuery('.dc_right_panel input, .dc_right_panel select, #dc_bg_config_field_area input, #dc_bg_config_field_area select').each(function(e){
			if(jQuery(this).is('.jscolor')) { //For inline styling due to jscolor
				jQuery(this).data('dc_init_data_jscolor', jQuery(this).css('color'));
			}
			//Lets also capture the checboxes
			if(this.type == 'checkbox'){
				if(this.checked){
					jQuery(this).data('dc_init_data', 'on');
				} else {
					jQuery(this).data('dc_init_data', 'off');
				}
			} else {
				jQuery(this).data('dc_init_data', jQuery(this).val()); //For all other fields
			}
		});
		
		//For image input url default data
		jQuery('#dc_demo_img_input').data('dc_init_data', jQuery('#dc_demo_img_input').val()); //For the img input field
		jQuery('#dc_demo_img').data('dc_init_data', jQuery('#dc_demo_img').attr('src')); //For img element src
	
	}
	
	//Define re-call of initial data values
	function dc_init_data(dc_class_array){
		jQuery('#dc_demo_img_input').val(jQuery('#dc_demo_img_input').data('dc_init_data')); //For the img input field
		jQuery('#dc_demo_img').attr('src', jQuery('#dc_demo_img').data('dc_init_data')); //For img element src
		for( i = 0 ; i < dc_class_array.length; i++) { //For all other fields
			if( jQuery(dc_class_array[i]).is('.jscolor') ) { //For inline styling due to jscolor
				jQuery(dc_class_array[i]).attr('style',
				'background-color: #' + jQuery(dc_class_array[i]).data('dc_init_data') + ';\
				color: ' + jQuery(dc_class_array[i]).data('dc_init_data_jscolor') + ';');
			}
			//Lets also capture the checboxes
			if(jQuery(dc_class_array[i])[0].type == 'checkbox'){
				if(jQuery(dc_class_array[i]).data('dc_init_data') == 'on'){
					jQuery(dc_class_array[i]).prop('checked', 'checked');
				} else {
					jQuery(dc_class_array[i]).prop('checked', false);
				}
			} else { //Do the rest of the fields right here
				jQuery(dc_class_array[i]).val( jQuery(dc_class_array[i]).data('dc_init_data') ); //For all other fields
			}
		}
		//Empty the image data values prior to data storage when choosing banner image
		jQuery('#dc_demo_img').data('dc_img_true_height', '');
		jQuery('#dc_demo_img').data('dc_img_true_width', '');
	}
	
	//Get change and input events through each of the class
	jQuery(dc_class_sequence).off('change, input').on('change, input', function(e){
		dc_print_inline_style(dc_class_sequence);
	});
	
	//Had to separate the change events for background-color from above because .change() method is faster than the .off() or .on() methods
	//This was also necessary due to the event handler present in the jscolor js lib
	jQuery('.' + dc.background_color + ', .' + dc.title_font_color_name + ', .' + dc.sub_title_font_color_name).change(function(){
		dc_print_inline_style(dc_class_sequence);
	});
	
	//Hex to RGB color converter
	function hexToRgb(hex) {
		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		return result ? {
			r: parseInt(result[1], 16),
			g: parseInt(result[2], 16),
			b: parseInt(result[3], 16)
		} : null;
	}
	
	//Regex pattern for color hex values
	//Basically removing the # (hash tag)
	function regex_color_code(color_code){
		var result1 = /^#?([a-f\d]{6})$/i.exec(color_code);
		var result2 = /^#?([a-f\d]{3})$/i.exec(color_code);
		return result1 ? result1[1] : result2[1];
	}
	
	//Define dynamic inline styling function
	function dc_print_inline_style(dc_class_sequence) {
		
		var dc_inline_style_h1 = '',
			dc_inline_style_h2 = '',
			dc_demo_container = jQuery('#dc_demo_wrapper .dc_demo_container'),
			dc_demo_h1 = jQuery('#dc_demo_wrapper .dc_demo_container h1'),
			dc_demo_h2 = jQuery('#dc_demo_wrapper .dc_demo_container h2'),
			dc_demo_parent_wrapper = jQuery('#dc_demo_preview_wrapper'),
			dc_demo_wrapper = jQuery('#dc_demo_wrapper'),
			dc_demo_wrapper_inline_style = '',
			dc_demo_bg_color = jQuery('input[data-key="dc_background_color"]'),
			dc_demo_bg_opacity = jQuery('input[data-key="dc_background_opacity"]'),
			WP_current_title_element = jQuery(dc.wp_title_element),
			dc_demo_title_element = jQuery('input[data-key="dc_title"]'),
			dc_demo_img_element = jQuery('#dc_demo_img'),
			dc_demo_box_element = jQuery('#dc_demo_box');
			
		//Set initial flex wrapper
		var dc_inline_init = 'top: ' + (jQuery('.dc_header_wrapper').outerHeight() + 23) + 'px; height: ' + dc_demo_img_element.outerHeight() + 'px;';
		setTimeout(function(){
				dc_inline_init += dc_demo_wrapper.data('style');
				dc_demo_wrapper.attr('style', dc_inline_init);
			}, 100);
		
		//Set after loading img src
		dc_demo_img_element.load(function(e){
			dc_inline_init = 'top: ' + (jQuery('.dc_header_wrapper').outerHeight() + 23) + 'px; height: ' + dc_demo_img_element.data('dc_img_true_height') + 'px;';
			dc_inline_init += dc_demo_wrapper.data('style');
			dc_demo_wrapper.attr('style', dc_inline_init);
			dc_dynamic_popup_style(); //Load the dynamic styling for the popup settings again
			
			//For check image dimensions
			jQuery('#dc_demo_img_1_w_message').remove();
			jQuery('#dc_demo_img_2_w_message').remove();
			var demo_img = dc_demo_img_element[0];
			if(dc_demo_img_element.data('dc_img_true_width') && dc_demo_img_element.data('dc_img_true_width') < 1299){
				dc_demo_img_element.after('<div id="dc_demo_img_1_w_message" class="dc_warning_message_wrapper" style="top: 72px; left: 20px;">Banner image must have a <b>Width</b> of 1300px (pixels) or higher.</div>');
			}
			if(dc_demo_img_element.data('dc_img_true_height') && dc_demo_img_element.data('dc_img_true_height') < 599){
				dc_demo_img_element.after('<div id="dc_demo_img_2_w_message" class="dc_warning_message_wrapper" style="top: 112px; left: 20px;">Banner image must have a <b>Height</b> of 600px (pixels) or higher.</div>');
			}
		});
		
		//Loop the elements and apply inline styles
		jQuery(dc_class_sequence).each(function(e){
			
			if(jQuery(this).attr('data-key') == 'dc_title'){
				var dc_demo_title_timeout = setTimeout(function(){ //This timeout is for placing default value to the Page title input field when empty
					if(!dc_demo_title_element.val()) {
						dc_demo_h1.html( WP_current_title_element.val() ); //Apply the current title to the demo panel
						dc_demo_title_element.val( WP_current_title_element.val() ); //Apply default value to the page title input field
					}
				},10);
				if(jQuery(this).val()) {
					clearTimeout(dc_demo_title_timeout); //Always clear timeout to avoid unexpected timeout loop
					dc_demo_h1.html( jQuery(this).val() );
				}
			} else if(jQuery(this).attr('data-key')== 'dc_sub_title'){
				dc_demo_h2.html( jQuery(this).val() );
			} else {
				
				if(jQuery(this).attr('data-key') == 'dc_title_pos'){
					//dc_inline_style_h1 .= 'text-align: ' + jQuery(this).val() + 'px;
					if(jQuery(this).val() == 'center_center') {
						dc_demo_container.attr('style', 'align-items: center;');
						dc_inline_style_h1 = 'text-align: center;';
						dc_inline_style_h2 = dc_inline_style_h1;
					} else if(jQuery(this).val() == 'top_center') {
						dc_demo_container.attr('style', 'align-items: flex-start;');
						dc_inline_style_h1 = 'text-align: center;';
						dc_inline_style_h2 = dc_inline_style_h1;
					} else if(jQuery(this).val() == 'bottom_center') {
						dc_demo_container.attr('style', 'align-items: flex-end;');
						dc_inline_style_h1 = 'text-align: center;';
						dc_inline_style_h2 = dc_inline_style_h1;
					} else if(jQuery(this).val() == 'top_right') {
						dc_demo_container.attr('style', 'align-items: flex-start;');
						dc_inline_style_h1 = 'text-align: right;';
						dc_inline_style_h2 = dc_inline_style_h1;
					} else if(jQuery(this).val() == 'top_left') {
						dc_demo_container.attr('style', 'align-items: flex-start;');
						dc_inline_style_h1 = 'text-align: left;';
						dc_inline_style_h2 = dc_inline_style_h1;
					} else if(jQuery(this).val() == 'left_center') {
						dc_demo_container.attr('style', 'align-items: center;');
						dc_inline_style_h1 = 'text-align: left;';
						dc_inline_style_h2 = dc_inline_style_h1;
					} else if(jQuery(this).val() == 'right_center') {
						dc_demo_container.attr('style', 'align-items: center;');
						dc_inline_style_h1 = 'text-align: right;';
						dc_inline_style_h2 = dc_inline_style_h1;
					} else if(jQuery(this).val() == 'bottom_left') {
						dc_demo_container.attr('style', 'align-items: flex-end;');
						dc_inline_style_h1 = 'text-align: left;';
						dc_inline_style_h2 = dc_inline_style_h1;
					} else {
						dc_demo_container.attr('style', 'align-items: flex-end;');
						dc_inline_style_h1 = 'text-align: right;';
						dc_inline_style_h2 = dc_inline_style_h1;
					}
					
				}
				
				if(jQuery(this).attr('data-key') == 'dc_background_color'){
					jQuery('#' + dc_demo_bg_opacity[0].name + '_w_message').remove();
					var r = hexToRgb('#' + regex_color_code( jQuery(this).val() )).r,
						g = hexToRgb('#' + regex_color_code( jQuery(this).val() )).g,
						b = hexToRgb('#' + regex_color_code( jQuery(this).val() )).b;
					if( dc_demo_bg_opacity.val() == '' ){
						dc_demo_bg_opacity.after('<div id="' + dc_demo_bg_opacity[0].name + '_w_message" class="dc_warning_message_wrapper">Value must not be empty.</div>');
						dc_demo_wrapper_inline_style = 'background-color: rgb(' + r + ',' + g + ',' + b + ');';
					} else {
						if( parseInt(dc_demo_bg_opacity.val()) > 0 && parseInt(dc_demo_bg_opacity.val()) < 100 ) {
							dc_demo_bg_opacity.val(parseInt(dc_demo_bg_opacity.val())); //Set value to integer only. Otherwise error message is displayed.
							var a = ( parseInt(dc_demo_bg_opacity.val()) / 100 );
							dc_demo_wrapper_inline_style = 'background-color: rgba(' + r + ',' + g + ',' + b + ',' + a + ');';
						} else {
							dc_demo_bg_opacity.after('<div id="' + dc_demo_bg_opacity[0].name + '_w_message" class="dc_warning_message_wrapper">Value must be in between 1-99 only.</div>');
							dc_demo_wrapper_inline_style = 'background-color: rgb(' + r + ',' + g + ',' + b + ');';
						}
					}
				}
				
				if(jQuery(this).attr('data-key') == 'dc_background_height'){
					//500 is the constant number based on the max-height css for the ID's: dc_demo_preview_wrapper, dc_demo_wrapper
					if(dc_demo_img_element.data('dc_img_true_height')){ //Perform code if data is set
						var dc_demo_img_true_ratio = (dc_demo_img_element.data('dc_img_true_width') / dc_demo_img_element.data('dc_img_true_height')),
							dc_demo_img_true_height = dc_demo_parent_wrapper.width() / dc_demo_img_true_ratio,
							val = (( dc_demo_img_true_height > 500) ? 500 : dc_demo_img_true_height );
						dc_demo_wrapper_inline_style += dc_backround_height(jQuery(this), val, dc_demo_wrapper_inline_style, dc_demo_parent_wrapper);
					} else { //If data is not set yet
						var val = ((dc_demo_img_element.outerHeight() > 500) ? 500 : dc_demo_img_element.outerHeight());
						dc_demo_wrapper_inline_style += dc_backround_height(jQuery(this), val, dc_demo_wrapper_inline_style, dc_demo_parent_wrapper);
					}
					
					//Define function for styling wrapper/overlay height
					function dc_backround_height(target, val, dc_demo_wrapper_inline_style, dc_demo_parent_wrapper){
						if(dc_demo_img_element.data('dc_img_true_height') && dc_demo_img_element.data('dc_img_true_height') < 600){ //Initial height of image must be 600px or greater
							var val = 600;
						} else {
							//var val = 600; //requires workaround
						}
						if(jQuery('select[data-key="dc_menu_overlay"]').val() == 'yes') {
							var dc_demo_menu_overlay_height = 74; //74 is the constant height of the dc_demo_menu_overlay element
						} else {
							var dc_demo_menu_overlay_height = 16; //16 is the constant for top spacing
						}
						if(target.val() == 25) {
							dc_demo_wrapper_inline_style += 'height: ' + ((val * 0.25) + dc_demo_menu_overlay_height ) + 'px;';
							dc_demo_parent_wrapper.data('style', 'height: ' + ((val * 0.25) + dc_demo_menu_overlay_height) + 'px;');
						} else if(target.val() == 50) {
							dc_demo_wrapper_inline_style += 'height: ' + (val * 0.5) + 'px;';
							dc_demo_parent_wrapper.data('style', 'height: ' + (val * 0.5) + 'px;');
						} else if(target.val() == 75) {
							dc_demo_wrapper_inline_style += 'height: ' + (val * 0.75) + 'px;';
							dc_demo_parent_wrapper.data('style', 'height: ' + (val * 0.75) + 'px;');
						} else {
							dc_demo_wrapper_inline_style += 'height: ' + val + 'px;';
							dc_demo_parent_wrapper.data('style', 'height: ' + val + 'px;');
						}
						dc_dynamic_popup_style(); //Load dynamic popup positioning
						return dc_demo_wrapper_inline_style;
					}
				}
				
				if(jQuery(this).attr('data-key') == 'dc_hide_title'){
					if(this.checked){
						jQuery('#dc_options_config_title').attr('style', 'opacity: 0.5; pointer-events: none;');
					} else {
						jQuery('#dc_options_config_title').removeAttr('style');
					}
				}
				
				if(jQuery(this).attr('data-key') == 'dc_hide_sub_title'){
					if(this.checked){
						jQuery('#dc_options_config_sub_title').attr('style', 'opacity: 0.5; pointer-events: none;');
					} else {
						jQuery('#dc_options_config_sub_title').removeAttr('style');
					}
				}
				
				if(jQuery(this).attr('data-key') == 'dc_title_font_size'){
					jQuery('#' + jQuery(this)[0].name + '_w_message').remove();
					if( parseInt(jQuery(this).val()) > 19 && parseInt(jQuery(this).val()) < 101 ){
						jQuery(this).val(parseInt(jQuery(this).val())); //Set value to integer only. Otherwise error message is displayed.
						dc_inline_style_h1 += 'font-size: ' + parseInt(jQuery(this).val()) + 'px;';
					} else {
						jQuery(this).after('<div id="' + jQuery(this)[0].name + '_w_message" class="dc_warning_message_wrapper">Value must be in between 20-100 only.</div>');
					}
				}
				
				if(jQuery(this).attr('data-key') == 'dc_title_font_weight'){
					if(jQuery(this).val() == 'medium') {
						dc_inline_style_h1 += 'font-weight: 600;';
					} else {
						dc_inline_style_h1 += 'font-weight: ' + jQuery(this).val() + ';';
					}
				}
				
				if(jQuery(this).attr('data-key') == 'dc_title_font_color'){
					dc_inline_style_h1 += 'color: #' + regex_color_code( jQuery(this).val() ) + ';';
				}
				
				if(jQuery(this).attr('data-key') == 'dc_sub_title_font_size'){
					jQuery('#' + jQuery(this)[0].name + '_w_message').remove();
					if( parseInt(jQuery(this).val()) > 13 && parseInt(jQuery(this).val()) < 101 ){
						jQuery(this).val(parseInt(jQuery(this).val())); //Set value to integer only. Otherwise error message is displayed.
						dc_inline_style_h2 += 'font-size: ' + parseInt(jQuery(this).val()) + 'px;';
					} else {
						jQuery(this).after('<div id="' + jQuery(this)[0].name + '_w_message" class="dc_warning_message_wrapper">Value must be in between 14-100 only.</div>');
					}
				}
				
				if(jQuery(this).attr('data-key') == 'dc_sub_title_font_weight'){
					if(jQuery(this).val() == 'medium') {
						dc_inline_style_h2 += 'font-weight: 600;';
					} else {
						dc_inline_style_h2 += 'font-weight: ' + jQuery(this).val() + ';';
					}
				}
				
				if(jQuery(this).attr('data-key') == 'dc_sub_title_font_color'){
					dc_inline_style_h2 += 'color: #' + regex_color_code( jQuery(this).val() ) + ';';
				}
				
			}
			
			//Finally apply the inline style at the end of the loop
			if(e == (dc.class_array.length - 1)) {
				
				//For the parent wrapper
				dc_demo_parent_wrapper.attr('style', dc_demo_parent_wrapper.data('style'));
				
				//For the sub element wrapper
				dc_demo_wrapper.data('style', dc_demo_wrapper_inline_style); //had to place it in data for load functionality with img src display
				
				//For the h1 and h2 elements
				dc_demo_h1.attr('style', dc_inline_style_h1);
				dc_demo_h2.attr('style', dc_inline_style_h2);
				
				if(jQuery('select[data-key="dc_menu_overlay"]').val() == 'yes') {
					var dc_demo_menu_overlay_height = 74, //74 is the constant height of the dc_demo_menu_overlay element
						dc_calc_height = dc_demo_parent_wrapper.outerHeight() - dc_demo_box_element.outerHeight() - dc_demo_menu_overlay_height;
				} else {
					var dc_demo_menu_overlay_height = 16, //16 is the constant for top spacing
						dc_calc_height = dc_demo_parent_wrapper.outerHeight() - dc_demo_box_element.outerHeight() - dc_demo_menu_overlay_height;
				}
				
				if( dc_calc_height < 0 ) {
					dc_demo_parent_wrapper.attr('style', dc_demo_parent_wrapper.data('style') + 'height: ' + (dc_demo_box_element.outerHeight() + dc_demo_menu_overlay_height) + 'px;');
					dc_demo_wrapper.data('style', dc_demo_wrapper_inline_style + 'height: ' + (dc_demo_box_element.outerHeight() + dc_demo_menu_overlay_height) + 'px;');
				}
				
			}
			
		});
	}
	
	jQuery('#dc_choose_img_btn').off('click').on('click', function(e){
		e.preventDefault();
		dc_show_modal_wp_media();
	});
	
	//Define frame media modal function
	function dc_show_modal_wp_media(){
		
		//Define variables
		var dc_media_frame,
			dc_img_elem = jQuery('#dc_demo_img'),
			dc_img_input_elem = jQuery('#dc_demo_img_input'),
			dc_overlay_elem = jQuery('#dc_overlay_wrapper');
		
		dc_overlay_elem.addClass('dc_overlay_wrapper');
		
		//If media frame exists, re-open it
		if( dc_media_frame ) {
			dc_media_frame.open();
			return;
		}
		
		dc_media_frame = wp.media({
			title: 'Select / Upload Photo as Background Image',
			button: {
				text: 'Use as Background Image'
			},
			multiple: false
		});
		
		var dc_timeout = setTimeout(function(){
			jQuery('#dc_overlay_wrapper').data('dc_media_frame_id', jQuery(dc_media_frame.el).parent().parent().parent().attr('id'));
		}, 10);
		
		var dc_interval = setInterval(function(){
			if( jQuery('#' + jQuery('#dc_overlay_wrapper').data('dc_media_frame_id') + '').css('display') == 'none' ){
				clearInterval(dc_interval);
				clearTimeout(dc_timeout);
				dc_overlay_elem.removeClass('dc_overlay_wrapper'); //Remove the overlay class
				dc_dynamic_popup_style(dc_interval); //Load dynamic popup positioning
				dc_print_inline_style(dc_class_sequence); //Load dynamic data again
			}
		}, 10);
		
		//Do line of codes on selection
		dc_media_frame.on('select', function(){
			
			var attachment = dc_media_frame.state().get('selection').first().toJSON();
			dc_img_elem.data('dc_img_true_height', attachment.height); //place true height image data
			dc_img_elem.data('dc_img_true_width', attachment.width); //place true width image data
			
			//We need to get the relative url path for the image src
			var url_array = attachment.url.split('/'),
				wp_content_index = url_array.indexOf('wp-content'),
				img_url = '';
				
			//Define the logic for getting the relative url path for the image src
			if(wp_content_index < 0){
				dc_img_elem.attr('src', attachment.url); //Set the URL to img element
				dc_img_input_elem.val(attachment.url); //Set input value to URL
			} else {
				for(i = wp_content_index; i < url_array.length; i++){
					img_url += '/' + url_array[i];
					if(i == (url_array.length - 1)) {
						dc_img_elem.attr('src', dc.site_url + img_url); //Set the URL to img element
						dc_img_input_elem.val(img_url); //Set input value to URL
					}
				}
			}
			
			
			dc_overlay_elem.removeClass('dc_overlay_wrapper'); //Remove the overlay class
			
		});
		
		//Open the frame on run
		dc_media_frame.open();
		
	}
	
	//Define a function to load default values when using the metabox settings for the first time.
	function dc_load_default_values(dc_class_sequence){
		var dc_default_config = {
				dc_title: { css_class: '.' + dc.title_name, value: '' + jQuery(dc.wp_title_element).val() + '' },
				dc_position: { css_class: '.' + dc.title_pos_name, value: 'center_center' },
				dc_bg_color: { css_class: '.' + dc.background_color, value: '000000' },
				dc_bg_opacity: { css_class: '.' + dc.background_opacity, value: '50' },
				dc_bg_height: { css_class: '.' + dc.background_height, value: '50' },
				dc_title_font_size: { css_class: '.' + dc.title_font_size_name, value: '50' },
				dc_title_font_weight: { css_class: '.' + dc.title_font_weight_name, value: 'bold' },
				dc_title_font_color: { css_class: '.' + dc.title_font_color_name, value: 'FFFFFF' },
				dc_sub_title_font_size: { css_class: '.' + dc.sub_title_font_size_name, value: '18' },
				dc_sub_title_font_weight: { css_class: '.' + dc.sub_title_font_weight_name, value: 'normal' },
				dc_sub_title_font_color: { css_class: '.' + dc.sub_title_font_color_name, value: 'FFFFFF' }
			};
		
		//Loop object property names and place the values
		for( property in dc_default_config ) {
			if(property == 'dc_title') {
				jQuery( dc_default_config.dc_title.css_class ).val( dc_default_config.dc_title.value );
			} else if(property == 'dc_position'){
				jQuery( dc_default_config.dc_position.css_class ).val( dc_default_config.dc_position.value );
			} else if(property == 'dc_bg_color'){
				jQuery( dc_default_config.dc_bg_color.css_class ).val( dc_default_config.dc_bg_color.value );
			} else if(property == 'dc_bg_opacity'){
				jQuery( dc_default_config.dc_bg_opacity.css_class ).val( dc_default_config.dc_bg_opacity.value );
			} else if(property == 'dc_bg_height'){
				jQuery( dc_default_config.dc_bg_height.css_class ).val( dc_default_config.dc_bg_height.value );
			} else if(property == 'dc_title_font_size'){
				jQuery( dc_default_config.dc_title_font_size.css_class ).val( dc_default_config.dc_title_font_size.value );
			} else if(property == 'dc_title_font_weight'){
				jQuery( dc_default_config.dc_title_font_weight.css_class ).val( dc_default_config.dc_title_font_weight.value );
			} else if(property == 'dc_title_font_color'){
				jQuery( dc_default_config.dc_title_font_color.css_class ).val( dc_default_config.dc_title_font_color.value );
			} else if(property == 'dc_sub_title_font_size'){
				jQuery( dc_default_config.dc_sub_title_font_size.css_class ).val( dc_default_config.dc_sub_title_font_size.value );
			} else if(property == 'dc_sub_title_font_weight'){
				jQuery( dc_default_config.dc_sub_title_font_weight.css_class ).val( dc_default_config.dc_sub_title_font_weight.value );
			} else {
				jQuery( dc_default_config.dc_sub_title_font_color.css_class ).val( dc_default_config.dc_sub_title_font_color.value );
				dc_print_inline_style(dc_class_sequence); //Do inline styling after placing all of the default values
			}
		}
	}
	
	//For import and export functionality
	//For export window
	jQuery('#dc_export').off('click').on('click', function(e){
		e.preventDefault();
		
		//Exclude the title and sub title during export
		var form_fields = jQuery('#dc_overlay_wrapper').find('input, select');
		jQuery('#dc_overlay_wrapper').find('input, select').each(function(index){
			if(jQuery(this).attr('data-key') == 'dc_title' || jQuery(this).attr('data-key') == 'dc_sub_title'){
				delete form_fields[index];
			}
		});
		
		//Begin serializing
		var form_serialized_array = (form_fields.serialize()).split('&');
		form_serialized_array.shift(); //Remove the first array item
		jQuery(this).after('\
		<div id="dc_export_box">\
			<a href="#" class="dc_close_import_export">Close</a>\
			<label>Export Settings</label>\
			<span>Copy this text and paste it during import.</span>\
			<textarea>import_type=' + dc.post_type + ',' + form_serialized_array + '</textarea>\
		</div>\
		');
		jQuery(this).attr('disabled', 'disabled');
		jQuery('#dc_import').attr('disabled', 'disabled');
		dc_import_export_events();
	});
	
	//For import window
	jQuery('#dc_import').off('click').on('click', function(e){
		e.preventDefault();
		var form_serialized_array = (jQuery('#dc_overlay_wrapper').find('input, select').serialize()).split('&');
		form_serialized_array.shift(); //Remove the first array item
		jQuery(this).after('\
		<div id="dc_import_box">\
			<a href="#" class="dc_close_import_export">Close</a>\
			<label>Import Settings</label>\
			<span>Paste your exported text code and import it.</span>\
			<textarea></textarea>\
			<button id="dc_import_box_btn" class="button-secondary dc_import_box_btn" type="button">Import</button>\
		</div>\
		');
		jQuery(this).attr('disabled', 'disabled');
		jQuery('#dc_export').attr('disabled', 'disabled');
		dc_import_export_events();
	});
	
	//Had to do it this way so that the added dom element can be read
	function dc_import_export_events(){
		
		//For import action
		jQuery('#dc_import_box_btn').off('click').on('click', function(e){
			e.preventDefault();
			var import_array = jQuery('#dc_import_box textarea').val().split(',');
			dc_import_action(import_array);
		});
		
		//For close button
		jQuery('.dc_close_import_export').off('click').on('click', function(e){
			e.preventDefault();
			dc_remove_import_export_window();
		});
		
	}
	
	//Define wrapper elements to remove when exiting import/export wrapper elements
	function dc_remove_import_export_window(){
		jQuery('#dc_export').removeAttr('disabled');
		jQuery('#dc_export_box').remove();
		jQuery('#dc_import').removeAttr('disabled');
		jQuery('#dc_import_box').remove();
	}
	
	//Define import action for pasted text data
	function dc_import_action(import_array) {
		
		jQuery('#dc_import_error_message').remove();
		
		var item_array;
		for(i = 0; i < import_array.length; i++){
			
			//Assign it to a dynamic variable
			item_array = import_array[i].split('=');
			
			/*
			Start of error message handler
			Note: Indeed there are two logical opertators because of array values needed to be verified.
			*/
				//Break the loop if incorrect data import is detected
				if( (item_array.length > 0 || item_array.length == 0) && i == 0 && item_array[0] == 'import_type' && item_array[1] != dc.post_type ) {
					error_message();
					break;
				}
				
				//Break the loop if incorrect data import is detected
				if( (item_array.length > 0 || item_array.length == 0) && i == 0 && item_array[0] != 'import_type' && item_array[1] != dc.post_type ) {
					error_message();
					break;
				}
				
				//Define error message
				function error_message(){
					jQuery('#dc_import_error_message').remove();
					jQuery('#dc_import_box_btn').after('<span id="dc_import_error_message">Import Failed! Incorrect import data.</span>');
				}
			/* End of error message handler */
			
			//Target each element and apply the imported value
			if(decode_data(item_array) == 'on' || decode_data(item_array) == 'off'){
				if(decode_data(item_array) == 'on'){
					jQuery('#dc_overlay_wrapper input[name="' + item_array[0] + '"]').prop('checked', 'checked');
				} else {
					jQuery('#dc_overlay_wrapper input[name="' + item_array[0] + '"]').prop('checked', false);
				}
			} else {
				jQuery('#dc_overlay_wrapper input[name="' + item_array[0] + '"], #dc_overlay_wrapper select[name="' + item_array[0] + '"]').val( decode_data(item_array) );
			}
			
			//Define data handler for decoding serialized data
			function decode_data(item_array){
				return decodeURIComponent(item_array[1]).replace( /\+/g, ' ' );
			}
			
			if( (import_array.length - 1) == i ){
				jQuery('#dc_demo_img').attr('src', dc.site_url + jQuery('#dc_demo_img_input').val()); //For img src attribute
				
				//Empty the data value stored for the img element
				jQuery('#dc_demo_img').data('dc_img_true_height', '');
				jQuery('#dc_demo_img').data('dc_img_true_width', '');
				
				dc_print_inline_style(dc_class_sequence); //Apply the importd data and print to inline style
				dc_remove_import_export_window(); //Remove import/export wrapper elements
			}
			
		}
		
	}
	
});