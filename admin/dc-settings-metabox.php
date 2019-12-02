<?php
/*
This is our settings metabox template
*/

//Exit on unecessary access
defined('ABSPATH') or exit;
?>

<div id="dc_main_options" class="<?php echo $object_meta->dc_coverpic_settings; ?>_wrapper">
	<label>CoverPic Settings <input class="<?php echo $object_meta->dc_coverpic_settings; ?>" type="checkbox" name="<?php echo $object_meta->dc_coverpic_settings; ?>" <?php echo (($object_meta->metadata[$object_meta->dc_coverpic_settings][0] == 'on') ? 'checked' : ''); ?>/><?php echo (($object_meta->metadata[$object_meta->dc_coverpic_settings][0] == 'on') ? '<span class="enabled">Enabled</span>' : '<span class="disabled">Disabled</span>'); ?></label>
	
	<?php
	//Check if Breadcrumbs NavXT plugin is activated
	if(is_plugin_active('breadcrumb-navxt/breadcrumb-navxt.php')): ?>
	<hr>
	<label>Breadcrumbs (NavXT) <input class="<?php echo $object_meta->dc_breadcrumbs; ?>" type="checkbox" name="<?php echo $object_meta->dc_breadcrumbs; ?>" <?php echo (( $object_meta->metadata[$object_meta->dc_breadcrumbs][0] == 'on' || is_null($object_meta->metadata[$object_meta->dc_breadcrumbs][0]) ) ? 'checked' : ''); ?>/><?php echo (( $object_meta->metadata[$object_meta->dc_breadcrumbs][0] == 'on' || is_null($object_meta->metadata[$object_meta->dc_breadcrumbs][0]) ) ? '<span class="enabled">Enabled</span>' : '<span class="disabled">Disabled</span>'); ?></label>
	<?php endif; ?>
	
	<input type="hidden" name="_dc_object_type" value="<?php echo $object_meta->object_type; ?>"/>
	<input type="hidden" name="_dc_object_id" value="<?php echo $object_meta->id; ?>"/>
	<button id="dc_advance_settings" class="button-secondary" type="button" disabled="disabled">Show Settings</button>
</div>
<div id="dc_overlay_wrapper">
	<div id="dc_overlay_container" class="dc_overlay_container">
		<input type="hidden" name="_dc_object_type" value="<?php echo $object_meta->object_type; ?>"/>
		<input type="hidden" name="_dc_object_id" value="<?php echo $object_meta->id; ?>"/>
		
		<!-- This hidden field is for checking default options for the current meta box. -->
		<input type="hidden" name="<?php echo $object_meta->dc_default_settings; ?>" value="true"/>
		
		<div class="dc_header_wrapper">
			<h2 class="dc_heading_title">CoverPic <?php echo $object_meta->name; ?> Settings</h2>
			<button id="dc_choose_img_btn" class="button-secondary" type="button">Choose Image</button>
			<div id="dc_bg_config_field_area" class="dc_header_field_wrapper">
				<div class="dc_header_field_container">
					<label>BG Color</label>
					<input type="text" class="<?php echo $object_meta->dc_background_color; ?> jscolor" name="<?php echo $object_meta->dc_background_color; ?>" placeholder="Default: 000000" value="<?php echo $object_meta->metadata[$object_meta->dc_background_color][0]; ?>" data-key="dc_background_color" autocomplete="off"/>
				</div>
				<div class="dc_header_field_container">
					<label>BG Opacity</label>
					<input type="number" class="<?php echo $object_meta->dc_background_opacity; ?>" name="<?php echo $object_meta->dc_background_opacity; ?>" placeholder="Default: 50" value="<?php echo $object_meta->metadata[$object_meta->dc_background_opacity][0]; ?>" data-key="dc_background_opacity" autocomplete="off" min="1" max="99"/>
				</div>
				<div class="dc_header_field_container">
					<label style="top: 0;">BG Height</label>
					<select name="<?php echo $object_meta->dc_background_height; ?>" class="<?php echo $object_meta->dc_background_height; ?>" data-key="dc_background_height">
						<option value="25" <?php echo (($object_meta->metadata[$object_meta->dc_background_height][0] == '25') ? 'selected' : ''); ?>>25%</option>
						<option value="50" <?php echo (($object_meta->metadata[$object_meta->dc_background_height][0] == '50') ? 'selected' : ''); ?>>50%</option>
						<option value="75" <?php echo (($object_meta->metadata[$object_meta->dc_background_height][0] == '75') ? 'selected' : ''); ?>>75%</option>
						<option value="100" <?php echo (($object_meta->metadata[$object_meta->dc_background_height][0] == '100') ? 'selected' : ''); ?>>100%</option>
					</select>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div class="dc_config_area">
			<div class="dc_left_panel">
				<div id="dc_demo_preview_wrapper">
					<input id="dc_demo_img_input" class="<?php echo $object_meta->dc_background_img; ?>" type="hidden" name="<?php echo $object_meta->dc_background_img; ?>" value="<?php echo (($object_meta->metadata[$object_meta->dc_background_img][0] == null) ? '/wp-content/themes/Divi-Child/demo.jpg' : $object_meta->metadata[$object_meta->dc_background_img][0] ); ?>"/>
					<img id="dc_demo_img" src="<?php echo (($object_meta->metadata[$object_meta->dc_background_img][0] == null) ? DC_ABSURL . '/demo.jpg' : get_option('siteurl') . $object_meta->metadata[$object_meta->dc_background_img][0] ); ?>"/>
					<div id="dc_demo_wrapper">
						<div class="dc_demo_container">
							<div id="dc_demo_box" class="dc_demo_box">
								<h1></h1>
								<h2></h2>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="dc_right_panel">
				<label class="dc_<?php echo $object_meta->type . '_title'; ?>"><?php echo $object_meta->name . ' Title'; ?> <span style="font-style: italic; font-size: 14px; color: #795548;">(Default: Current title)</span> <span class="dc_hide_title">Hide <input data-key="dc_hide_title" type="checkbox" class="<?php echo $object_meta->dc_hide_title_name; ?>" name="<?php echo $object_meta->dc_hide_title_name; ?>" <?php echo (($object_meta->metadata[$object_meta->dc_hide_title_name][0] == 'on') ? 'checked' : ''); ?>/></span></label>
				<div id="dc_options_config_title" class="dc_options_config_wrapper">
					<input class="<?php echo $object_meta->dc_title_name; ?>" type="text" name="<?php echo $object_meta->dc_title_name; ?>" value="<?php echo $object_meta->metadata[$object_meta->dc_title_name][0]; ?>" data-key="dc_title" autocomplete="off"/>
					<div class="dc_left_panel_sub">
						<table>
							<tr>
								<td><label>Position</label></td>
								<td>
									<select class="<?php echo $object_meta->dc_title_pos_name; ?>" name="<?php echo $object_meta->dc_title_pos_name; ?>" data-key="dc_title_pos">
										<option value="center_center" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'center_center') ? 'selected' : ''); ?>>Center Center</option>
										<option value="top_center" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'top_center') ? 'selected' : ''); ?>>Top Center</option>
										<option value="bottom_center" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'bottom_center') ? 'selected' : ''); ?>>Bottom Center</option>
										<option value="top_left" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'top_left') ? 'selected' : ''); ?>>Top Left</option>
										<option value="top_right" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'top_right') ? 'selected' : ''); ?>>Top Right</option>
										<option value="left_center" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'left_center') ? 'selected' : ''); ?>>Left Center</option>
										<option value="right_center" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'right_center') ? 'selected' : ''); ?>>Right Center</option>
										<option value="bottom_left" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'bottom_left') ? 'selected' : ''); ?>>Bottom Left</option>
										<option value="bottom_right" <?php echo (($object_meta->metadata[$object_meta->dc_title_pos_name][0] == 'bottom_right') ? 'selected' : ''); ?>>Bottom Right</option>
									</select>
								</td>
							</tr>
						</table>
						<table>
							<tr>
								<td><label>Font Size</label></td>
								<td><input type="number" class="<?php echo $object_meta->dc_title_font_size_name; ?> dc_toggle_1" name="<?php echo $object_meta->dc_title_font_size_name; ?>" placeholder="Default: 30px" value="<?php echo $object_meta->metadata[$object_meta->dc_title_font_size_name][0]; ?>" data-key="dc_title_font_size" autocomplete="off" min="20" max="100"/></td>
							</tr>
						</table>
					</div>
					<div class="dc_right_panel_sub">
						<table>
							<tr>
								<td><label>Font Weight</label></td>
								<td>
									<select name="<?php echo $object_meta->dc_title_font_weight_name; ?>" class="<?php echo $object_meta->dc_title_font_weight_name; ?> dc_toggle_1" data-key="dc_title_font_weight">
										<option value="lighter" <?php echo (($object_meta->metadata[$object_meta->dc_title_font_weight_name][0] == 'lighter') ? 'selected' : ''); ?>>Lighter</option>
										<option value="normal" <?php echo (($object_meta->metadata[$object_meta->dc_title_font_weight_name][0] == 'normal') ? 'selected' : ''); ?>>Normal</option>
										<option value="medium" <?php echo (($object_meta->metadata[$object_meta->dc_title_font_weight_name][0] == 'medium') ? 'selected' : ''); ?>>Medium</option>
										<option value="bold" <?php echo (($object_meta->metadata[$object_meta->dc_title_font_weight_name][0] == 'bold') ? 'selected' : ''); ?>>Bold</option>
										<option value="bolder" <?php echo (($object_meta->metadata[$object_meta->dc_title_font_weight_name][0] == 'bolder') ? 'selected' : ''); ?>>Bolder</option>
									</select>
								</td>
							</tr>
						</table>
						<table>
							<tr>
								<td><label>Font Color</label></td>
								<td><input type="text" class="<?php echo $object_meta->dc_title_font_color_name; ?> dc_toggle_1 jscolor" name="<?php echo $object_meta->dc_title_font_color_name; ?>" placeholder="Default: #FFFFFF" value="<?php echo $object_meta->metadata[$object_meta->dc_title_font_color_name][0]; ?>" data-key="dc_title_font_color" autocomplete="off"/></td>
							</tr>
						</table>
					</div>
				</div>
				<hr class="dc_hr_devider">
				<label class="dc_<?php echo $object_meta->type . '_sub_title'; ?>"><?php echo $object_meta->name . ' Sub-Title'; ?> <span style="font-style: italic; font-size: 14px; color: #795548;">(Default: None)</span> <span class="dc_hide_sub_title">Hide <input data-key="dc_hide_sub_title" type="checkbox" class="<?php echo $object_meta->dc_hide_sub_title_name; ?>" name="<?php echo $object_meta->dc_hide_sub_title_name; ?>" <?php echo (($object_meta->metadata[$object_meta->dc_hide_sub_title_name][0] == 'on') ? 'checked' : ''); ?>/></span></label>
				<div id="dc_options_config_sub_title" class="dc_options_config_wrapper">
					<input class="<?php echo $object_meta->dc_sub_title_name; ?>" type="text" name="<?php echo $object_meta->dc_sub_title_name; ?>" value='<?php echo $object_meta->metadata[$object_meta->dc_sub_title_name][0]; ?>' data-key="dc_sub_title" autocomplete="off"/>
					<div class="dc_left_panel_sub">
						<table>
							<tr>
								<td><label>Font Size</label></td>
								<td><input type="number" class="<?php echo $object_meta->dc_sub_title_font_size_name; ?> dc_toggle_2" name="<?php echo $object_meta->dc_sub_title_font_size_name; ?>" placeholder="Default: 30px" value="<?php echo $object_meta->metadata[$object_meta->dc_sub_title_font_size_name][0]; ?>" data-key="dc_sub_title_font_size" autocomplete="off" min="14" max="100"/></td>
							</tr>
						</table>
					</div>
					<div class="dc_right_panel_sub">
						<table>
							<tr>
								<td><label>Font Weight</label></td>
								<td>
									<select name="<?php echo $object_meta->dc_sub_title_font_weight_name; ?>" class="<?php echo $object_meta->dc_sub_title_font_weight_name; ?> dc_toggle_2" data-key="dc_sub_title_font_weight">
										<option value="lighter" <?php echo (($object_meta->metadata[$object_meta->dc_sub_title_font_weight_name][0] == 'lighter') ? 'selected' : ''); ?>>Lighter</option>
										<option value="normal" <?php echo (($object_meta->metadata[$object_meta->dc_sub_title_font_weight_name][0] == 'normal') ? 'selected' : ''); ?>>Normal</option>
										<option value="medium" <?php echo (($object_meta->metadata[$object_meta->dc_sub_title_font_weight_name][0] == 'medium') ? 'selected' : ''); ?>>Medium</option>
										<option value="bold" <?php echo (($object_meta->metadata[$object_meta->dc_sub_title_font_weight_name][0] == 'bold') ? 'selected' : ''); ?>>Bold</option>
										<option value="bolder" <?php echo (($object_meta->metadata[$object_meta->dc_sub_title_font_weight_name][0] == 'bolder') ? 'selected' : ''); ?>>Bolder</option>
									</select>
								</td>
							</tr>
						</table>
						<table>
							<tr>
								<td><label>Font Color</label></td>
								<td><input type="text" class="<?php echo $object_meta->dc_sub_title_font_color_name; ?> dc_toggle_2 jscolor" name="<?php echo $object_meta->dc_sub_title_font_color_name; ?>" placeholder="Default: #FFFFFF" value="<?php echo $object_meta->metadata[$object_meta->dc_sub_title_font_color_name][0]; ?>" data-key="dc_sub_title_font_color" autocomplete="off"/></td>
							</tr>
						</table>
					</div>
				</div>
				<hr class="dc_hr_devider">
				<label class="dc_banner_img_alt_tag_label">CoverPic IMG ALT tag <span style="font-style: italic; font-size: 14px; color: #795548;">(Optional)</span><br /><span style="font-style: italic; font-size: 14px; color: #795548;">(Default: None)</span></label>
				<input class="<?php echo $object_meta->dc_background_img_alt_tag; ?> dc_backround_img_alt_tag" type="text" name="<?php echo $object_meta->dc_background_img_alt_tag; ?>" value="<?php echo $object_meta->metadata[$object_meta->dc_background_img_alt_tag][0]; ?>" autocomplete="off" placeholder="Enter the alt tag description for the CoverPic image"/>
			</div>
			<div id="dc_import_export_wrapper" class="dc_right_panel dc_import_export_wrapper">
				<table style="margin: auto;">
					<tr>
						<td><button id="dc_import" class="button-secondary" type="button">Import Settings</button></td>
						<td><button id="dc_export" class="button-secondary" type="button">Export Settings</button></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="dc_action_buttons">
			<table>
				<tr>
					<td><button id="dc_save" class="button-primary" type="button">Save CoverPic <?php echo $object_meta->name; ?> Settings</button></td>
					<td><button id="dc_cancel" class="button-secondary" type="button">Cancel</button></td>
				</tr>
			</table>
		</div>
	</div>
</div>