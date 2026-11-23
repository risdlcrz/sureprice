// Builds the Philippine address data used by the cascading address dropdowns
// (resources/js/ph-address.js) from the PSA's PSGC lists, as published by https://psgc.gitlab.io/api.
//
//   node scripts/build-ph-address.mjs            # downloads the lists
//   node scripts/build-ph-address.mjs <dir>      # or reads regions/provinces/cities-municipalities/barangays .json from <dir>
//
// Output (public/data/ph-address):
//   locations.json             { regions: [[code, label]], provinces: [[code, name, regionCode]], cities: [[code, name, provinceCode]] }
//   barangays/<province>.json  { <cityCode>: [barangay names] }
// Cities with no province (Metro Manila, Isabela City, Cotabato City) get a stand-in "province" so every
// region still reads Region → Province → City/Municipality → Barangay.
import fs from 'node:fs';
import path from 'node:path';

const LISTS = ['regions', 'provinces', 'cities-municipalities', 'barangays'];
const src = process.argv[2];
const load = async (name) => src
    ? JSON.parse(fs.readFileSync(path.join(src, `${name}.json`), 'utf8'))
    : (await fetch(`https://psgc.gitlab.io/api/${name}.json`)).json();
const [regions, provinces, cities, barangays] = await Promise.all(LISTS.map(load));

const NCR = '130000000';
const byName = (a, b) => a.localeCompare(b, 'en', { numeric: true, sensitivity: 'base' });
const regionLabel = (r) => (r.regionName.startsWith('Region') ? `${r.regionName} – ${r.name}` : `${r.regionName} (${r.name})`)
    .replace('MIMAROPA Region (MIMAROPA Region)', 'MIMAROPA Region');

const outProvinces = provinces.map((p) => [p.code, p.name, p.regionCode]);
const provinceOf = new Map();
for (const c of cities) {
    if (c.provinceCode) { provinceOf.set(c.code, c.provinceCode); continue; }
    const code = c.regionCode === NCR ? `${NCR}-MM` : `${c.code}-IC`;
    if (!outProvinces.some(([pc]) => pc === code)) {
        outProvinces.push([code, c.regionCode === NCR ? 'Metro Manila' : c.name, c.regionCode]);
    }
    provinceOf.set(c.code, code);
}

const order = (r) => ({ [NCR]: '0', '140000000': '1', '170000000': '2045' })[r.code] ?? `2${r.code}`; // NCR, CAR, then I…XIII (MIMAROPA after IV-A)
const out = {
    regions: [...regions].sort((a, b) => order(a).localeCompare(order(b))).map((r) => [r.code, regionLabel(r)]),
    provinces: outProvinces.sort((a, b) => byName(a[1], b[1])),
    cities: cities.map((c) => [c.code, c.name, provinceOf.get(c.code)]).sort((a, b) => byName(a[1], b[1])),
};

const perProvince = {};
for (const b of barangays) {
    const city = b.cityCode || b.municipalityCode;
    const prov = provinceOf.get(city);
    if (!prov) continue;
    ((perProvince[prov] ??= {})[city] ??= []).push(b.name);
}

const dir = path.resolve('public/data/ph-address');
fs.rmSync(dir, { recursive: true, force: true });
fs.mkdirSync(path.join(dir, 'barangays'), { recursive: true });
fs.writeFileSync(path.join(dir, 'locations.json'), JSON.stringify(out));
for (const [prov, byCity] of Object.entries(perProvince)) {
    for (const list of Object.values(byCity)) list.sort(byName);
    fs.writeFileSync(path.join(dir, 'barangays', `${prov}.json`), JSON.stringify(byCity));
}
console.log(`${out.regions.length} regions, ${out.provinces.length} provinces, ${out.cities.length} cities/municipalities, ${barangays.length} barangays`);
