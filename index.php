<?php
/*
Divi Template base version: 3.12.2
*/
get_header(); ?>

<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">
		<?php
			if ( have_posts() ) :
				while ( have_posts() ) : the_post();
					$post_format = et_pb_post_format(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>
					
				<?php
					et_divi_post_format_content();

					if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) {
						if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) :
							printf(
								'<div class="et_main_video_container">
									%1$s
								</div>',
								$first_video
							);
						elseif (
							! in_array( $post_format, array( 'gallery' ) ) &&
							'on' === et_get_option( 'divi_thumbnails_index', 'on' ) &&
							has_post_thumbnail()
						) :
							?>
							<div class="dc_post_item_left">
								<a class="entry-featured-image-url" href="<?php the_permalink(); ?>">
									<?php
									the_post_thumbnail(
										'medium', array(
											'title' => get_the_title()
										)
									);
									?>
								</a>
							</div>
					<?php
						elseif ( 'gallery' === $post_format ) :
							et_pb_gallery_images();
						endif;
					} ?>

				<?php if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) : ?>
					<div class="dc_post_item_right">
					<?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) ) : ?>
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php endif; ?>

					<?php
						et_divi_post_meta();

						if ( 'on' !== et_get_option( 'divi_blog_style', 'false' ) || ( is_search() && ( 'on' === get_post_meta( get_the_ID(), '_et_pb_use_builder', true ) ) ) ) {
							/*
							Usage:
							truncate_post( $amount, $echo = true, $post = '', $strip_shortcodes = false )
							*/
							if(DC()->dc_options['show-archive-readmore'] != 'on'){
								truncate_post(270);
							} else {
								truncate_post(150);
								echo '
									<div class="dc_readmore_archive">
										<a href="' . get_the_permalink() . '" class="et_pb_button">Read More</a>
									</div>'
								;
							}
						} else {
							the_content();
						}
					?>
					</div>
				<?php endif; ?>
				
					</article> <!-- .et_pb_post -->
			<?php
					endwhile;

					if ( function_exists( 'wp_pagenavi' ) )
						wp_pagenavi();
					else
						get_template_part( 'includes/navigation', 'index' );
				else :
					get_template_part( 'includes/no-results', 'index' );
				endif;
			?>
			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php

get_footer();
