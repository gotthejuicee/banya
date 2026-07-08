// Генерація SEO-асетів: OG-картинка для месенджерів + фавіконки для видачі Google.
// Запуск: node scripts/make-seo-assets.mjs (sharp — devDependency, артефакти комітяться)
import sharp from 'sharp';
import { writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';

const pub = fileURLToPath(new URL('../public/', import.meta.url));

// 1) OG 1200×630 для Telegram / соцмереж — банер «ГОТОВЕ РІШЕННЯ ДЛЯ НЕЇ ТА НЬОГО»
await sharp(pub + 'images/banner-boxes.png')
    .resize(1200, 630, { fit: 'cover', position: 'centre' })
    .jpeg({ quality: 85, mozjpeg: true })
    .toFile(pub + 'images/og.jpg');
console.log('og.jpg: 1200x630');

// 2) Фавіконки з favicon.svg (бігунець на чорному скругленому квадраті)
const sizes = [
    [48, 'favicon-48.png'],
    [96, 'favicon-96.png'],
    [180, 'apple-touch-icon.png'],
    [192, 'favicon-192.png'],
    [512, 'favicon-512.png'],
];

for (const [size, name] of sizes) {
    await sharp(pub + 'favicon.svg', { density: Math.ceil((72 * size) / 128) })
        .resize(size, size)
        .png()
        .toFile(pub + name);
    console.log(`${name}: ${size}x${size}`);
}

// 3) favicon.ico — PNG-in-ICO контейнер (скелетон Laravel кладе ПОРОЖНІЙ ico,
//    через нього браузери/пошуковики показують сіру заглушку)
const icoPng = await sharp(pub + 'favicon.svg', { density: 27 })
    .resize(48, 48)
    .png()
    .toBuffer();

const icoHeader = Buffer.alloc(6);
icoHeader.writeUInt16LE(0, 0); // reserved
icoHeader.writeUInt16LE(1, 2); // type: icon
icoHeader.writeUInt16LE(1, 4); // кількість зображень

const icoEntry = Buffer.alloc(16);
icoEntry.writeUInt8(48, 0); // width
icoEntry.writeUInt8(48, 1); // height
icoEntry.writeUInt8(0, 2); // palette
icoEntry.writeUInt8(0, 3); // reserved
icoEntry.writeUInt16LE(1, 4); // planes
icoEntry.writeUInt16LE(32, 6); // bpp
icoEntry.writeUInt32LE(icoPng.length, 8); // розмір даних
icoEntry.writeUInt32LE(22, 12); // зсув даних (6 + 16)

writeFileSync(pub + 'favicon.ico', Buffer.concat([icoHeader, icoEntry, icoPng]));
console.log('favicon.ico: 48x48 (PNG-in-ICO)');

// 4) Веб-маніфест (іконки для Android/встановлення на екран)
writeFileSync(pub + 'site.webmanifest', JSON.stringify({
    name: 'IDI_V_BANYU__',
    short_name: 'IDI_V_BANYU__',
    icons: [
        { src: '/favicon-192.png', sizes: '192x192', type: 'image/png' },
        { src: '/favicon-512.png', sizes: '512x512', type: 'image/png' },
    ],
    theme_color: '#060608',
    background_color: '#060608',
    display: 'browser',
}, null, 2));
console.log('site.webmanifest');
