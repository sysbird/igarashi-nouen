<?php
add_filter( 'comments_open', '__return_false' );

//////////////////////////////////////////
// Add tag setting at page
function birdfield_child_add_tag_to_page() {
  register_taxonomy_for_object_type('post_tag', 'page'); }
add_action('init', 'birdfield_child_add_tag_to_page');

//////////////////////////////////////////////////////
// CSS
function birdfield_child_css() {

	//Theme Option
	$header_color = esc_attr( get_theme_mod( 'birdfield_header_color', '#79a596' ) );
	$text_color = esc_attr( get_theme_mod( 'birdfield_text_color', '#222327' ) );
?>

<style type="text/css">
.home #content #blog ul.article .hentry .entry-header .entry-title,

	.home #content #blog ul.article .hentry .entry-header .entry-title,
	.home #content h2 a {
		color: <?php echo $header_color;?> !important;
		}

	.home #content #blog ul.article .hentry .entry-header p {
		color: <?php echo $text_color;?>;
	}

</style>

<?php

}
add_action( 'wp_head', 'birdfield_child_css' );

//////////////////////////////////////////////////////
// Setup Theme
function birdfield_child_setup() {

	register_default_headers( array(
		'birdfield_child'		=> array(
		'url'			=> '%2$s/images/header.jpg',
		'thumbnail_url'	=> '%2$s/images/header-thumbnail.jpg',
		'description_child'	=> 'birdfield'
		)
	) );
}
add_action( 'after_setup_theme', 'birdfield_child_setup' );

//////////////////////////////////////////////////////
// Shortcode
function birdfield_child_count( $atts ) {

	$atts = shortcode_atts( array( 'site_name'	=> 'toriko',
					'text' => '%s',
					'thumbnail' => 'yes',
					'display'	=> 'count' ),
					$atts );
	$output = '';

	if( is_multisite() ){
		$my_sites = wp_get_sites();
		foreach ( $my_sites as $my_site ):
			if( !( strpos( $my_site[ 'path' ], $atts[ 'site_name' ] ) === FALSE ) ){
				switch_to_blog( $my_site[ 'blog_id' ] );

				$thumbnail = '';

				$myposts = get_posts( array( 'posts_per_page' => 1 ) );
				foreach( $myposts as $post ):
					setup_postdata( $post );
					$output = sprintf( $atts[ 'text' ], $post->post_title );

					if( has_post_thumbnail( $post->ID ) && 'yes' == $atts[ 'thumbnail' ] ){
						$thumbnail = '<span class="thumbnail">' .get_the_post_thumbnail( $post->ID ) .'</span>';
					}
				endforeach;

				if( strpos(  $atts[ 'display' ] , 'title' ) === FALSE ){
					$output = sprintf( $atts[ 'text' ], wp_count_posts( 'post','publish' )->publish );
				}

				restore_current_blog();

				$output .=  $thumbnail;

				break;
			}
		endforeach;
	}

	return $output;
}
add_shortcode( 'birdfield_child_count', 'birdfield_child_count' );
