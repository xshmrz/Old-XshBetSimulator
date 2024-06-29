(function ($) {
    $.fn.googleMap                 = function (options) {
        var options    = $.extend({
                                      zoom              : 5,
                                      center            : [39, -98], // Varsayılan olarak Paris koordinatları
                                      mapType           : 'ROADMAP',
                                      language          : 'en',
                                      overviewMapControl: false,
                                      streetViewControl : false,
                                      scrollwheel       : true,
                                      mapTypeControl    : false,
                                      draggable         : true,
                                      // Varsayılan marker ikonu burada tanımlanıyor
                                      markerIcon: 'assets/vendor/map-pin-3.png'
                                  }, options);
        var mapOptions = {
            zoom              : options.zoom,
            center            : new google.maps.LatLng(options.center[0], options.center[1]),
            mapTypeId         : google.maps.MapTypeId[options.mapType],
            langage           : options.langage,
            overviewMapControl: options.overviewMapControl,
            streetViewControl : options.streetViewControl,
            scrollwheel       : options.scrollwheel,
            mapTypeControl    : options.mapTypeControl,
            draggable         : options.draggable
        };
        this.each(function () {
            var map = new google.maps.Map(this, mapOptions);
            $(this).data('map', map);
            $(this).data('markers', []);
            // Varsayılan marker ikonunu harita nesnesine sakla
            $(this).data('defaultIcon', options.markerIcon);
        });
        return this;
    };
    $.fn.googleMapAddMarker        = function (options) {
        var options = $.extend({
                                   id     : null,
                                   center : null,
                                   data   : null,
                                   onClick: null
                                   // icon seçeneği kaldırıldı, varsayılan ikon kullanılacak
                               }, options);
        if (!options.id || !options.center) {
            console.error('An ID and Coordinates Are Required For a Marker.');
            return this;
        }
        this.each(function () {
            var map    = $(this).data('map');
            // Varsayılan ikonu kullan
            var icon   = $(this).data('defaultIcon');
            var marker = new google.maps.Marker({
                                                    position: new google.maps.LatLng(options.center[0], options.center[1]),
                                                    map     : map,
                                                    data    : options.data,
                                                    icon    : {
                                                        url       : icon,
                                                        scaledSize: new google.maps.Size(32, 32),
                                                        origin    : new google.maps.Point(0, 0),
                                                        anchor    : new google.maps.Point(0, 0)
                                                    }
                                                });
            // Marker'a tıklama olayı ekleyin, varsa
            if (typeof options.onClick === 'function') {
                google.maps.event.addListener(marker, 'click', function () {
                    options.onClick(options);
                });
            }
            // Marker'ı sakla
            var markers = $(this).data('markers');
            markers.push(marker);
        });
        return this;
    };
    $.fn.googleMapAddMarkerCluster = function (options) {
        // Default options, now including gridSize
        var defaults = {
            gridSize : 50, // Default gridSize
            maxZoom  : 10, // Default maxZoom
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m' // Default imagePath
        };
        // Extend defaults with any options provided
        var settings = $.extend({}, defaults, options);
        this.each(function () {
            var map     = $(this).data('map');
            var markers = $(this).data('markers');
            // Use MarkerClusterer library to cluster markers with provided settings
            new MarkerClusterer(map, markers, {
                gridSize : settings.gridSize,
                maxZoom  : settings.maxZoom,
                imagePath: settings.imagePath
            });
        });
        return this; // For chainability
    };
    $.fn.googleMapLocationPicker   = function (options) {
        var settings = $.extend({
                                    // Default settings
                                    zoom              : 15,
                                    center            : [39, -98], // Single coordinate object for initial location
                                    mapType           : 'ROADMAP', // Map type setting
                                    debug             : false, // Debug mode setting, using correct JavaScript boolean notation
                                    language          : 'en', // Language setting, expecting Google Maps API format
                                    overviewMapControl: false, // Overview map control setting
                                    streetViewControl : false, // Street view control setting
                                    scrollwheel       : true, // Scrollwheel setting
                                    mapTypeControl    : false, // Map type control setting
                                    draggable         : true, // Draggable setting for the map
                                    onLocationSelected: null // Default callback function when a location is selected
                                }, options);
        return this.each(function () {
            var mapElement = this;
            var mapOptions = {
                center            : new google.maps.LatLng(settings.center[0], settings.center[1]),
                zoom              : settings.zoom,
                mapTypeId         : google.maps.MapTypeId[settings.mapType],
                streetViewControl : settings.streetViewControl,
                scrollwheel       : settings.scrollwheel,
                mapTypeControl    : settings.mapTypeControl,
                draggable         : settings.draggable,
                overviewMapControl: settings.overviewMapControl,
                language          : settings.language
            };
            var map        = new google.maps.Map(mapElement, mapOptions);
            var geocoder   = new google.maps.Geocoder();
            var marker     = new google.maps.Marker({
                                                        position : new google.maps.LatLng(settings.center[0], settings.center[1]),
                                                        map      : map,
                                                        draggable: true
                                                    });
            google.maps.event.addListener(marker, 'dragend', function () {
                geocoder.geocode({'location': marker.getPosition()}, function (results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            var locationDetails = parseLocationDetails(results[0]);
                            // Call the callback function
                            if (typeof settings.onLocationSelected === 'function') {
                                settings.onLocationSelected(locationDetails);
                            }
                        }
                        else {
                            if (settings.debug) {
                                console.log('No address found for the selected location.');
                            }
                        }
                    }
                    else {
                        if (settings.debug) {
                            console.log('Geocoder failed: ' + status);
                        }
                    }
                });
            });
        });
        function parseLocationDetails(locationResult) {
            var details = {};
            locationResult.address_components.forEach(function (component) {
                var componentType = component.types[0];
                switch (componentType) {
                    case 'street_number':
                        details.street_number = component.long_name;
                        break;
                    case 'route':
                        details.route = component.long_name;
                        break;
                    case 'locality':
                        details.city = component.long_name;
                        break;
                    case 'administrative_area_level_1':
                        details.state      = component.long_name;
                        details.state_code = component.short_name;
                        break;
                    case 'country':
                        details.country      = component.long_name;
                        details.country_code = component.short_name;
                        break;
                    case 'postal_code':
                        details.zip = component.long_name;
                        break;
                }
            });
            details.address   = locationResult.formatted_address;
            details.latitude  = locationResult.geometry.location.lat();
            details.longitude = locationResult.geometry.location.lng();
            return details;
        }
    };
}(jQuery));


