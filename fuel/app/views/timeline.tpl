
			<header class="clearfix">
				<div class="float-left">
					<h2>{{$path|escape}}</h2>
					<p>{{$path_kana}}</p>
				</div>
{{if $user}}
				<nav class="float-right">
					<button class="btn btn-success mb-1" data-toggle="modal" data-target="#add-division">
						<i class="fa fa-plus"></i>
						自治体追加
					</button>
					<button class="btn btn-primary mb-1" data-toggle="modal" data-target="#edit-division">
						<i class="fa fa-edit"></i>
						自治体変更
					</button>
					<button class="btn btn-danger mb-1" data-toggle="modal" data-target="#delete-division">
						<i class="fa fa-trash"></i>
						自治体削除
					</button>
				</nav>
{{/if}}
			</header>
			<ul>
				<li><a href="{{$url_detail}}">所属自治体</a></li>
				<li><a href="{{$url_detail_timeline}}">自治体タイムライン</a></li>
				<li><a href="{{$url_belongto_timeline}}">所属自治体タイムライン</a></li>
			</ul>

<style>
	.leaflet-container {background: #fff;}
</style>
<script>
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

function load(map, url, success)
{
	$.getJSON(
		url,
		function(data) {
			// 区域の中心を算出
			for (var idx in data.features[0].geometry.coordinates) {
				var shape = data.features[0].geometry.coordinates[idx];
				for (var idx2 in shape[0]) {
					set_coord(map, shape[0][idx2]);
				}
			}

			var divisionLayer = L.geoJson(data, {
				pointToLayer: function (feature, latlng) {
					var s = geojson_style(feature.properties);
					if(feature.properties['_markerType']=='Icon') {
						var myIcon = L.icon(s);
						return L.marker(latlng, {icon: myIcon});
					}
					if(feature.properties['_markerType']=='DivIcon') {
						var myIcon = L.divIcon(s);
						return L.marker(latlng, {icon: myIcon});
					}
					if(feature.properties['_markerType']=='Circle') {
						return L.circle(latlng,feature.properties['_radius'],s);
					}
					if(feature.properties['_markerType']=='CircleMarker') {
						return L.circleMarker(latlng,s);
					}
				},
				style: function (feature) {
					if(!feature.properties['_markerType']) {
						var s = geojson_style(feature.properties);
						return s;
					}
				},
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
	); // $.getJSON
} // function load()

function create_map(id, shapes)
{
	$("#" + id).show();

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
</script>

			<div class="col-md-10 offset-md-1 pb-3">
				<section class="timeline">
{{foreach name=events from=$events item=event}}
					<article
						class="row editable {{if $event->birth}}birth{{/if}} {{if $event->live}}live{{/if}} {{if $event->death}}death{{/if}}"
						data-event-id="{{$event.event_id}}">
						<section class="col-sm-7">
							<header class="clearfix">
								<h3 class="float-left">{{$event.type|escape}}</h3>
								<time class="float-right" datetime="{{$event.date}}">{{$event.date|date_format2:'Y(Jk)-m-d'}}</time>
							</header>
							<ul>
{{foreach from=$event.divisions item=d}}
								<li>
									<a href="{{$d->url_detail|escape}}">
{{if $division.id == $d.id}}
										<b>{{$d.fullname|escape}}</b>,
{{else}}
										{{$d.fullname|escape}},
{{/if}}
										{{$d.division_result|escape}}
									</a>
								</li>
{{/foreach}}
							</ul>
						</section>
						<div class="map col-sm-5 mb-4" id="map-{{$event.event_id}}">
							<div class="loading">
								{{Asset::img('loading.gif')}}
							</div>
						</div>
						<script>
							$(function(){
								var shapes = [];
{{foreach from=$event.divisions item=d}}
{{if $d && $d.url_geoshape}}
								shapes.push("{{$d.url_geoshape}}");
{{/if}}
{{/foreach}}
								if (shapes.length) {
									$("#map-{{$event.event_id}}").show();
									create_map("map-{{$event.event_id}}", shapes);
								}
							});
						</script>
					</article>
{{foreachelse}}
					<p>no events</p>
{{/foreach}}
{{if $user}}
					<span class="add"><i class="fas fa-plus"></i> イベントを追加…</span>
{{/if}}
				</section>
			</div>

{{if $user}}

{{$components.add_division}}
{{$components.edit_division}}
{{$components.delete_division}}
{{$components.change_event}}

{{/if}}
