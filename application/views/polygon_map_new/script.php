<script>
    // var map = L.map('map').setView([51.505, -0.09], 13);
    var map = L.map('map', {
        // layers: [base],
        tap: false, // ref https://github.com/Leaflet/Leaflet/issues/7255
        center: new L.LatLng(51.505, -0.09),
        zoom: 15,
        fullscreenControl: true,
        fullscreenControlOptions: { // optional
            title: "Show me the fullscreen !",
            titleCancel: "Exit fullscreen mode"
        }
    });

    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // var polygon = L.polygon([
    //     [51.509, -0.08],
    //     [51.503, -0.06],
    //     [51.51, -0.047]
    // ]).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    map.locate({
        setView: true,
        maxZoom: 22
    });

    L.control.scale({
        imperial: true,
        metric: true,
        position: 'bottomright'
    }).addTo(map);
    // open street map
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    osm.addTo(map);

    // watercolor
    var CartoDB_DarkMatter = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    });
    CartoDB_DarkMatter.addTo(map);

    // google street
    googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });
    googleStreets.addTo(map);

    // google satelite
    googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });
    googleSat.addTo(map);


    var Stamen_Watercolor = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.{ext}', {
        attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        subdomains: 'abcd',
        minZoom: 1,
        maxZoom: 16,
        ext: 'jpg'
    });
    Stamen_Watercolor.addTo(map);

    var baseLayers = {
        "Satellite": googleSat,
        "Google Map": googleStreets,
        "Water Color": Stamen_Watercolor,
        "OpenStreetMap": osm,
    };


    // map.fullScreen();
    // map.addControl(new L.Control.Fullscreen());
    L.control.layers(baseLayers).addTo(map);

    L.Control.geocoder().addTo(map);

    var polygon = 0;

    function createAreaTooltip(layer) {

        if (layer.areaTooltip) {
            // alert('pp');
            return;
        }

        layer.areaTooltip = L.tooltip({
            permanent: true,
            direction: 'center',
            className: 'area-tooltip'
        });

        layer.on('remove', function (event) {
            layer.areaTooltip.remove();
            alert('hapus');
            polygon--;
            // alert('remove');
        });

        layer.on('add', function (event) {
            polygon++;
            alert('created');
            updateAreaTooltip(layer);
            layer.areaTooltip.addTo(map);
            // alert('add');
        });

        if (map.hasLayer(layer)) {
            updateAreaTooltip(layer);
            layer.areaTooltip.addTo(map);
            // alert('??');
        }
    }

    function updateAreaTooltip(layer) {

        var area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
        var readableArea = L.GeometryUtil.readableArea(area, true);
        var latlng = layer.getCenter();

        let text = layer.getLatLngs().toString();
        let textTanpaLatLng = text.replace(/LatLng\(/g, '');
        // console.log(textTanpaLatLng);

        let newString = textTanpaLatLng.replace(/\)/g, '|');
        // console.log(newString);

        // Memisahkan koordinat menjadi array dengan membagi string berdasarkan koma dan menghapus spasi
        const coordinatesArray = newString.split('|');

        jumlahPerulangan = coordinatesArray.length - 1;

        alert(jumlahPerulangan);

        layer.areaTooltip
            .setContent(readableArea)
            .setLatLng(latlng);
        // alert(layer.length);''
        // layer.bindPopup(
        //     newString
        // );
    }

    /**
     * SIMPLE EXAMPLE
     */


    // createAreaTooltip(polygon);

    /**
     * EXAMPLE WITH LEAFLET DRAW CONTROL
     */
    var drawnItems = L.featureGroup().addTo(map);

    map.addControl(new L.Control.Draw({
        edit: {
            featureGroup: drawnItems,
            poly: {
                allowIntersection: false
            }
        },
        draw: {
            marker: false,
            circle: false,
            circlemarker: false,
            rectangle: false,
            polyline: false,
            polygon: {
                allowIntersection: true,
                showArea: true
            }
        }
    }));

    map.on(L.Draw.Event.CREATED, function (event) {
        if (polygon == 0) {
            var layer = event.layer;
            // alert(layer.getLatLngs());



            if (layer instanceof L.Polygon) {
                createAreaTooltip(layer);
            }
            drawnItems.addLayer(layer);
        } else {
            alert('Polygon Telah dibuat, hapus atau ubah polygon sebelumnya!')
        }
    });

    map.on(L.Draw.Event.EDITED, function (event) {
        event.layers.getLayers().forEach(function (layer) {
            if (layer instanceof L.Polygon) {
                updateAreaTooltip(layer);
            }
            alert('updated');
        })
    });

    // Ambil tombol dan div konten dengan ID
    const reloadButton = document.getElementById('reloadButton');

    // Fungsi yang akan dijalankan saat tombol diklik
    function handleClick() {
        alert(polygon);
    }

    // Tambahkan event listener untuk mengaktifkan fungsi saat tombol diklik
    reloadButton.addEventListener('click', handleClick);

    document.getElementById('export').addEventListener('click', function () {
        if (!(polygon == 1)) {
            alert('Gambar dan buat polygon terlebih dahulu');
        } else {
            exportGeoJSON();
        }
    });

    function exportGeoJSON() {
        var geoJSONData = drawnItems.toGeoJSON();
        var geoJSONStr = JSON.stringify(geoJSONData, null, 2);

        var blob = new Blob([geoJSONStr], {
            type: 'application/json'
        });
        var url = URL.createObjectURL(blob);

        var a = document.createElement('a');
        a.href = url;
        a.download = 'map.geojson';
        a.click();
    }
</script>