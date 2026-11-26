<div id="map" style="height: 400px;"></div>

@php
    $isAr = app()->getLocale() === 'ar';
    $mapText = [
        'zoomIn' => $isAr ? 'تكبير' : 'Zoom in',
        'zoomOut' => $isAr ? 'تصغير' : 'Zoom out',
        'perMonth' => $isAr ? 'شهرياً' : '/ month',
        'view' => $isAr ? 'عرض التفاصيل' : 'View details',
        'office' => app(\App\Settings\GeneralSettings::class)->site_name ?: $brand->name(),
    ];
@endphp

<script type="module">
    let L;
    try {
        L = await window.loadLeaflet();
    } catch (error) {
        // Map chunk unavailable (network, or a page cached across a deploy):
        // hide the empty box instead of leaving a blank 400px gap.
        document.getElementById('map')?.setAttribute('hidden', '');
        throw error;
    }
    const properties = @json($properties);
    const currencySymbols = @json(collect(app(\App\Services\CurrencyService::class)->all())->map(fn ($c) => $c['symbol']));
    const t = @json($mapText);
    const numberLocale = 'en-US'; // prices use Western digits site-wide
    const detailUrl = @json(url('/properties')) + '/';

    // Centered on Al-Mukalla, Hadhramaut. The default zoom control is
    // replaced so its button titles/labels follow the page language.
    const map = L.map('map', { doubleClickZoom: false, zoomControl: false }).setView([14.5385, 49.1235], 12);
    L.control.zoom({ zoomInTitle: t.zoomIn, zoomOutTitle: t.zoomOut }).addTo(map);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Popups are built as DOM nodes with textContent, never as an HTML
    // string: titles come from user-submitted listings, and Leaflet renders
    // a string popup as raw HTML.
    const el = (tag, text, className) => {
        const node = document.createElement(tag);
        if (text !== undefined) node.textContent = text;
        if (className) node.className = className;
        return node;
    };

    const office = el('strong', t.office);
    L.marker([14.5385, 49.1235]).addTo(map).bindPopup(office);

    properties.forEach((loc) => {
        const popup = el('div', undefined, 'text-sm');
        popup.dir = 'auto';
        popup.append(el('strong', loc.title, 'block mb-1'));

        if (loc.price !== null && loc.price !== undefined && !Number.isNaN(Number(loc.price))) {
            const symbol = currencySymbols[loc.currency] || loc.currency || '';
            const price = `${Number(loc.price).toLocaleString(numberLocale)} ${symbol}`.trim();
            popup.append(el('div', loc.rent ? `${price} ${t.perMonth}` : price, 'mb-1'));
        }

        const link = el('a', t.view, 'font-bold underline');
        link.href = detailUrl + encodeURIComponent(loc.id);
        popup.append(link);

        L.marker([loc.latitude, loc.longitude]).addTo(map).bindPopup(popup);
    });
</script>
