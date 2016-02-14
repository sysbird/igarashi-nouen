<?php
add_filter( 'comments_open', '__return_false' );

//////////////////////////////////////////
// Add tag setting at page
function birdfield_child_add_tag_to_page() {
  register_taxonomy_for_object_type('post_tag', 'page'); }
add_action('init', 'birdfield_child_add_tag_to_page');

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
 		// toppage news
		$query->set( 'post_type', 'news' );
		$query->set( 'posts_per_page', 3 );
	}

	if ( $query->is_archive() && $query->is_main_query() ) {
		if( 'vegetables' ===  $query->get( 'post_type' )){
			// vegetables type
			$type = get_query_var('type') ;
			if( !empty( $type )){
				$query->set('meta_query',
				array(
				array(
				'key' => 'type',
				'value' => $type,
				'compare' => 'LIKE' )));
				$query->set( 'posts_per_page', -1 );
			}

			// vegetables season
			$season = get_query_var('season') ;
			if( !empty( $season )){
				$query->set('meta_query',
				array(
				array(
				'key' => 'season',
				'value' => $season,
				'compare' => 'LIKE' )));
				$query->set( 'posts_per_page', -1 );
			}
		}
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
// Shortcode Vegitables Calendar
function igr_nouen_vegetables_calendar ( $atts ) {

	$html_table_header = '<table class="vegetables-calendar"><tbody><tr><th class="title">&nbsp;</th><th class="data"><span>1月</span><span>2月</span><span>3月</span><span>4月</span><span>5月</span><span>6月</span><span>7月</span><span>8月</span><span>9月</span><span>10月</span><span>11月</span><span>12月</span></th></tr>';
	$html_table_footer = '</tbody></table>';
	$html = '';

	$args = array(
		'posts_per_page' => -1,
		'post_type' => 'vegetables',
		'post_status' => 'publish',
		'meta_key'	=> 'type',
		'orderby'	 => 'meta_value',
	);

	$the_query = new WP_Query($args);
	$type_current = '';
	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();

		$type = get_field( 'type' );
		if( $type && ( $type != $type_current ) ){
			if( !empty( $html )){
				$html .= $html_table_footer;
			}

			$html .= '<div class="vegetables-meta">' .igarashi_nouen_get_type_label( $type ) .'</div>';
			$type_current = $type;
			$html .= $html_table_header;
		}

		// 収穫カレンダー
		$selected = get_field( 'calendar' );
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

	if( !empty( $html )){
		$html .= $html_table_footer;
	}
	$html = '<h2>野菜収穫カレンダー</h2>' .$html;

	return $html;
}
add_shortcode( 'igarashi_nouen_vegetables_calendar', 'igr_nouen_vegetables_calendar' );

//////////////////////////////////////////////////////
// Shortcode Vegitables Pickup at home
function igr_nouen_vegetables_pickup ( $atts ) {

	$html = '';

	$args = array(
		'posts_per_page' => 6,
		'post_type' => 'vegetables',
		'post_status' => 'publish',
		'meta_key' => '_thumbnail_id',
		'orderby'	 => 'rand',
	);

	$the_query = new WP_Query($args);
	$type_current = '';
	if ( $the_query->have_posts() ) :

		$html .= '<ul class="tile">';
		while ( $the_query->have_posts() ) : $the_query->the_post();

			$html .= '<li class="hentry"><a href="' .get_permalink() .'">' ;

			if( has_post_thumbnail() ):
				$html .= '<div class="entry-eyecatch">' .get_the_post_thumbnail(  get_the_ID(), 'large' ) .'</div>';
			endif;

			$html .= '<header class="entry-header"><h3 class="entry-title">'  .get_the_title() .'</h3></header>';
			$html .= '</a></li>';

		endwhile;
		$html .= '</ul>';
	endif;
	wp_reset_postdata();

	return $html;
}
add_shortcode( 'igarashi_nouen_vegetables_pickup', 'igr_nouen_vegetables_pickup' );

//////////////////////////////////////////////////////
// Display the Featured Image at vegetable page
function igarashi_nouen_post_image_html( $html, $post_id, $post_image_id ) {

	if( !( false === strpos( $html, 'anchor' ) ) ){
		$html = '<a href="' .get_permalink() .'" class="thumbnail">' .$html .'</a>';
	}

	return $html;
}
add_filter( 'post_thumbnail_html', 'igarashi_nouen_post_image_html', 10, 3 );

/////////////////////////////////////////////////////
// get type label in vegetables
function igarashi_nouen_get_type_label( $value, $anchor = TRUE ) {
	$label ='';
	$fields = get_field_object( 'type' );
	$url = get_post_type_archive_link( 'vegetables' );

	if( array_key_exists( 'choices' , $fields ) ){
		$label .= '<span>';
		if( $anchor ){
			$label .= '<a href="' .$url .'/type/' .$value .'">';
		}
		$label .= $fields[ 'choices' ][ $value ];
		if( $anchor ){
			$label .= '</a>';
		}
		$label .= '</span>';
	}

	return $label;
}

/////////////////////////////////////////////////////
// get season label in vegetables
function igarashi_nouen_get_season_label( $value, $anchor = TRUE ) {
	$label ='';
	$fields = get_field_object( 'season' );
	$url = get_post_type_archive_link( 'vegetables' );

	if( is_array($value)){
		foreach ( $value as $key => $v ) {
			if( array_key_exists( 'choices', $fields) ) {
				$label .= '<span>';
				if( $anchor ){
					$label .= '<a href="' .$url .'/season/' .$v .'">';
				}
				$label .= ( $fields[ 'choices' ][ $v ] );
				if( $anchor ){
					$label .= '</a>';
				}
				$label .= '</span>';
			}
		}
	}
	else{
		if( array_key_exists( 'choices', $fields) ) {
			$label .= '<span>'. $fields[ 'choices' ][ $value ] .'</span>';
		}
	}

	return $label;
}

/////////////////////////////////////////////////////
// add permalink rule for vegetables
function myRewriteRule(){
	add_rewrite_rule('vegetables/type/([a-zA-Z_]+)/?$' ,'index.php?post_type=vegetables&type=$matches[1]', 'top');
	add_rewrite_rule('vegetables/season/([a-zA-Z_]+)/?$' ,'index.php?post_type=vegetables&season=$matches[1]', 'top');
}
add_action( 'init', 'myRewriteRule' );
/////////////////////////////////////////////////////
// add permalink parameters for vegetables
function add_query_vars_filter( $vars ){
	$vars[] = "type";
	$vars[] = "season";
	return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );
