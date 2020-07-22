var iconstyle = 
    new ol.style.Style({
        image: new ol.style.Icon(({
            src: 'Aiga_immigration_inv.png',    
            scale: 0.04
        }))
    });

var border = new ol.layer.Vector({
    source: new ol.source.Vector({
        url: 'border.osm',
        format: new ol.format.OSMXML()
    }),
    style: function(feature, resolution) {
        return iconstyle;
    }
});

var raster = new ol.layer.Tile({
    source: new ol.source.OSM({
        attributions: [
            '© <a href="https://www.openstreetmap.org/copyright" target="new">OpenStreetMap</a> contributors'
        ]
    })
});

c = Cookies.get();
czoom = c.zoom == undefined ? 3 : c.zoom;
console.log(c.lon);
if(c.lon == undefined || c.lat == undefined) {
    console.log('und');
    clonlat = [0, 0];
} else {
    clonlat = [parseFloat(c.lon), parseFloat(c.lat)];
}

var map = new ol.Map({
    target: document.getElementById('mainmap'),
    layers: [raster, border],
    view: new ol.View({
        center: ol.proj.fromLonLat(clonlat, 'EPSG:3857'),
        zoom: czoom
    })
});

function save_coord2cookie(evt) {
    lonlat = ol.proj.toLonLat(map.getView().getCenter(), 'EPSG:3857');
    Cookies.set('lon',lonlat[0]);
    Cookies.set('lat',lonlat[1]);
    Cookies.set('zoom',map.getView().getZoom());
}

map.on('moveend', save_coord2cookie);

var element = document.getElementById('popup');
var editor = $('.editor').get(0);
var popup = new ol.Overlay({
    element: element,
    positioning: 'bottom-center',
    stopEvent: false,
    offset: [0, -10]
});
map.addOverlay(popup);

map.on('click', function(evt) {
    var feature = map.forEachFeatureAtPixel(evt.pixel,
        function(feature) {
            return feature;
        });
    if (feature) {
        var coordinates = feature.getGeometry().getCoordinates();
        popup.setPosition(coordinates);
        id = feature.getId();
        kv = feature.getProperties();
        tags = "";
        for(let key of Object.keys(kv)) {
            if(key != 'geometry' && key != 'barrier') {
                tags += key + " = " + kv[key] + "<br>";
            }
        }
        if(feature.get('name') == null) {
            title = id;
        } else {
            title = feature.get('name');
        }
        description = `
<h1>${title}</h1>
${tags}
<iframe class="comments" src="comment.php?border=${id}"></iframe>
<br><a target='_blank' href='https://www.openstreetmap.org/edit?node=${id}'>Edit(OSM)</a>,
<a target='_blank' href='https://www.openstreetmap.org/node/${id}'>View(OSM)</a>
<form class="comment-form">
<div>Your comment</div>
<div><textarea class="comment-text" placeholder="Add your comment"></textarea></div>
<input class="postbutton" type="button" value="POST"></input>
</form>
`

        $(element).popover({
            placement: 'top',
            html: true,
            content: description
        });
        $(element).data('bs.popover').options.content = '<strong>↓</strong>';
        $(element).popover('show');
        $(editor).html(description);
        $('.postbutton').off('click');
        $('.postbutton').on('click', () => {
            text = $('.comment-text').val();
            $.post('comment.php', `border=${id}&comment=${text}`
            ).done((data, textStatus, jqXHR) => {
                // reload comment area
                var src = $(".comments").attr("src");
                $(".comments").attr("src","");
                $(".comments").attr("src",src);
            }).fail((jqXHR, textStatus, errorThrown) => {
            });
        });
        $(editor).css('display', 'block');

    } else {
        $(element).popover('destroy');
        $(editor).css('display', 'none');
    }
});

// change mouse cursor when over marker
map.on('pointermove', function(e) {
    if (e.dragging) {
        $(element).popover('destroy');
        return;
    }
    var pixel = map.getEventPixel(e.originalEvent);
    var hit = map.hasFeatureAtPixel(pixel);
    map.getTarget().style.cursor = hit ? 'pointer' : '';
});
