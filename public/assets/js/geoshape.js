
"use strict";

function geojson_style(prop) {
  var s = {};
  for(var name in prop) {
    if(name.match(/^_/) && !name.match(/_markerType/)){
      if( prop['_markerType']=='Circle' && name =='_radius'){continue;}
      s[name.substr(1)]=prop[name];
    }
  }
  return s;
}

function popup_properties(prop) {
  var s = ''
  for(var name in prop) {
    if(!name.match(/^_/)){
      s += name + "：" + prop[name] + "<br />";
    }
  }
  return s;
}

function set_coord(map, coord)
{
	var lng = coord[0];
	var lat = coord[1];

	if (map.lng_min > lng) {
		map.lng_min = lng;
	}
	if (lng > map.lng_max) {
		map.lng_max = lng;
	}
	if (map.lat_min > lat) {
		map.lat_min = lat;
	}
	if (lat > map.lat_max) {
		map.lat_max = lat;
	}
} // function set_coord()

function set_map_center(map)
{
	var lng_center = (map.lng_max + map.lng_min) / 2;
	var lat_center = (map.lat_max + map.lat_min) / 2;
	var width = map.lng_max - map.lng_min;
	var height = map.lat_max - map.lat_min;
	var size = (width > height ? width : height);

	var zoom = Math.floor( 10 + 0.15 / size );

	map.setView(
		[lat_center, lng_center], // center
		zoom // zoom
	); // map.setView()
} // function set_map_center()

function load(map, shape)
{
	$.ajax(
		shape.url,
		{
			type: "post",
			data: {
				method: "post",
			},
			dataType: "json",
		}
	)
	.done(
		function(data) {
			// 区域の中心を算出
			for (var idx in data.features[0].geometry.coordinates) {
				var polygon = data.features[0].geometry.coordinates[idx];
				for (var idx2 in polygon[0]) {
					set_coord(map, polygon[0][idx2]);
				}
			}

			var style = {};
			if (shape.split) {
				style.color = "#ff0000";
			}
			var divisionLayer = L.geoJson(data, {
				style: style,
				onEachFeature: function (feature, layer) {
					var name = feature.properties.N03_001;
					if (feature.properties.N03_002) {
						name += feature.properties.N03_002;
					}
					if (feature.properties.N03_003) {
						name += feature.properties.N03_003;
					}
					if (feature.properties.N03_004) {
						name += feature.properties.N03_004;
					}
				  layer.bindPopup(popup_properties({'名称': name}));
				}
			}); // L.geoJson

			set_map_center(map);
			divisionLayer.addTo(map);
			++map.spapes_loaded;
			if (map.spapes_loaded == map.shapes_count) {
				map.$loading_pane.remove();
			}
		}
	);
} // function load()

function create_map(id, shapes)
{
	var map = L.map(id);
	map.lng_min = 180;
	map.lat_min = 90;
	map.lng_max = -180;
	map.lat_max = -90;
	map.$loading_pane = $("#" + id + " .loading");
	map.shapes_count = shapes.length;
	map.spapes_loaded = 0;

	for (var idx in shapes) {
		load(map, shapes[idx]);
	}

	// 背景地図設定
	var std = L.tileLayer(
		'https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png',
		{
			attribution: "<a href='https://maps.gsi.go.jp/development/ichiran.html' target='_blank'>地理院タイル（標準地図）</a>",
			maxNativeZoom: 18,
			maxZoom: 18,
			opacity: 0.5
		}
	).addTo(map);

	L.control.scale({imperial: false}).addTo(map);
} // function create_map()
