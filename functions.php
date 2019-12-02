<?php
/*
Note:
1. If you wish to use a method inside the DC_main class,
simply do it like this DC()->this_is_a_method()

2. Declared constants:
- DC_ABSURL
- DC_ABSPATH
- DC_VERSION
*/

//Quick security check. Exit on direct file access
defined('ABSPATH') or exit;

//Check if the class exists
if(!class_exists('DC_Main')){
	include_once get_stylesheet_directory() . '/class/class-dc-main.php';
}

//Instantiate the DC_Main class
function DC(){
	//Attach the version number as well for the current Child Theme version
	return DC_Main::instance('1.2.0');
}
DC();

/*
#################
#################

WARNING: PLEASE READ!!!
----------------------------
Do not edit or add anything in here.
If you wish to add your customizations,
look for the overrides.php file
in the same directory as this functions.php file
and code it from there.

#################
#################
*/