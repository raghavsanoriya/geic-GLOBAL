const fs=require('node:fs');
const path=require('node:path');
const sharp=require(process.env.SHARP_MODULE || 'sharp');
const dir=__dirname;
const source=Buffer.from(fs.readFileSync(path.join(dir,'App-Icon.svg'),'utf8').replace(/width="[^"]+"/,'width="1024"').replace(/height="[^"]+"/,'height="1024"'));
async function icon(name,size,logoSize,background) {
 const logo=await sharp(source,{density:72}).resize(logoSize,logoSize,{fit:'contain'}).png().toBuffer();
 await sharp({create:{width:size,height:size,channels:4,background}}).composite([{input:logo,gravity:'centre'}]).png().toFile(path.join(dir,name));
}
(async()=>{
 await icon('brand-icon.png',1024,820,'#ffffff');
 await icon('brand-adaptive.png',1024,640,{r:255,g:255,b:255,alpha:0});
 await icon('brand-splash.png',512,410,{r:255,g:255,b:255,alpha:0});
 await icon('brand-favicon.png',96,80,'#ffffff');
 await sharp(path.join(dir,'brand-adaptive.png')).tint('#000000').png().toFile(path.join(dir,'brand-monochrome.png'));
 console.log('Created branded Expo icon assets from App-Icon.svg');
})().catch(error=>{console.error(error.message);process.exit(1)});
