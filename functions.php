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

	// お知らせ
	$labels = array(
		'name'		=> 'お知らせ',
		'all_items'	=> 'お知らせの一覧',
		);

	$args = array(
		'labels'		=> $labels,
		'supports'		=> array( 'title','editor', 'thumbnail' ),
		'public'		=> true,	// 公開するかどうが
		'show_ui'		=> true,	// メニューに表示するかどうか
		'menu_position'	=> 5,		// メニューの表示位置
		'has_archive'	=> true,	// アーカイブページの作成
		);

	register_post_type( 'news', $args );

	// 収穫野菜
	$labels = array(
		'name'		=> '農園でとれる野菜',
		'all_items'	=> '農園でとれる野菜の一覧',
		);

	$args = array(
		'labels'		=> $labels,
		'supports'		=> array( 'title','editor', 'thumbnail', 'custom-fields' ),
		'public'		=> true,	// 公開するかどうが
		'show_ui'		=> true,	// メニューに表示するかどうか
		'menu_position'	=> 5,		// メニューの表示位置
		'has_archive'	=> true,	// アーカイブページの作成
		);

	register_post_type( 'vegetables', $args );
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

	if ( is_page() || is_home() ) {
		wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp');
	}

	wp_enqueue_script( 'igarashi-nouen-infinitescroll', get_stylesheet_directory_uri() .'/js/jquery.infinitescroll.js', array( 'jquery' ), '2.1.0');
	wp_enqueue_script( 'igarashi-nouen', get_stylesheet_directory_uri() .'/js/script.js', array( 'jquery' , 'birdfield' ), '1.00');
}
add_action( 'wp_enqueue_scripts', 'igr_scripts' );

//////////////////////////////////////////////////////
// Shortcode Goole Maps
function igr_nouen_map ( $atts ) {

	$output = '<div id="map-canvas">地図はいります </div>';
	$output .= '<input type="hidden" id="map_icon" value="' .get_stylesheet_directory_uri() .'/images/icon_map.png">';
	return $output;
}
add_shortcode( 'igarashi_nouen_map', 'igr_nouen_map' );

//////////////////////////////////////////////////////
// Shortcode Vegitables
function igr_nouen_vegetables_calendar ( $atts ) {

	$post_type = get_post_type_object( 'vegetables' );

	$html = '<h2>野菜収穫カレンダー</h2>';
	$html .= '<table class="vegetables-calendar"><tbody><tr><th class="title"><em>野菜の名前</em></th><th class="data"><span>1月</span><span>2月</span><span>3月</span><span>4月</span><span>5月</span><span>6月</span><span>7月</span><span>8月</span><span>9月</span><span>10月</span><span>11月</span><span>12月</span></th></tr>';

	$args = array(
		'posts_per_page' => -1,
		'post_type' => 'vegetables',
		'post_status' => 'publish'
	);
	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();

		$selected = get_field( 'calendar' );

		// 収穫カレンダー
		$html .= '<tr>';
		$html .= '<td class="title"><a href="' .get_permalink() .'">' .get_the_title() .'</a></td>';
		$html .= '<td class="data">';
		for( $i = 1; $i <= 12; $i++ ){
			if( in_array( $i, $selected) ) {
				$html .= '<span class="best">' .$i .'</span>';
			}
			else{
				$html .= '<span>' .$i .'</span>';
			}
		}

		$html .= '</td>';
		$html .= '</tr>';

		endwhile;
	endif;

	wp_reset_postdata();

	$html .= '</tbody></table>';

	return $html;
}
add_shortcode( 'igarashi_nouen_vegetables_calendar', 'igr_nouen_vegetables_calendar' );
