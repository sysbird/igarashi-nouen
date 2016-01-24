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
	.home #content h2 a {
		color: <?php echo $header_color;?> !important;
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
// Custom Post Type for News
function create_post_type_news() {
	$labels = array(
		'name'		=> 'お知らせ',
		'all_items'	=> 'お知らせの一覧',
		);

	$args = array(
		'labels'			=> $labels,
		'supports'		=> array( 'title','editor', 'excerpt', 'thumbnail' ),
		'public'			=> true,	// 公開するかどうが
		'show_ui'		=> true,	// メニューに表示するかどうか
		'menu_position'	=> 5,		// メニューの表示位置
		'has_archive'		=> true,	// アーカイブページの作成
		);

	register_post_type( 'news', $args );
}
add_action( 'init', 'create_post_type_news', 0 );

//////////////////////////////////////////////////////
// Filter main query at home
function igr_home_query( $query ) {
 	if ( $query->is_home() && $query->is_main_query() ) {
		$query->set( 'post_type', 'news' );
		$query->set( 'posts_per_page', 3 );
	}
}
add_action( 'pre_get_posts', 'igr_home_query' );

//////////////////////////////////////////////////////
// Enqueue Scripts
function igr_scripts() {

	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}
add_action( 'wp_enqueue_scripts', 'igr_scripts' );
