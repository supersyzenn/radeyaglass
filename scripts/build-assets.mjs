import { readFile, mkdir, writeFile, rm } from 'node:fs/promises';
import { createHash } from 'node:crypto';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = fileURLToPath(new URL('../', import.meta.url));
const directory = path.join(root, 'public/build');
const manifest = {};
const assets = await Promise.all(['resources/css/app.css', 'resources/css/storefront.css', 'resources/js/app.js'].map(async (source) => {
    const content = await readFile(path.join(root, source));
    const hash = createHash('sha256').update(content).digest('hex').slice(0, 12);
    return {source, content, file: `assets/${path.basename(source, path.extname(source))}-${hash}${path.extname(source)}`};
}));
await mkdir(directory, {recursive: true});
for (const asset of assets) {
    await mkdir(path.dirname(path.join(directory, asset.file)), {recursive: true});
    await writeFile(path.join(directory, asset.file), asset.content);
    manifest[asset.source] = {file: asset.file, src: asset.source, isEntry: true};
}
await writeFile(path.join(directory, 'manifest.json'), JSON.stringify(manifest, null, 2));
await rm(path.join(root, 'public/hot'), {force: true});
console.log(`Built ${assets.length} versioned assets → public/build/manifest.json`);
