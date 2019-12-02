<?php
/*
This is our CoverPic template file
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

//Get predefined object data
$object_meta = $this->get_predefined_metabox_object( get_queried_object() );

//Get the metadata values
$dc_coverpic_settings = $object_meta->metadata[$object_meta->dc_coverpic_settings][0];

if($dc_coverpic_settings == 'on'):

//Continue getting other metadata values
$site_url = esc_url(get_site_url());
$dc_background_img = $object_meta->metadata[$object_meta->dc_background_img][0];
$dc_background_img_alt_tag = $object_meta->metadata[$object_meta->dc_background_img_alt_tag][0];
$dc_background_color = $object_meta->metadata[$object_meta->dc_background_color][0];
$dc_background_opacity = $object_meta->metadata[$object_meta->dc_background_opacity][0];
$dc_background_height = $object_meta->metadata[$object_meta->dc_background_height][0];
$dc_hide_title = $object_meta->metadata[$object_meta->dc_hide_title_name][0];
$dc_title = $object_meta->metadata[$object_meta->dc_title_name][0];
$dc_title_pos = $object_meta->metadata[$object_meta->dc_title_pos_name][0];
$dc_title_font_weight = $object_meta->metadata[$object_meta->dc_title_font_weight_name][0];
$dc_title_font_size = $object_meta->metadata[$object_meta->dc_title_font_size_name][0];
$dc_title_font_color = $object_meta->metadata[$object_meta->dc_title_font_color_name][0];
$dc_hide_sub_title = $object_meta->metadata[$object_meta->dc_hide_sub_title_name][0];
$dc_sub_title = $object_meta->metadata[$object_meta->dc_sub_title_name][0];
$dc_sub_title_font_weight = $object_meta->metadata[$object_meta->dc_sub_title_font_weight_name][0];
$dc_sub_title_font_size = $object_meta->metadata[$object_meta->dc_sub_title_font_size_name][0];
$dc_sub_title_font_color = $object_meta->metadata[$object_meta->dc_sub_title_font_color_name][0];

//Define text positioning
$dc_title_pos_flex = null;
switch($dc_title_pos){
	case 'center_center':
		$dc_title_pos_flex = 'align-items: center; text-align: center;';
		break;
	case 'top_center':
		$dc_title_pos_flex = 'align-items: flex-start; text-align: center;';
		break;
	case 'bottom_center':
		$dc_title_pos_flex = 'align-items: flex-end; text-align: center;';
		break;
	case 'top_right':
		$dc_title_pos_flex = 'align-items: flex-start; text-align: right;';
		break;
	case 'top_left':
		$dc_title_pos_flex = 'align-items: flex-start; text-align: left;';
		break;
	case 'left_center':
		$dc_title_pos_flex = 'align-items: center; text-align: left;';
		break;
	case 'right_center':
		$dc_title_pos_flex = 'align-items: center; text-align: right;';
		break;
	case 'bottom_left':
		$dc_title_pos_flex = 'align-items: flex-end; text-align: left;';
		break;
	default:
		$dc_title_pos_flex = 'align-items: flex-end; text-align: right;';
}
?>

<style>
#dc_coverpic_image {
	background: url('<?php echo esc_url($site_url . $dc_background_img); ?>');
}
#dc_coverpic_image, #dc_coverpic_text_wrapper, #dc_coverpic_text_area_flex {
	height: <?php echo intval($dc_background_height) * 10; ?>px;
}
#dc_coverpic_text_wrapper {
	background: <?php echo $this->hexToRgb('#' . $dc_background_color, $dc_background_opacity); ?>;
}
#dc_coverpic_text_area_flex {
	<?php echo $dc_title_pos_flex; ?>
}
#dc_text_box h1 {
	<?php
	echo 'font-size: ' . $dc_title_font_size . 'px;';
	echo 'font-weight: ' . $dc_title_font_weight . ';';
	echo 'color: #' . $dc_title_font_color . ';';
	?>
}
#dc_text_box h2 {
	<?php
	echo 'font-size: ' . $dc_sub_title_font_size . 'px;';
	echo 'font-weight: ' . $dc_sub_title_font_weight . ';';
	echo 'color: #' . $dc_sub_title_font_color . ';';
	?>
}
@media (max-width: 601px){
	#dc_text_box h1 {
		<?php
		if(intval($dc_title_font_size) >= 50){
			echo 'font-size: ' . intval($dc_title_font_size) / 1.8 . 'px;';
		}
		?>
	}
	#dc_text_box h2 {
		<?php
		if(intval($dc_sub_title_font_size) >= 30){
			echo 'font-size: ' . intval($dc_sub_title_font_size) / 1.8 . 'px;';
		}
		?>
	}
}
</style>
<div id="dc_coverpic_wrapper" class="dc_coverpic_wrapper">
	<div id="dc_coverpic_image"
		<?php if(!empty($dc_background_img_alt_tag)): ?>
		role="img" aria-label="<?php echo $dc_background_img_alt_tag; ?>"
		<?php endif; ?>
	>
	</div>
	<div id="dc_coverpic_text_wrapper" class="dc_coverpic_text_wrapper">
		<div class="container">
			<div id="dc_coverpic_text_area_flex" class="dc_coverpic_text_area_flex">
				<div id="dc_text_box" class="dc_text_box">
					<?php if($dc_hide_title != 'on'): ?>
					<h1><?php echo htmlspecialchars_decode($dc_title); ?></h1>
					<?php endif; ?>
					<?php if($dc_hide_sub_title != 'on'): ?>
					<h2><?php echo htmlspecialchars_decode($dc_sub_title); ?></h2>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>