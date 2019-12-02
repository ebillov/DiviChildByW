<?php
/*
Template file for the child theme options page
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

//Lets get all of the associative options array
global $dc_options;

//Handle save/post requests to the server
do_action('dc_settings_action', $_POST);

//Get the option settings
$dc_settings = get_option('elegant-child-options');
?>
<div class="wrap dc_option_settings">
	<h2>Divi Child Options <span class="theme_version"><?php echo DC_VERSION; ?></span> <span class="change_log"><a href="<?php echo DC_ABSURL . '/changelog.txt?version=' . DC_VERSION; ?>" target="_blank">View Change log</a></span></h2>
	<div class="dc_form_wrapper">
		<form method="post">
			<ul>
			<?php
			foreach($dc_options as $idx => $details) {
				echo '<li>';
				echo '<label>' . $details['label'] . '</label> <div class="description_wrapper">' . ((!empty($details['description'])) ? '<span class="description">' . $details['description'] . '</span>' : '') . (($details['has_shortcode'] === true) ? '<span class="dc_shortcode_info">[dc_content name="' . $idx . '"]</span>' : '') . '</div>';
				if($details['type'] == 'wp-editor') {
					wp_editor(
						stripcslashes($details['data_array']['value']),
						$idx,
						array('textarea_name' => 'data[' . $idx . ']')
					);
				} else {
					echo $this->_formatElement($idx, $details['type'], $dc_settings[$idx], $details['data_array']);
				}
				echo '</li>';
			}
			?>
			</ul>
			<hr>
			<input class="button button-primary" type="submit" name="dc_settings_save" value="Save Settings" />
		</form>
	</div>
</div>