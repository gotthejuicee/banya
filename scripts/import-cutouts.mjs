// Імпорт вирізаних коробок з Figma: трим прозорих полів → до 900px → webp+png.
// Використання: node scripts/import-cutouts.mjs <вхідний.png> <box-female-dark>
import sharp from 'sharp';

const [src, name] = process.argv.slice(2);
if (!src || !name) {
    console.error('usage: node scripts/import-cutouts.mjs <file.png> <box-name>');
    process.exit(1);
}

const outDir = 'C:/Users/user/Desktop/zmova2026/banya/public/images/products/';

const png = await sharp(src)
    .trim({ threshold: 4 }) // обережно: не зрізати мʼяку тінь
    .resize(900, 900, { fit: 'inside', withoutEnlargement: true })
    .png()
    .toBuffer();

await sharp(png).png({ compressionLevel: 9 }).toFile(outDir + name + '.png');
await sharp(png).webp({ quality: 88, alphaQuality: 90 }).toFile(outDir + name + '.webp');
const m = await sharp(png).metadata();
console.log(`${name}: ${m.width}x${m.height}, alpha=${m.hasAlpha}`);
