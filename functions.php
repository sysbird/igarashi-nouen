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
//		'name'		=> '農園でとれる野菜',
//		'all_items'	=> '農園でとれる野菜の一覧',
		'name'		=> '漢字',
		'all_items'	=> '漢字の一覧',
		);

	$args = array(
		'labels'		=> $labels,
		'supports'		=> array( 'title','editor', 'thumbnail' ),
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
function igr_nouen_vegetables ( $atts ) {

	$post_type = get_post_type_object( 'vegetables' );

	$calendar_html = '<h2>' .$post_type->labels->name .'カレンダー</h2>';
	$calendar_html .= '<table class="vegetables-calendar"><tbody><tr><th class="title"><em>漢字の名前<em></em></em></th><th class="data"><span>1月</span><span>2月</span><span>3月</span><span>4月</span><span>5月</span><span>6月</span><span>7月</span><span>8月</span><span>9月</span><span>10月</span><span>11月</span><span>12月</span></th></tr>';

	$vegetables_html ='<h2>' .$post_type->labels->name .'</h2>';
	$vegetables_html  .= '<ul class="vegetables-list">';

	$args = array(
		'posts_per_page' => 3,
		'post_type' => 'vegetables',
		'post_status' => 'publish'
	);
	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();

		// 収穫カレンダー
		$calendar_html .= '<tr>';
		$calendar_html .= '<td class="title">タイトル</td>';
		$calendar_html .= '<td class="data">';
		$calendar_html .= '<span>1</span>';
		$calendar_html .= '<span>2</span>';
		$calendar_html .= '<span class="good">3</span>';
		$calendar_html .= '<span class="best">4</span>';
		$calendar_html .= '<span class="best">5</span>';
		$calendar_html .= '<span class="best">6</span>';
		$calendar_html .= '<span class="good">7</span>';
		$calendar_html .= '<span>8</span>';
		$calendar_html .= '<span>9</span>';
		$calendar_html .= '<span>10</span>';
		$calendar_html .= '<span>11</span>';
		$calendar_html .= '<span>12</span>';
		$calendar_html .= '</td>';
		$calendar_html .= '</tr>';

		// 野菜について
		$vegetables_html .= '<li>';
		$vegetables_html .= '<h3><a href="' .get_permalink() .'">' .get_the_title() .'</a></h3>';
		$vegetables_html .= get_the_content();

		$vegetables_html  .= '</li>';

		endwhile;
	endif;

	wp_reset_postdata();

	$calendar_html .= '</tbody></table>';

	$link = get_post_type_archive_link( 'vegetables');
	$vegetables_html .='</ul><div class="more"><a href="' .$link .'">' .$post_type->labels->name .'をもっと見る</a></div>';
	$output = $calendar_html  .$vegetables_html;

	return $output;
}
add_shortcode( 'igarashi_nouen_vegetables', 'igr_nouen_vegetables' );
