<?php
//Quick security check. Exit on direct file access
defined('ABSPATH') or exit;

require_once( get_template_directory() . esc_attr( "/options_divi.php" ) );

$epanel_key = "name";
$epanel_value_1 = "Show Google+ Icon";
$epanel_value_2 = "Google+ Profile Url";

//For swatches
$custom_options_1 = array(
	array( "name" =>esc_html__( "Show Pinterest Icon", $themename ),
		   "id" => $shortname . "_show_pinterest_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Pinterest Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show LinkedIn Icon", $themename ),
		   "id" => $shortname . "_show_linkedin_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the LinkedIn Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show Tumblr Icon", $themename ),
		   "id" => $shortname . "_show_tumblr_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Tumblr Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show Instagram Icon", $themename ),
		   "id" => $shortname . "_show_instagram_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Instagram Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show Skype Icon", $themename ),
		   "id" => $shortname . "_show_skype_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Skype Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show Flikr Icon", $themename ),
		   "id" => $shortname . "_show_flikr_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Flikr Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show Myspace Icon", $themename ),
		   "id" => $shortname . "_show_myspace_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Myspace Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show Dribbble Icon", $themename ),
		   "id" => $shortname . "_show_dribbble_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Dribbble Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show Youtube Icon", $themename ),
		   "id" => $shortname . "_show_youtube_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Youtube Icon on your homepage. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Show Vimeo Icon", $themename ),
		   "id" => $shortname . "_show_vimeo_icon",
		   "type" => "checkbox",
		   "std" => "on",
		   "desc" =>esc_html__( "Here you can choose to display the Vimeo Icon on your homepage. ", $themename ) )
		   
);

foreach( $options as $index => $value ) {
    if ( isset($value[$epanel_key]) && $value[$epanel_key] === $epanel_value_1 ) {
        foreach( $custom_options_1 as $custom_index => $custom_option ) {
            $options = insertArrayIndex($options, $custom_option, $index+$custom_index+1);
        }
        break;
    }
}

//For URLs
$custom_options_2 = array(

	array( "name" =>esc_html__( "Pinterest Url", $themename ),
		   "id" => $shortname . "_pinterest_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Pinterest Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Linkedin Url", $themename ),
		   "id" => $shortname . "_linkedin_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Linkedin Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Tumblr Url", $themename ),
		   "id" => $shortname . "_tumblr_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Linkedin Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Instagram Url", $themename ),
		   "id" => $shortname . "_instagram_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Instagram Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Skype Url", $themename ),
		   "id" => $shortname . "_skype_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Skype Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Flikr Url", $themename ),
		   "id" => $shortname . "_flikr_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Flikr Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Myspace Url", $themename ),
		   "id" => $shortname . "_myspace_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Myspace Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Dribbble Url", $themename ),
		   "id" => $shortname . "_dribbble_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Dribbble Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Youtube Url", $themename ),
		   "id" => $shortname . "_youtube_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Youtube Profile. ", $themename ) ),
		   
	array( "name" =>esc_html__( "Vimeo Url", $themename ),
		   "id" => $shortname . "_vimeo_url",
		   "std" => "#",
		   "type" => "text",
		   "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Vimeo Profile. ", $themename ) )
		   
);

foreach( $options as $index => $value ) {
    if ( isset($value[$epanel_key]) && $value[$epanel_key] === $epanel_value_2 ) {
        foreach( $custom_options_2 as $custom_index => $custom_option ) {
            $options = insertArrayIndex($options, $custom_option, $index+$custom_index+1);
        }
        break;
    }
}

function insertArrayIndex($array, $new_element, $index) {
	$start = array_slice($array, 0, $index);
	$end = array_slice($array, $index);
	$start[] = $new_element;
	return array_merge($start, $end);
}

return $options;