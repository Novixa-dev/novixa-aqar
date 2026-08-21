<div id="map" style="height: 400px;"></div>

<script type="module">
    let properties = @json($properties);
    let currencySymbols = @json(collect(app(\App\Services\CurrencyService::class)->all())->map(fn ($c) => $c['symbol']));
    // Centered on Al-Mukalla, Hadhramaut.
    var map = L.map('map', {
        doubleClickZoom: false
    }).setView([14.5385, 49.1235], 12);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([14.5385, 49.1235]).addTo(map)
        .bindPopup("{{ app(\App\Settings\GeneralSettings::class)->site_name }}");

    properties.forEach(function(loc) {
        let symbol = currencySymbols[loc.currency] || loc.currency;
        L.marker([loc.latitude, loc.longitude]).addTo(map)
            .bindPopup(`<br> ${loc.title} </b><br> ${Number(loc.price).toLocaleString()} ${symbol}`);
    });
</script>
