<?php
add_filter( 'comments_open', '__return_false' );

//////////////////////////////////////////////////////
// Setup Theme
function igarashi_nouen_setup() {

	register_default_headers( array(
		'birdfield_child'		=> array(
		'url'			=> '%2$s/images/header.jpg',
		'thumbnail_url'	=> '%2$s/images/header-thumbnail.jpg',
		'description_child'	=> 'birdfield'
		)
	) );
}
add_action( 'after_setup_theme', 'igarashi_nouen_setup' );

//////////////////////////////////////////////////////
// Child Theme Initialize
function igarashi_nouen_init() {

 	// add tags at page
	register_taxonomy_for_object_type('post_tag', 'page');
	// add post type news
	$labels = array(
		'name'		=> 'お知らせ',
		'all_items'	=> 'お知らせの一覧',
		);
	$args = array(
		'labels'			=> $labels,
		'supports'		=> array( 'title','editor', 'thumbnail' ),
		'public'			=> true,	// 公開するかどうが
		'show_ui'		=> true,	// メニューに表示するかどうか
		'menu_position'	=> 5,		// メニューの表示位置
		'has_archive'		=> true,	// アーカイブページの作成
		);
	register_post_type( 'news', $args );

	// add post type vegetables
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

	// add permalink rule for vegetables
	add_rewrite_rule('vegetables/type/([a-zA-Z_]+)/?$' ,'index.php?post_type=vegetables&type=$matches[1]', 'top');
	add_rewrite_rule('vegetables/season/([a-zA-Z_]+)/?$' ,'index.php?post_type=vegetables&season=$matches[1]', 'top');
}
add_action( 'init', 'igarashi_nouen_init', 0 );

//////////////////////////////////////////////////////
// Filter at main query
function igarashi_nouen_query( $query ) {

 	if ( $query->is_home() && $query->is_main_query() ) {
 		// toppage news
		$query->set( 'post_type', 'news' );
		$query->set( 'posts_per_page', 3 );
	}

	if ($query->is_main_query() && is_post_type_archive('vegetables')) {
		// vegetables
		$type = get_query_var('type') ;
		if( !empty( $type )){
			// vegetables type
			$query->set('meta_query',
				array(
					array(
					'key' => 'type',
					'value' => $type,
					'compare' => 'LIKE' )));
			$query->set( 'posts_per_page', -1 );
		}
		else {
			$season = get_query_var('season') ;
			if( !empty( $season )){
				// vegetables season
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
add_action( 'pre_get_posts', 'igarashi_nouen_query' );

//////////////////////////////////////////////////////
// Enqueue Scripts
function igarashi_nouen_scripts() {

	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
	wp_enqueue_style( 'igarashi-nouen-magnific-popup', get_stylesheet_directory_uri().'/js/Magnific-Popup/magnific-popup.css' );

	if ( is_page() || is_home() ) {
		wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp');
	}

	wp_enqueue_script( 'igarashi-nouen-infinitescroll', get_stylesheet_directory_uri() .'/js/jquery.infinitescroll.js', array( 'jquery' ), '1.1.0');
	wp_enqueue_script( 'igarashi-nouen-magnific-popup', get_stylesheet_directory_uri() .'/js/Magnific-Popup/jquery.magnific-popup.min.js', array( 'jquery' ), '3.3.0');
	wp_enqueue_script( 'igarashi-nouen', get_stylesheet_directory_uri() .'/js/script.js', array( 'jquery' , 'birdfield' ), '1.00');
}
add_action( 'wp_enqueue_scripts', 'igarashi_nouen_scripts' );

//////////////////////////////////////////////////////
// Shortcode Goole Maps
function igarashi_nouen_nouen_map ( $atts ) {

	$output = '<div id="map-canvas">地図はいります </div>';
	$output .= '<input type="hidden" id="map_icon_path" value="' .get_stylesheet_directory_uri() .'/images">';
	return $output;
}
add_shortcode( 'igarashi_nouen_map', 'igarashi_nouen_nouen_map' );

//////////////////////////////////////////////////////
// Shortcode Vegitables Calendar
function igarashi_nouen_vegetables_calendar ( $atts ) {

	extract( shortcode_atts( array(
		'title' => 'no'
		), $atts ) );

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

		wp_reset_postdata();
	endif;


	if( !empty( $html )){
		$html .= $html_table_footer;
	}

	if( 'yes' === $title ){
		$html = '<h2>野菜収穫カレンダー</h2>' .$html;
	}

	return $html;
}
add_shortcode( 'igarashi_nouen_vegetables_calendar', 'igarashi_nouen_vegetables_calendar' );

//////////////////////////////////////////////////////
// Shortcode Vegitables Pickup at home
function igarashi_nouen_vegetables_pickup ( $atts ) {

	ob_start();

	$args = array(
		'posts_per_page' => 6,
		'post_type' => 'vegetables',
		'post_status' => 'publish',
		'meta_key' => '_thumbnail_id',
		'orderby'	 => 'rand',
	);

	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) :
		?> <div class="tile"><?php

		while ( $the_query->have_posts() ) : $the_query->the_post();
			get_template_part( 'content', 'vegetables' );
		endwhile;

		?></div><?php

		wp_reset_postdata();
	endif;

	return ob_get_clean();
}
add_shortcode( 'igarashi_nouen_vegetables_pickup', 'igarashi_nouen_vegetables_pickup' );

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
			$label .= '<a href="' .$url .'type/' .$value .'">';
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
					$label .= '<a href="' .$url .'season/' .$v .'">';
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
// add permalink parameters for vegetables
function igarashi_nouen_query_vars( $vars ){
	$vars[] = "type";
	$vars[] = "season";
	return $vars;
}
add_filter( 'query_vars', 'igarashi_nouen_query_vars' );

/////////////////////////////////////////////////////
// Add WP REST API Endpoints
function igarashi_nouen_rest_api_init() {
	register_rest_route( 'get_vegetables', '/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'igarashi_nouen_get_vegetables',
		) );
}
add_action( 'rest_api_init', 'igarashi_nouen_rest_api_init' );

function igarashi_nouen_get_vegetables( $params ) {

	$find = FALSE;
	$id = 0;
	$title = '';
	$content = '';

	$args = array(
		'p'					=> $params['id'],
		'posts_per_page'	=> 1,
		'post_type'			=> 'vegetables',
		'post_status'		=> 'publish',
	);

	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) :
		$find = TRUE;
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$id = get_the_ID();
			$title = get_the_title( );
			$content = apply_filters('the_content', get_the_content() );
			break;
		endwhile;

		wp_reset_postdata();
	endif;

	if($find) {
		return new WP_REST_Response( array(
			'id'		=> $id,
			'title'		=> $title,
			'content'	=> $content,
		) );
	}
	else{
		$response = new WP_Error('error_code', 'Sorry, no posts matched your criteria.');
		return $response;
	}
}

/////////////////////////////////////////////////////
// show catchcopy at vegetables tile
function igarashi_nouen_get_catchcopy() {

	$catchcopy = get_field( 'catchcopy' );
	if( $catchcopy ){
		return '<p class="catchcopy">' .$catchcopy .'</p>';
	}

	return NULL;
}

//////////////////////////////////////////////////////
// login logo
function igarashi_nouen_login_head() {

	$url = get_stylesheet_directory_uri() .'/images/login.png';
	echo '<style type="text/css">.login h1 a { background-image:url(' .$url .'); height: 84px; width: 320px; background-size: 100% 100%;}</style>';
}
add_action('login_head', 'igarashi_nouen_login_head');

//////////////////////////////////////////////////////
// remove emoji
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles', 10 );

//////////////////////////////////////////////////////
// set favicon
function igarashi_nouen_favicon() {
	echo '<link rel="shortcut icon" type="image/x-icon" href="' .get_stylesheet_directory_uri(). '/images/favicon.ico" />'. "\n";
}
add_action( 'wp_head', 'igarashi_nouen_favicon' );