// Чоловічі коробки: переносимо альфа-маску з дизайнерських жіночих вирізок
// (той самий бокс, та сама студія і ракурс — силуети майже ідентичні).
import sharp from 'sharp';

const src = 'C:/Users/user/Desktop/фотки-баня/';
const prod = 'C:/Users/user/Desktop/zmova2026/banya/public/images/products/';
const scratch = 'C:/Users/user/AppData/Local/Temp/claude/C--Users-user-Desktop-zmova2026/7aed000e-6020-4568-b461-1fae209bc6e0/scratchpad/';

const pairs = [
    { out: 'box-male-light', photo: 7, maskFrom: 'box-female-light' },
    { out: 'box-male-dark', photo: 8, maskFrom: 'box-female-dark' },
];

const previews = [];
for (const { out, photo, maskFrom } of pairs) {
    const maskMeta = await sharp(prod + maskFrom + '.png').metadata();

    // маска: альфа жіночої вирізки; м'яку тінь відкидаємо (під нею в
    // чоловічому фото білий фон), край трохи розмиваємо
    const mask = await sharp(prod + maskFrom + '.png')
        .extractChannel(3)
        .threshold(190)
        .blur(0.7)
        .toBuffer();

    // чоловіче фото: трим білого і підгонка ТОЧНО під розмір маски
    const maleRgb = await sharp(src + photo + '.jpg')
        .trim({ background: '#ffffff', threshold: 12 })
        .resize(maskMeta.width, maskMeta.height, { fit: 'fill' })
        .removeAlpha()
        .toBuffer();

    const png = await sharp(maleRgb)
        .joinChannel(mask)
        .png()
        .toBuffer();

    await sharp(png).png({ compressionLevel: 9 }).toFile(scratch + out + '.png');
    previews.push({ out, buf: await sharp(png).resize(330, 330, { fit: 'inside' }).png().toBuffer() });
    console.log(`${out}: маска з ${maskFrom} → фото №${photo} (${maskMeta.width}x${maskMeta.height})`);
}

// прев'ю на чорному
const cell = 340, lh = 24;
const comp = [];
previews.forEach((t, i) => {
    comp.push({ input: t.buf, left: i * (cell + 8) + 12, top: lh + 8 });
    comp.push({ input: Buffer.from(`<svg width="${cell}" height="${lh}"><text x="6" y="17" font-family="sans-serif" font-size="14" fill="#d6ff41" font-weight="bold">${t.out}</text></svg>`), left: i * (cell + 8) + 8, top: 4 });
});
await sharp({ create: { width: 2 * (cell + 8) + 8, height: cell + lh + 16, channels: 3, background: '#060608' } })
    .composite(comp).png().toFile(scratch + 'male-mask-transfer.png');
console.log('preview saved');
