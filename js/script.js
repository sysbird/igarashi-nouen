jQuery(function() {

	if( jQuery( '#map-canvas').length ){
		google.maps.event.addDomListener(window, 'load',  igr_google_maps);
	}

	igr_AdjustHeader();

	jQuery( window ).scroll(function () {
		if ( jQuery( this ).scrollTop() > 200 ) {
			jQuery('#header').addClass('thin');
		}
		else {
			jQuery('#header').removeClass('thin');
		}

		igr_AdjustHeader();
	});
});

////////////////////////////////////////
// Adjust parallax
function igr_AdjustHeader() {

	if('absolute' == jQuery('.headerimage').css('position')){
		var scrollTop = parseInt( jQuery( this ).scrollTop() );
		var top = parseInt( jQuery( '.headerimage' ).css('top') );
		jQuery( '.headerimage' ).css( 'top', (top -80) + 'px' );
	}
}

////////////////////////////////////////
// Google Maps for access
function igr_google_maps() {

	var latlng = new google.maps.LatLng( 35.764384, 139.615194 );

	var mapOptions = {
		zoom: 16,
		center: latlng,
		scrollwheel: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		scaleControl: true,
		scaleControlOptions: {
			position: google.maps.ControlPosition.BOTTOM_LEFT
		},
		mapTypeControlOptions: {
			mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'm_map']
		}
	}
	var map = new google.maps.Map( document.getElementById('map-canvas'), mapOptions );

	var igr_mapstyle = [
		{
			"stylers": [
				{ "lightness": 15 },
				{ "gamma": 0.8 },
				{ "hue": "#f1efe9" }
				]
		}
	];

	var m_mapstyleOptions = {
		name: "漢字五文字"
	};

	var m_mapType = new google.maps.StyledMapType( igr_mapstyle, m_mapstyleOptions );
	map.mapTypes.set('m_map', m_mapType);
	map.setMapTypeId('m_map');

	var image = '../images/icon_map.png';
	var igrMarker = new google.maps.Marker({
		position: latlng,
		map: map,
		icon: image
	});

    new google.maps.InfoWindow({
        content: '漢字五文字<a href="https://goo.gl/maps/oJ8dhHpiiB22" style="display :block;padding-top: 5px; font-size: 0.9em;">地図を拡大表示</a>'
    }).open(igrMarker.getMap(), igrMarker);

}
