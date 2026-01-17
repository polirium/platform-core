let mix = require('laravel-mix');
let path = require('path');
let directory = path.basename(path.resolve(__dirname));

// Get the relative path from root to this directory
const rootPath = path.resolve(__dirname, '../../..');
const relativePath = path.relative(rootPath, __dirname);

// Path to folder with compiled assets
const source = relativePath;
const assets = source + '/resources/assets';
const publicPath = source + '/public';
const productFolder = 'public/vendor/polirium/core/' + directory;

mix.disableNotifications();

// SCSS files that need compilation
const scssFiles = [
    'media-manager',
];

// JS files
const jsFiles = [
    'media-manager',
];

// Compile SCSS files
scssFiles.forEach(function (file) {
    mix.sass(assets + '/scss/' + file + '.scss', productFolder + '/css/' + file + '.min.css');
});

// Compile JS files
jsFiles.forEach(function (file) {
    mix.js(assets + '/js/' + file + '.js', productFolder + '/js/' + file + '.min.js');
});

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
