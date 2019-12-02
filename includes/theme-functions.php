<?php
/*
Codes below are intended for both child theme functions and Divi core function overrides.
*/

//Exit on unecessary access
defined('ABSPATH') or exit;

/*
Begin custom post meta (For Microformats mark-up)
Note: This is an override to the parent Divi core function
*/
function et_pb_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
	$postinfo_meta = '';

	if ( in_array( 'author', $postinfo ) )
		$postinfo_meta .= ' ' . esc_html__( 'by', 'et_builder' ) . ' <span class="author vcard"><span class="fn">' . et_pb_get_the_author_posts_link() . '</span></span>';

	if ( in_array( 'date', $postinfo ) ) {
		if ( in_array( 'author', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= '<span class="published">' . esc_html( get_the_time( wp_unslash( $date_format ) ) ) . '</span><span style="display: none;" class="updated">' . get_the_modified_time('F jS, Y') . '</span>';
	}

	if ( in_array( 'categories', $postinfo ) ) {
		$categories_list = get_the_category_list(', ');
		
		// do not output anything if no categories retrieved
		if ( '' !== $categories_list ) {
			if ( in_array( 'author', $postinfo ) || in_array( 'date', $postinfo ) )	$postinfo_meta .= ' | ';

			$postinfo_meta .= $categories_list;
		}
	}

	if ( in_array( 'comments', $postinfo ) ){
		if ( in_array( 'author', $postinfo ) || in_array( 'date', $postinfo ) || in_array( 'categories', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= et_pb_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );
	}

	return $postinfo_meta;
}

/*
Begin custom Footer Copyright content
Note: This is an override to the parent Divi core function
*/
function et_get_footer_credits(){
	//Get Privacy Policy link
	$privacy_policy = null;
	$inline_script = null;
	if(!empty(DC()->dc_options['privacy-policy'])){
		$privacy_policy = get_permalink(intval(DC()->dc_options['privacy-policy']));
		$inline_script = '
			<script>
				var font = jQuery("#footer-info a").css("font"),
					color = jQuery("#footer-info a").css("color");
				jQuery("#dc_privacy_policy a").css("font", font);
				jQuery("#dc_privacy_policy a").css("color", color);
			</script>
		';
	}
	return ((!empty($privacy_policy)) ? '<div id="dc_privacy_policy"><a href="' . esc_url($privacy_policy) . '">Privacy Policy</a></div>' : '') . '
		<p id="footer-info">' . stripcslashes(DC()->dc_options['copyright']) . '</p>
	' . ((!empty($privacy_policy) ? $inline_script : ''));
}