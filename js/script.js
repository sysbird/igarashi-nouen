jQuery(function() {

	// vagetables card
/*
	jQuery('.tile .type-vegetables a').live("click",function(){
		var id  = jQuery(this).parents( '.type-vegetables' ).attr('id');
		id = id.replace( 'post-', '' );
		var url = '/wp-json/get_vegetables/' + id + '?_jsonp=?';
		jQuery.ajax({
			type: 'GET',
			url: url,
			dataType: 'jsonp'
			}).done(function(data, status, xhr) {

				// popup
				jQuery.magnificPopup.open({
					items: {
						src: '<div class="vegetables_card"><div class="entry-title">' + data.title + '</div> ' + data.content +'</div>',
						type: 'inline'
					}
				});

			}).fail(function(xhr, status, error) {
		});

		return false;
	});
*/

	// infinitescroll for vegetables masonry
	if( 0 < jQuery( '.more.pagenation' ).length ){
		// infinitescroll
		var loading = jQuery( 'img.loading' ).attr( 'src' );
		jQuery( '.tile' ).infinitescroll( {
			navSelector : ".more.pagenation",
			nextSelector : ".more.pagenation a",
			itemSelector : ".tile .type-vegetables",
			bufferPx: 800,
			loading: {
				finishedMsg: '',
				msgText  : '読み込み中...',
				img: loading,
			}
		},
		function( newElements ){
			var newElems = jQuery( newElements ).css({ opacity: 0 });
			newElems.imagesLoaded(function(){
				newElems.animate({ opacity: 1 });
				jQuery( '.tile.masonry' ).masonry( 'appended', newElems, true );
			});
		} );
	}

	// Google Maps
	if( jQuery( '#map-canvas').length ){
		google.maps.event.addDomListener(window, 'load',  igr_google_maps);
	}

	jQuery( window ).load(function() {
		jQuery( ".home .tile .type-vegetables" ).tile();

		jQuery( '.tile.masonry ' ).masonry({
			itemSelector: '.type-vegetables',
			isAnimated: true
		});

		// for facebook
		jQuery( '#widget-area .container' ).masonry( 'destroy' );
		var widgetArea = jQuery( '#widget-area' ).height();
		var footerHeight = jQuery( '#footer .site-title' ).innerHeight();
		var height = parseInt( widgetArea ) + parseInt( footerHeight );
		jQuery('#content').css('padding-bottom', height + 'px' );
		jQuery('#footer').css('height', height + 'px' );

	} );
});

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
