<?php
/*
This is our main DC_Admin_Menus class
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

class DC_Admin_Menus extends DC_Main {
	
	//Main construct for admin menu pages
	protected function __construct(){
		
		//List for action hooks
		add_action('admin_menu', array($this, 'child_submenu'), 11);
		
	}
	
	//Main child theme admin menu settings
    final public function child_submenu(){
        add_submenu_page(
			'themes.php',
			'Child Theme Options',
			'Child Theme Options',
			'administrator',
			'child-theme-options',
			function(){
				include_once DC_ABSPATH . '/admin/dc-settings.php';
			}
		);
    }
	
	//Method to get the HTML field elements
    final public function _formatElement($name, $type, $value = '', $data_array = array()){
		if(!empty($value)){
			$value = stripcslashes($value);
		}
		if(!empty($type)){
			switch($type) {
				case 'textarea':
					return '<textarea id="' . $name . '" name="data[' . $name . ']">' . $value . '</textarea>';
				case 'text':
					return '<input id="' . $name . '" type="text" name="data[' . $name . ']" value="' . $value . '">';
				case 'checkbox':
					return '<input id="' . $name . '" type="checkbox" name="data[' . $name . ']" ' . (($value == 'on') ? 'checked="checked"' : '') . '">' . (($value == 'on') ? '<span class="enabled">Enabled</span>' : '<span class="disabled">Disabled</span>');
				case 'select':
					ob_start();
					echo '<select id="' . $name . '" name="data[' . $name . ']">';
					if(!empty($data_array)){
						foreach($data_array as $item){
							echo '<option value="' . $item['option'] . '" ' . (($item['option'] == $value) ? 'selected' : '') . '>' . $item['value'] . '</option>';
						}
					}
					echo '</select>';
					return ob_get_clean();
			}
		}
    }
	
}
new DC_Admin_Menus;