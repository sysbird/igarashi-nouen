jQuery(function() {

	jQuery('.tile .type-vegetables a').attr( 'href', '#vegetables_boxer' );
	jQuery('.type-vegetables a').boxer( {
//		mobile: boxer_mobile,
		callback: function(data){
			console.log(data);
		}
	} );

jQuery(window).on("open.boxer", function(e) {
}).on("close.boxer", function() {
});
	jQuery('.tile a').live('click',function(){
console.log("ckick");


		// vagetables card
/*
		var id  = jQuery(this).parents( '.type-vegetables' ).attr('id');
		id = id.replace( 'post-', '' );
		var url = '/wp-json/get_vegetables/' + id + '?_jsonp=?';
		jQuery.ajax({
			type: 'GET',
			url: url,
			dataType: 'jsonp'
			}).done(function(data, status, xhr) {

				// Show


			}).fail(function(xhr, status, error) {
		});
*/
		return false;
});

	// infinitescroll for all vegetables
	jQuery( window ).load(function() {

		jQuery( "ul.tile li" ).tile();

		if( 0 < jQuery( '#all-vegetables' ).length ){
			path = '';
			if ( jQuery( '#all-vegetables .rewrite_url' ).length ){
				// using_permalinks
				path=new Array();
				path.push( location.href+'?infinite_timeline_next=' );
				path.push( "" );
			}

			// infinitescroll
			var loading = jQuery( '#all-vegetables img.loading' ).attr( 'src' );
			jQuery( '#all-vegetables' ).infinitescroll( {
				navSelector : "#all-vegetables .pagenation",
				nextSelector : "#all-vegetables .pagenation a",
				itemSelector : "#all-vegetables .box",
				loading: {
					img: loading,
				},
				path : path
			},
			function( newElements ){
			} );
		}
	} );

	// Google Maps
	if( jQuery( '#map-canvas').length ){
		google.maps.event.addDomListener(window, 'load',  igr_google_maps);
	}

	// thin header for scroll
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
		jQuery( '.headerimage' ).css( 'top', (top -20) + 'px' );
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

	// map style gray
	 var igr_mapstyle = [
	    {
	        "featureType": "landscape",
	        "stylers": [
	            {
	                "saturation": -100
	            },
	            {
	                "lightness": 60
	            }
	        ]
	    },
	    {
	        "featureType": "road.local",
	        "stylers": [
	            {
	                "saturation": -100
	            },
	            {
	                "lightness": 40
	            },
	            {
	                "visibility": "on"
	            }
	        ]
	    },
	    {
	        "featureType": "transit",
	        "stylers": [
	            {
	                "saturation": -100
	            },
	            {
	                "visibility": "simplified"
	            }
	        ]
	    },
	    {
	        "featureType": "administrative.province",
	        "stylers": [
	            {
	                "visibility": "off"
	            }
	        ]
	    },
	    {
	        "featureType": "water",
	        "stylers": [
	            {
	                "visibility": "on"
	            },
	            {
	                "lightness": 30
	            }
	        ]
	    },
	    {
	        "featureType": "road.highway",
	        "elementType": "geometry.fill",
	        "stylers": [
	            {
	                "color": "#ef8c25"
	            },
	            {
	                "lightness": 40
	            }
	        ]
	    },
	    {
	        "featureType": "road.highway",
	        "elementType": "geometry.stroke",
	        "stylers": [
	            {
	                "visibility": "off"
	            }
	        ]
	    },
	    {
	        "featureType": "poi.park",
	        "elementType": "geometry.fill",
	        "stylers": [
	            {
	                "color": "#b6c54c"
	            },
	            {
	                "lightness": 40
	            },
	            {
	                "saturation": -40
	            }
	        ]
	    },
	    {}
	  ];

	var m_mapType = new google.maps.StyledMapType( igr_mapstyle, {name: "五十嵐農園"} );
	map.mapTypes.set('m_map', m_mapType);
	map.setMapTypeId('m_map');

	var map_icon = jQuery( '#map_icon' ).val();
	var igrMarker = new google.maps.Marker({
		position: latlng,
		map: map,
		icon: map_icon
	});

    new google.maps.InfoWindow({
        content: '五十嵐農園<a href="https://goo.gl/maps/oJ8dhHpiiB22" style="display :block;padding-top: 5px; font-size: 0.9em;">地図を拡大表示</a>'
    }).open(igrMarker.getMap(), igrMarker);

}
