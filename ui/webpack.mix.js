let mix = require('laravel-mix');
let path = require('path');
let directory = path.basename(path.resolve(__dirname));

// Get the relative path from root to this directory
const rootPath = path.resolve(__dirname, '../../..');
const relativePath = path.relative(rootPath, __dirname);

//path to folder with compiled assets
const source = relativePath;
const assets = source + '/resources/assets';
const publicPath = source + '/public';
const productFolder = 'public/vendor/polirium/core/' + directory;

mix.disableNotifications();

// SCSS files that need compilation
const scssFiles = [
    'polirium-core',
    'polirium-flags',
    'polirium-marketing',
    'polirium-payments',
    'polirium-props',
    'polirium-social',
    'polirium-themes',
    'polirium-vendors',
    'app',
];

// Plain CSS files that just need to be copied/minified
// Note: ALL CSS files have been moved to scss/polirium/ and imported in app.scss
// No plain CSS files remaining
const plainCssFiles = [];

const jsFiles = [
    'polirium',
    'theme',
    'app',
];

// Compile SCSS files
scssFiles.forEach(function (file) {
    mix.sass(assets + '/scss/' + file + '.scss', productFolder + '/css/' + file + '.min.css');
});

// Copy and optionally minify plain CSS files
plainCssFiles.forEach(function (file) {
    const sourceCss = publicPath + '/css/' + file + '.css';
    const targetCss = productFolder + '/css/' + file + '.min.css';

    // Copy CSS file (Laravel Mix will handle minification in production)
    mix.copy(sourceCss, targetCss);
});

// Compile JS files
jsFiles.forEach(function (file) {
    mix.js(assets + '/js/' + file + '.js', productFolder + '/js/' + file + '.min.js');
});

// Copy libs directory
mix.copyDirectory(assets + '/libs', productFolder + '/libs');

// Copy files after they are built - using then() to ensure proper order
mix.then(() => {
    const fs = require('fs');

    // Copy SCSS-compiled CSS files
    scssFiles.forEach(function (file) {
        const sourceFile = productFolder + '/css/' + file + '.min.css';
        const targetFile = publicPath + '/css/' + file + '.min.css';

        if (fs.existsSync(sourceFile)) {
            const targetDir = path.dirname(targetFile);
            if (!fs.existsSync(targetDir)) {
                fs.mkdirSync(targetDir, { recursive: true });
            }
            fs.copyFileSync(sourceFile, targetFile);
        }
    });

    // Copy plain CSS files
    plainCssFiles.forEach(function (file) {
        const sourceFile = publicPath + '/css/' + file + '.css';
        const targetFile = productFolder + '/css/' + file + '.min.css';
        const backupFile = publicPath + '/css/' + file + '.min.css';

        // Source -> Product folder
        if (fs.existsSync(sourceFile)) {
            const targetDir = path.dirname(targetFile);
            if (!fs.existsSync(targetDir)) {
                fs.mkdirSync(targetDir, { recursive: true });
            }
            fs.copyFileSync(sourceFile, targetFile);
        }

        // Product folder -> Backup (same folder as source)
        if (fs.existsSync(targetFile)) {
            const targetDir = path.dirname(backupFile);
            if (!fs.existsSync(targetDir)) {
                fs.mkdirSync(targetDir, { recursive: true });
            }
            fs.copyFileSync(targetFile, backupFile);
        }
    });

    // Copy JS files
    jsFiles.forEach(function (file) {
        const sourceFile = productFolder + '/js/' + file + '.min.js';
        const targetFile = publicPath + '/js/' + file + '.min.js';

        if (fs.existsSync(sourceFile)) {
            const targetDir = path.dirname(targetFile);
            if (!fs.existsSync(targetDir)) {
                fs.mkdirSync(targetDir, { recursive: true });
            }
            fs.copyFileSync(sourceFile, targetFile);
        }
    });
});

mix.copyDirectory(assets + '/libs', publicPath + '/libs');
