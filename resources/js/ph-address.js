// Cascading Philippine address dropdowns for <x-address-fields> (Region → Province → City/Municipality → Barangay).
// Data: public/data/ph-address (scripts/build-ph-address.mjs). Selects submit names, so stored values stay plain text.
const cache = new Map();
const getJson = (url) => {
    if (!cache.has(url)) {
        cache.set(url, fetch(url, { headers: { Accept: 'application/json' } }).then((r) => {
            if (!r.ok) throw new Error(`${r.status} ${url}`);
            return r.json();
        }));
    }
    return cache.get(url);
};

// "City of Makati", "Makati City" and "makati" all match each other.
const norm = (s) => String(s ?? '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
    .replace(/\(.*?\)/g, '').replace(/\bcity of\b|\bcity\b/g, '').replace(/[^a-z0-9]+/g, ' ').trim();

function fill(select, items, placeholder) {
    select.replaceChildren(new Option(placeholder, ''));
    for (const [value, label, code] of items) {
        const opt = new Option(label, value);
        if (code) opt.dataset.code = code;
        select.add(opt);
    }
    select.disabled = items.length === 0;
}

function reset(select, placeholder) {
    fill(select, [], placeholder);
}

// Selects the option whose name matches `wanted`; keeps an unknown saved value as its own option so it isn't lost.
function choose(select, wanted, { keepUnknown = false } = {}) {
    if (!wanted) return false;
    const match = [...select.options].find((o) => o.value && (o.value === wanted || norm(o.value) === norm(wanted)));
    if (match) {
        select.value = match.value;
        return true;
    }
    if (keepUnknown) {
        select.add(new Option(wanted, wanted), 1);
        select.value = wanted;
        select.disabled = false;
    }
    return false;
}

function toTextInputs(root) {
    root.querySelector('[data-level="region"]')?.closest('[class*="col-"]')?.remove();
    root.querySelectorAll('select[data-level]').forEach((select) => {
        const input = document.createElement('input');
        input.type = 'text';
        input.id = select.id;
        input.name = select.name;
        input.className = select.className.replace('form-select', 'form-control');
        input.required = select.required;
        Object.assign(input.dataset, select.dataset);
        input.maxLength = 100;
        input.value = select.dataset.value || '';
        select.replaceWith(input);
    });
}

async function init(root) {
    const base = root.dataset.source.replace(/\/$/, '');
    const el = (level) => root.querySelector(`select[data-level="${level}"]`);
    const region = el('region');
    const province = el('province');
    const city = el('city');
    const barangay = el('barangay');
    const initial = { province: province.dataset.value, city: city.dataset.value, barangay: barangay.dataset.value };

    let data;
    try {
        data = await getJson(`${base}/locations.json`);
    } catch (e) {
        toTextInputs(root);
        return;
    }

    const code = (select) => select.selectedOptions[0]?.dataset.code;

    const loadProvinces = () => {
        const r = region.value;
        fill(province, data.provinces.filter((p) => p[2] === r).map(([c, n]) => [n, n, c]), r ? 'Select province' : 'Select a region first');
        if (!r) province.disabled = true;
        reset(city, 'Select a province first');
        reset(barangay, 'Select a city / municipality first');
    };

    const loadCities = () => {
        const p = code(province);
        fill(city, p ? data.cities.filter((c) => c[2] === p).map(([c, n]) => [n, n, c]) : [], p ? 'Select city / municipality' : 'Select a province first');
        reset(barangay, 'Select a city / municipality first');
    };

    const loadBarangays = async () => {
        const c = code(city);
        if (!c) {
            reset(barangay, 'Select a city / municipality first');
            return;
        }
        reset(barangay, 'Loading barangays…');
        try {
            const byCity = await getJson(`${base}/barangays/${code(province)}.json`);
            if (code(city) !== c) return; // the city changed while loading
            fill(barangay, (byCity[c] || []).map((n) => [n, n]), 'Select barangay');
        } catch (e) {
            reset(barangay, 'Could not load barangays — please retry');
        }
    };

    fill(region, data.regions.map(([c, label]) => [c, label]), 'Select region');
    region.addEventListener('change', loadProvinces);
    province.addEventListener('change', loadCities);
    city.addEventListener('change', loadBarangays);

    // Re-select a saved / old() address: the region is derived from the province.
    const savedProvince = initial.province && data.provinces.find((p) => norm(p[1]) === norm(initial.province));
    if (savedProvince) {
        region.value = savedProvince[2];
        loadProvinces();
        choose(province, savedProvince[1]);
        loadCities();
        if (choose(city, initial.city, { keepUnknown: true }) && code(city)) {
            await loadBarangays();
        }
        choose(barangay, initial.barangay, { keepUnknown: true });
    } else if (initial.province || initial.city) {
        // Free-text address from before the dropdowns: keep what was typed until the user picks a region.
        region.required = false;
        region.dataset.optional = '';
        province.disabled = false;
        choose(province, initial.province, { keepUnknown: true });
        choose(city, initial.city, { keepUnknown: true });
        choose(barangay, initial.barangay, { keepUnknown: true });
    } else {
        loadProvinces();
    }
}

document.querySelectorAll('[data-ph-address]').forEach((root) => { init(root); });
