const fs = require('fs');
const path = require('path');

const distDir = path.join(__dirname, 'dist');
const publicDir = path.join(__dirname, 'public');
const viewsDir = path.join(__dirname, 'resources', 'views');

// Copy directories from dist to public
for (const dir of ['js', 'css', 'img', 'fonts']) {
  const src = path.join(distDir, dir);
  const dest = path.join(publicDir, dir);

  if (fs.existsSync(src)) {
    if (fs.existsSync(dest)) {
      fs.rmSync(dest, { recursive: true, force: true });
    }
    fs.cpSync(src, dest, { recursive: true });
    console.log(`Copied ${dir}/`);
  }
}

// Copy favicon from dist to public
const faviconSrc = path.join(distDir, 'favicon.ico');
const faviconDest = path.join(publicDir, 'favicon.ico');
if (fs.existsSync(faviconSrc)) {
  fs.copyFileSync(faviconSrc, faviconDest);
  console.log('Copied favicon.ico');
}

// Parse dist/index.html and generate app.blade.php
const indexHtml = fs.readFileSync(path.join(distDir, 'index.html'), 'utf8');

// Extract CSS links
const cssLinks = [];
const cssRegex = /<link\s+href="([^"]+\.css)"[^>]*rel="stylesheet"[^>]*>/g;
let match;
while ((match = cssRegex.exec(indexHtml)) !== null) {
  cssLinks.push(match[1]);
}

// Also check for preload CSS links
const cssPreloadRegex = /<link\s+href="([^"]+\.css)"[^>]*rel="preload"[^>]*as="style"[^>]*>/g;
while ((match = cssPreloadRegex.exec(indexHtml)) !== null) {
  if (!cssLinks.includes(match[1])) {
    cssLinks.push(match[1]);
  }
}

// Extract JS scripts
const jsScripts = [];
const jsRegex = /<script\s+src="([^"]+\.js)"[^>]*><\/script>/g;
while ((match = jsRegex.exec(indexHtml)) !== null) {
  jsScripts.push(match[1]);
}

// Also check for prefetch JS links
const jsPrefetchRegex = /<link\s+href="([^"]+\.js)"[^>]*rel="prefetch"[^>]*>/g;
while ((match = jsPrefetchRegex.exec(indexHtml)) !== null) {
  if (!jsScripts.includes(match[1])) {
    jsScripts.push(match[1]);
  }
}

// Generate Blade template
const cssLinkTags = cssLinks.map(href => `    <link href="${href}" rel="stylesheet">`).join('\n');
const jsScriptTags = jsScripts.map(src => `    <script src="${src}"></script>`).join('\n');

const bladeTemplate = `<!DOCTYPE html>
<html lang="uk">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="icon" href="/favicon.ico">
    <title>Каталог навчальних планів</title>
${cssLinkTags}
  </head>
  <body>
    <noscript>
      <strong>Потрібно увімкнути JavaScript для роботи додатку.</strong>
    </noscript>
    <div id="app"></div>
${jsScriptTags}
  </body>
</html>`;

fs.writeFileSync(path.join(viewsDir, 'app.blade.php'), bladeTemplate);
console.log('Updated resources/views/app.blade.php');

console.log('\nBuild files copied to public/ and Blade template updated.');